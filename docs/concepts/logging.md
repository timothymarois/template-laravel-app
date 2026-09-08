# Guide: Read and centralize logs

**When to use:** You need to read application logs locally, or you're deploying and need to ship container logs to a central, searchable store.
**Prerequisites:** For production, a Docker/Coolify deployment (this template deploys as a Dockerfile build pack). For centralizing, an S3-compatible bucket and a host you can run a collector on.

## The model

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

## Configure — centralize in production

You wire up **one** sink. Common choices: **Loki + Grafana** (self-hosted, cheapest storage on S3/Spaces, you operate it), **Axiom** (managed, zero infra, native Coolify Log Drain), or **Better Stack / Grafana Cloud** (managed, hosted UI). All separate apps by **labels** (e.g. `app="my-app"`), so one sink serves every project — never one stack per app.

The app side needs nothing beyond what ships: `LOG_CHANNEL=stderr` (set in Coolify) + JSON-by-default. Then pick a forwarding mechanism:

**Host collector — Grafana Alloy / Fluent Bit (recommended).** Run one collector container on the host, mounting the Docker log directory and socket. It auto-discovers every container, attaches labels, and pushes to your sink — capturing all output (nginx, php-fpm, workers), decoupled from any platform feature. Alloy is the current collector (replaces the deprecated Promtail). One collector per host serves every app.

**Coolify native Log Drain (fallback).** Under *Server → Log Drains* (Fluent Bit); enable per resource under its *Advanced* tab. Targets Axiom and New Relic natively, or a custom Fluent Bit destination. Caveat: Coolify currently ignores the log-drain definition for **Dockerfile build-pack** resources — and this template deploys as one. Until fixed, prefer the host collector.

### Recommended default — Coolify → Loki + Grafana

Everything below is **one-time host infrastructure**, shared by every app on the server — you do not repeat it per fork.

1. **Object storage (retention).** Create an S3-compatible bucket (e.g. a DigitalOcean Space) + access key/secret. Loki keeps chunks and index here cheaply.
2. **Loki** (single-binary, `-target=all`) as a Coolify *Docker Compose* resource, pointed at the bucket with retention enforced:

   ```yaml
   schema_config:
     configs:
       - { from: 2024-01-01, store: tsdb, object_store: s3, schema: v13, index: { prefix: index_, period: 24h } }
   common:
     storage:
       s3:
         endpoint: <region>.digitaloceanspaces.com   # region host only, no bucket
         bucketnames: <your-bucket>
         region: <region>
         access_key_id: ${LOKI_S3_KEY}
         secret_access_key: ${LOKI_S3_SECRET}
         s3forcepathstyle: false
   compactor: { working_directory: /loki/compactor, retention_enabled: true, delete_request_store: s3 }
   limits_config: { retention_period: 2160h }   # 90 days; compactor enforces it
   ```

3. **Grafana** as a Coolify resource behind its own domain (admin auth + SSL). Add a Loki datasource at `http://loki:3100`. This is the log UI.
4. **Grafana Alloy** (collector) — one container on the host, mounting the Docker socket. Auto-discovers every container, labels by app, pushes to Loki. **This is the piece that connects your apps** — no app redeploy:

   ```hcl
   discovery.docker "all" { host = "unix:///var/run/docker.sock" }
   loki.source.docker "all" {
     host = "unix:///var/run/docker.sock"
     targets = discovery.docker.all.targets
     forward_to = [loki.write.default.receiver]
     relabel_rules = loki.relabel.coolify.rules
   }
   loki.relabel "coolify" {
     rule { source_labels = ["__meta_docker_container_label_coolify_serviceName"], target_label = "app" }
   }
   loki.write "default" { endpoint { url = "http://loki:3100/loki/api/v1/push" } }
   ```

In Grafana, query `{app="<your-app>"}` to see each app's logs. One Loki + one Grafana + one bucket serve **all** apps on the host, separated by the `app` label.

### Multiple servers

There is always **one** Loki and **one** bucket. **Never** run a second Loki against the same bucket — single-binary instances don't coordinate; two would corrupt each other's index and their compactors would delete each other's chunks. Every additional server runs **only an Alloy agent** that pushes to the central Loki:

```
Primary server:  Loki + Grafana + Alloy   ← the only Loki, the only bucket
Other server(s): Alloy agent  ───────────▶ pushes to the central Loki
```

To add a server: (1) expose the central Loki to it — give the `loki` service a domain with **Basic Auth** (Loki has no auth of its own), or push to the primary's private IP `http://<private-ip>:3100` over a shared private network; (2) deploy an Alloy agent on the other server (collector only) pointed at that Loki URL, tagging logs with a `server` label so you can filter per host: `{app="my-app", server="server-b"}`. Apps on that server still just need `LOG_CHANNEL=stderr`.

## Background work

Supervisor runs Horizon and `schedule:work` as long-running processes and pipes both to
stdout/stderr (`docker/config/supervisord.conf`), so their output reaches the same sink as
web requests. **No host cron is involved** — `schedule:work` is the scheduler, and
`health:schedule-check-heartbeat` proves it is still ticking.

Failures need help to get there:

| Failure | Recorded natively in | Reaches the log sink because |
|---|---|---|
| Queued job | `failed_jobs` + Horizon's UI | `LogBackgroundFailures::jobFailed` logs `job.failed` with the job name, queue, attempts and uuid |
| Scheduled task | **nothing** | `LogBackgroundFailures::scheduledTaskFailed` logs `schedule.failed` with the task summary and expression |

Both are registered in `AppServiceProvider`. Query the sink on `event=job.failed` or
`event=schedule.failed`; under `LOG_CHANNEL=stderr` the context is one-line JSON, so each
key is a queryable field.

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
- Don't run a second single-binary Loki against a shared bucket — it corrupts the index.
- Run Loki + Grafana + Alloy as Coolify-managed resources (ideally one Compose stack) so the UI controls their lifecycle. A raw `docker run`/compose outside Coolify isn't tracked, and deleting it from the UI leaves the container running.
- Logs ≠ error tracking. Logging is for searchable, high-volume output; for grouped exceptions and alerting the template ships **Sentry** — set `SENTRY_LARAVEL_DSN`. Use both; they're complementary.

## Verify

- Local: `tail -f storage/logs/laravel-*.log` shows new lines as you exercise the app.
- Production: querying `{app="<your-app>"}` in Grafana returns the container's log stream with parsed JSON fields.
