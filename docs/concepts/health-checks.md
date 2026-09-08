# Guide: Configure and read health checks

**When to use:** You're setting up liveness/dependency monitoring, tailoring the active checks for a fork, or wiring failure notifications. Built on [`spatie/laravel-health`](https://spatie.be/docs/laravel-health).
**Prerequisites:** The scheduler must be running (via `docker/config/supervisord.conf` → `schedule:work`) so results are computed every minute. No DB table or migration is needed — the cache result store is used.

## The two endpoints — keep them separate

| Endpoint | Purpose | Who calls it | Failure behavior |
|----------|---------|--------------|------------------|
| **`/up`** | Lightweight liveness — the framework booted | Coolify container health check | Marks the **whole container** unhealthy (→ restart) |
| **`/health`** | Deep check of each dependency (DB, Redis, Horizon, queue, scheduler, Reverb) | External uptime monitor / on-call probe | Returns **503** with JSON; does **not** restart the container |

Keep `/up` as the container gate. **Never** point the container health check at `/health`: in a single multi-process container, one flaky dependency (say Reverb) would mark the container unhealthy and restart it — killing the web app and every other process. Process liveness is already supervisord's job (`autorestart=true`); `/health` is for **observability and alerting**, not for triggering restarts.

Results are computed on a schedule (every minute) and **cached** — `/health` serves the latest snapshot, so a request never blocks on running the checks.

## What's checked

Registered in `app/Providers/AppServiceProvider.php` → `configureHealthChecks()`:

| Check | What it verifies | Active when |
|-------|------------------|-------------|
| `UsedDiskSpaceCheck` | Disk below threshold | always |
| `DatabaseCheck` | A connection can be made | a database is configured |
| `RedisCheck` | Redis responds | Redis backs cache, queue, or sessions |
| `HorizonCheck` | Horizon master supervisor is running | `queue.default` is `redis` |
| `QueueCheck` | Jobs are actually being processed (heartbeat) | queue driver isn't `sync` |
| `ScheduleCheck` | The scheduler is firing (heartbeat) | always |
| `ReverbCheck` | Reverb is accepting socket connections | `BROADCAST_CONNECTION=reverb` |

`ReverbCheck` (`app/Health/Checks/ReverbCheck.php`) is a small custom check — spatie ships none for Reverb. It TCP-connects to the address Reverb **binds** to (`reverb.servers.reverb`, default `0.0.0.0:8080`, probed over loopback at `127.0.0.1:8080`) — the process's own listener, not the client/publish endpoint (`REVERB_HOST`) — so it tests Reverb directly and can't be fooled by a connect that lands on nginx.

## Steps — enable only the checks a project needs

Not every project runs every service. Only the checks that make sense should be active. Two mechanisms, in order of preference:

1. **Automatic gating (default).** Every optional check is registered with `->if(...)` keyed on whether the fork actually uses that dependency. A check that doesn't apply is **skipped, not failed** — `config/health.php` sets `treat_skipped_as_failure => false`. Out of the box a fork reports green for exactly the services it runs, with no edits.

2. **Edit the list.** If a project genuinely doesn't want a check (or wants one the template omits), edit `configureHealthChecks()` in that fork's `AppServiceProvider`:

   ```php
   // A DB-less site that broadcasts nothing: drop the ones it can't satisfy.
   Health::checks([
       UsedDiskSpaceCheck::new(),
       ScheduleCheck::new(),
       // no DatabaseCheck / RedisCheck / HorizonCheck / QueueCheck / ReverbCheck
   ]);
   ```

   The check list is project-specific, not managed core. Prefer leaving `->if()` gating in place (it self-disables cleanly); remove a check outright only when it will never apply.

## Steps — scheduling

`bootstrap/app.php` → `withSchedule()` runs three commands every minute:

- `health:check` — runs all checks, stores the snapshot for `/health`
- `health:queue-check-heartbeat` — feeds `QueueCheck` (dispatches a job whose completion proves the queue is draining)
- `health:schedule-check-heartbeat` — feeds `ScheduleCheck` (proves the scheduler itself fired)

If a fork removes `QueueCheck` or `ScheduleCheck`, drop the matching heartbeat command too.

## Steps — notifications (optional)

Health notifications are **off by default** (`HEALTH_NOTIFICATIONS_ENABLED=false`) because the template ships **Sentry** for exceptions — set `SENTRY_LARAVEL_DSN`. Sentry is for code errors, health notifications for infrastructure state. To get a message when a check flips to failing, set `HEALTH_NOTIFICATIONS_ENABLED=true` and configure a channel. Each channel fires only when its destination is set, so an enabled-but-unconfigured channel never errors:

| Channel | Env var | Notes |
|---------|---------|-------|
| **Discord** | `HEALTH_DISCORD_WEBHOOK_URL` | Custom channel (`App\Health\DiscordHealthChannel`) — posts an embed per failed check. No extra package. |
| Mail | `HEALTH_TO_ADDRESS` | Uses the app mailer. |
| Slack | `HEALTH_SLACK_WEBHOOK_URL` | Requires `laravel/slack-notification-channel`. |

Discord is wired in because spatie ships only mail + slack — registered via `Notification::extend('discord', …)` in `AppServiceProvider`. Failures are throttled (one notification per hour per channel by default; `config/health.php` → `throttle_notifications_for_minutes`).

- **Recovery pings.** spatie only notifies on failure. `NotifyOnHealthRecovery` (listens to `CheckEndedEvent`) posts a green "recovered" Discord embed when a check returns to ok. **Debounced**: a check must be down for **≥ 2 consecutive runs** (~2 min, a per-check down streak in the cache) before it counts as a real outage, so a single transient failure never produces a spurious recovery ping. (`ScheduleCheck` also uses a 2-minute heartbeat window — its 1-minute default false-fails on normal scheduler jitter.)
- **The endpoint serves the scheduled snapshot.** `always_send_fresh_results` is `false` in
  `config/health.php`. The package ships it as `true`, and the `/health` controller reads it whether or
  not the Oh Dear endpoint is enabled — so leaving it true makes every request run every check inline,
  fire the `CheckEnded` events that drive the recovery debounce, and rewrite the shared result cache.
  On an unauthenticated, unthrottled endpoint that is a denial-of-service lever. `?fresh` still forces a
  live run.
- **`HEALTH_SECRET_TOKEN` is enforced by middleware on the route.** `RequiresSecretToken` has to be
  attached explicitly; the package only auto-wires its own Oh Dear route, which is disabled here. Set
  the env var and the endpoint requires `X-Secret-Token`; leave it unset and the endpoint is open.
- **Maintenance-mode pings.** `NotifyOnMaintenanceMode` listens for `MaintenanceModeEnabled` / `MaintenanceModeDisabled` and posts to Discord when the app enters (amber) or leaves (green) maintenance — handy for bracketing deploy windows.

## Verify

- **HTTP:** `GET /health` → JSON of every check with status + meta. `200` when all-clear, `503` when any check failed (`config/health.php` → `json_results_failure_status`). Point an uptime monitor at it.
- **CLI:** `php artisan health:check` runs and prints them; `php artisan health:list` shows the last stored run.
- **Protect it (optional):** set `HEALTH_SECRET_TOKEN` and the endpoint requires that value in the `X-Secret-Token` header.

## Pitfalls

- Never repoint the container health check from `/up` to `/health` — one flaky dependency would restart the whole multi-process container.
- Don't leave a check active for a service the project doesn't run — a permanently-failing (or noisy) check trains everyone to ignore `/health`.
- These checks run **inside** the app, so a fully-down app (crashed container, host offline) can't notify anyone. For true "app is unreachable" alerting, point an **external** uptime monitor (Better Stack, Oh Dear, Sentry Uptime, UptimeRobot) at `/health` (or `/up`). That's the one piece that must live outside the app.
