# Health Checks

How the app reports the health of its moving parts — database, cache/Redis, queue workers (Horizon), the scheduler, and the Reverb websocket server — via [`spatie/laravel-health`](https://spatie.be/docs/laravel-health).

## The model

Two endpoints, deliberately separate — don't conflate them:

| Endpoint | Purpose | Who calls it | Failure behavior |
|----------|---------|--------------|------------------|
| **`/up`** | Lightweight liveness — the framework booted | Coolify container health check | Marks the **whole container** unhealthy (→ restart) |
| **`/health`** | Deep check of each dependency (DB, Redis, Horizon, queue, scheduler, Reverb) | External uptime monitor / on-call probe | Returns **503** with JSON; does **not** restart the container |

Keep `/up` as the container gate. **Never** point the container health check at `/health`: in a single multi-process container, one flaky dependency (say Reverb) would mark the container unhealthy and restart it — killing the web app and every other process with it. Process liveness is already supervisord's job (`autorestart=true`); `/health` is for **observability and alerting**, not for triggering restarts.

Results are computed on a schedule (every minute) and **cached** — `/health` serves the latest snapshot, so a request never blocks on running the checks. No database table or migration is needed (the cache result store is used precisely so multi-tenant and DB-less forks need no health migration).

## What's checked

Registered in `app/Providers/AppServiceProvider.php` → `configureHealthChecks()`:

| Check | What it verifies | Active when |
|-------|------------------|-------------|
| `UsedDiskSpaceCheck` | Disk below threshold | always |
| `DatabaseCheck` | A connection can be made | a database is configured |
| `RedisCheck` | Redis responds | Redis backs cache, queue, or sessions |
| `HorizonCheck` | Horizon master supervisor is running | Horizon is installed |
| `QueueCheck` | Jobs are actually being processed (heartbeat) | queue driver isn't `sync` |
| `ScheduleCheck` | The scheduler is firing (heartbeat) | always |
| `ReverbCheck` | Reverb is accepting socket connections | `BROADCAST_CONNECTION=reverb` |

`ReverbCheck` (`app/Health/Checks/ReverbCheck.php`) is a small custom check — spatie ships none for Reverb. It TCP-connects to the address Reverb **binds** to (`reverb.servers.reverb`, default `0.0.0.0:8080`, probed over loopback at `127.0.0.1:8080`) — the process's own listener, not the client/publish endpoint (`REVERB_HOST`), so it tests Reverb directly and can't be fooled by a connect that lands on nginx.

## Enable only the checks a project needs

> **This is the important part for forks.** Not every project runs every service — a DB-less marketing site has no queue or Reverb; a simple app may not use Redis. **Only the checks that make sense for the project should be active.**

Two mechanisms, in order of preference:

1. **Automatic gating (default).** Every optional check is registered with `->if(...)` keyed on whether the fork *actually uses* that dependency (Redis only if Redis backs cache/queue/session; Reverb only if `BROADCAST_CONNECTION=reverb`; etc.). A check that doesn't apply is **skipped, not failed** — `config/health.php` sets `treat_skipped_as_failure => false`. So out of the box a fork reports green for exactly the services it runs, with no edits.

2. **Edit the list.** If a project genuinely doesn't want a check (or wants one the template omits), edit `configureHealthChecks()` in that fork's `AppServiceProvider`:

   ```php
   // A DB-less site that broadcasts nothing: drop the ones it can't satisfy.
   Health::checks([
       UsedDiskSpaceCheck::new(),
       ScheduleCheck::new(),
       // no DatabaseCheck / RedisCheck / HorizonCheck / QueueCheck / ReverbCheck
   ]);
   ```

   This is one of the few places a fork is **expected** to diverge from the template — the check list is project-specific, not managed core. Prefer leaving the `->if()` gating in place (it self-disables cleanly); remove a check outright only when it will never apply.

Do **not** leave a check active for a service the project doesn't run — a permanently-failing (or, if you ever flip `treat_skipped_as_failure`, noisy) check trains everyone to ignore `/health`.

## Scheduling

`bootstrap/app.php` → `withSchedule()` runs three commands every minute (the scheduler runs via `docker/config/supervisord.conf` → `schedule:work`):

- `health:check` — runs all checks, stores the snapshot for `/health`
- `health:queue-check-heartbeat` — feeds `QueueCheck` (dispatches a job whose completion proves the queue is draining)
- `health:schedule-check-heartbeat` — feeds `ScheduleCheck` (proves the scheduler itself fired)

If a fork removes `QueueCheck` or `ScheduleCheck`, drop the matching heartbeat command too.

## Reading results

- **HTTP:** `GET /health` → JSON of every check with status + meta. `200` when all-clear, `503` when any check failed (`config/health.php` → `json_results_failure_status`). Point an uptime monitor (or Sentry Uptime / Oh Dear) at it.
- **CLI:** `php artisan health:check` runs and prints them; `php artisan health:list` shows the last stored run.
- **Protect it (optional):** set `HEALTH_SECRET_TOKEN` and the endpoint requires that value in the `X-Secret-Token` header.

## Notifications vs. error tracking

Health notifications are **off by default** (`HEALTH_NOTIFICATIONS_ENABLED=false`) because the template already ships **Sentry** for exceptions and alerting — set `SENTRY_LARAVEL_DSN`. The two are complementary: Sentry for code errors, health notifications for infrastructure state.

To get a message when a check flips to failing, set `HEALTH_NOTIFICATIONS_ENABLED=true` and configure one or more channels. **Each channel fires only when its destination is set** (`config/health.php` builds the channel list from the env below), so an enabled-but-unconfigured channel never errors:

| Channel | Env var | Notes |
|---------|---------|-------|
| **Discord** | `HEALTH_DISCORD_WEBHOOK_URL` | Custom channel (`App\Health\DiscordHealthChannel`) — posts an embed per failed check straight to a Discord channel webhook. No extra package. |
| Mail | `HEALTH_TO_ADDRESS` | Uses the app mailer. |
| Slack | `HEALTH_SLACK_WEBHOOK_URL` | Requires `laravel/slack-notification-channel`. |

Discord is wired in because spatie ships only mail + slack — the `discord` channel is registered via `Notification::extend('discord', …)` in `AppServiceProvider`. Failures are throttled (one notification per hour per channel by default; `config/health.php` → `throttle_notifications_for_minutes`).

### Recovery pings (down → ok)

spatie only notifies on **failure** — it goes silent on recovery. `NotifyOnHealthRecovery` (listens to `CheckEndedEvent`) closes that gap: it posts a green **"✅ recovered"** Discord embed when a check returns to ok. So you get 🔴 when something breaks and ✅ when it comes back — gated on the same Discord webhook + enabled flags.

It is **debounced**: a check must be down for **≥ 2 consecutive runs** (~2 min, tracked as a per-check down streak in the cache) before it counts as a real outage, so a single transient/flapping failure never produces a spurious "recovered" ping — and you get exactly one recovery per outage. (`ScheduleCheck` also uses a 2-minute heartbeat window for the same reason — its 1-minute default false-fails on normal scheduler jitter.)

### Maintenance-mode pings (`artisan down` / `up`)

`NotifyOnMaintenanceMode` listens for Laravel's `MaintenanceModeEnabled` / `MaintenanceModeDisabled` events and posts to Discord when the app **enters** (amber, 🚧) or **leaves** (green, ✅) maintenance mode — handy for bracketing deploy/maintenance windows.

### What this can't catch — a full crash

These all run **inside** the app, so they cover dependency failures and deliberate maintenance mode — but a **fully-down app (crashed container, host offline) can't notify anyone**. For true "the whole app is unreachable" alerting, point an **external uptime monitor** (Better Stack, Oh Dear, Sentry Uptime, UptimeRobot, …) at **`/health`** (or `/up`). That's the one piece that must live outside the app; everything above complements it.
