# Logging

Where this application's logs go, what shape they arrive in, and how a central store is reached.
Standing that store up is a task of its own:
[`guides/centralized-logging.md`](../guides/centralized-logging.md).

## How it works

| Environment | `LOG_CHANNEL` | Where logs go | How you read them |
|-------------|---------------|---------------|-------------------|
| Local / dev | `daily` | `storage/logs/laravel-YYYY-MM-DD.log` | `tail -f storage/logs/laravel-*.log`, your IDE, or `php artisan pail` |
| Production (Docker/Coolify) | `stderr` | the container's stdout/stderr | a central log sink |

In production every process — php-fpm, nginx, Horizon, the scheduler, Inertia SSR — writes to **stdout/stderr** (see `docker/config/supervisord.conf`). The container is **ephemeral with no persistent disk**, so there are no log files to browse. The container emits a log stream; a collector ships it to one central store you search. There is no in-app log browser — choose **one** external sink and read logs there.

Structured logs are **on by default**: the `stderr` channel emits one-line JSON per record (`config/logging.php` → `stderr.formatter` defaults to `Monolog\Formatter\JsonFormatter`), so a collector ships level, message, context, and timestamp already parsed. To get human-readable lines instead (e.g. to eyeball the Coolify Logs tab):

```dotenv
LOG_STDERR_FORMATTER=          # empty = Monolog's default LineFormatter
```

Local dev is unaffected — it uses the `daily` file channel.

## Read logs locally

1. Tail the current day's file: `tail -f storage/logs/laravel-*.log`
2. Or stream live with Pail: `php artisan pail`

## Centralize in production

You wire up **one** sink, shared by every app on the host and separated by a label (e.g. `app="my-app"`)
— never one stack per app. Common choices: **Loki + Grafana** (self-hosted, cheapest storage on S3/DO
Spaces, you operate it), **Axiom** (managed, zero infra, native Coolify Log Drain), or **Better Stack /
Grafana Cloud** (managed, hosted UI).

The app side needs nothing beyond what ships: `LOG_CHANNEL=stderr` set in Coolify, JSON by default. Only
the forwarding mechanism is a decision:

**Host collector — Grafana Alloy / Fluent Bit (recommended).** One collector container per host, mounting
the Docker socket. It auto-discovers every container, attaches labels, and pushes to your sink, capturing
all output (nginx, php-fpm, workers) independently of any platform feature. Alloy replaces the deprecated
Promtail. The recommended default — Coolify → Loki + Grafana on DigitalOcean Spaces — is built step by
step in [`guides/centralized-logging.md`](../guides/centralized-logging.md).

**Coolify native Log Drain (fallback).** Under *Server → Log Drains* (Fluent Bit); enabled per resource
under its *Advanced* tab. Targets Axiom and New Relic natively, or a custom Fluent Bit destination.
Caveat: Coolify currently ignores the log-drain definition for **Dockerfile build-pack** resources — and
this template deploys as one. Until that is fixed, prefer the host collector.

## Background work

Supervisor runs Horizon and `schedule:work` as long-running processes and pipes both to
stdout/stderr (`docker/config/supervisord.conf`), so their output reaches the same sink as
web requests. **No host cron is involved** — `schedule:work` is the scheduler, and
`health:schedule-check-heartbeat` proves it is still ticking.

**Laravel already logs both failures.** `Queue\Worker` reports a failed job to the
exception handler, and `ScheduleRunCommand` does the same for a failed task, so the
exception and its stack trace reach the sink without any help. A failed job additionally
lands in `failed_jobs` and Horizon's UI.

What the framework does **not** give you is shape. Its entry is the message plus a trace
blob, so the job class, queue, attempt count and uuid exist only as text inside that trace
and cannot be filtered on. `App\Listeners\LogBackgroundFailures` adds one structured line
alongside it:

| Event key | Fields |
|---|---|
| `job.failed` | `job`, `connection`, `queue`, `attempts`, `job_uuid`, `exception`, `message` |
| `schedule.failed` | `task`, `expression`, `exception`, `message` |

Under `LOG_CHANNEL=stderr` those become one-line JSON fields, so the sink can answer "how
often did `App\Jobs\SendInvoice` fail this week". The cost is a second ERROR line per
failure — trace in one, metadata in the other. A fork that never queries by field can
delete the two `Event::listen` calls in `AppServiceProvider` and lose only the filtering.

**The timeout invariant:** a worker's `timeout` must stay **below** the connection's
`retry_after`, or the queue releases a job back while it is still running and it executes
twice. Here that is Horizon `timeout: 60` against `REDIS_QUEUE_RETRY_AFTER: 90`
(`config/horizon.php`, `config/queue.php`). Raise one and you must raise the other.
Horizon's `tries: 1` is Laravel's own default and deliberate — a retry of a non-idempotent
job is worse than a failure — so set `$tries`/`$backoff` per job class rather than globally.

Supervisor gives Horizon `stopwaitsecs=3600` with `stopasgroup`/`killasgroup`, so a deploy
lets an in-flight job finish instead of killing the worker mid-job.

## How it fails

- Don't point the container health check at anything but `/up`; logging is separate from health.
- Run the collector and sink as Coolify-managed resources so the UI controls their lifecycle. A raw `docker run` outside Coolify isn't tracked, and deleting it from the UI leaves the container running.
- Logs ≠ error tracking. Logging is for searchable, high-volume output; for grouped exceptions and alerting the template ships **Sentry** — set `SENTRY_LARAVEL_DSN`. Use both; they're complementary.

## Verify

- Local: `tail -f storage/logs/laravel-*.log` shows new lines as you exercise the app.
- Production: querying `{app="<your-app>"}` in Grafana returns the container's log stream with parsed JSON fields.
