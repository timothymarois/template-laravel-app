+++
title = "Health checks"
subtitle = "the /health snapshot, the checks it holds, the secret token and the 503 on failure"
status = "approved"
goals = false
intent = """
Health checks exist so that a monitor outside the container learns which of the services the application
depends on are working, from one address, without the request itself running anything. A failing
dependency should show as a 503 a monitor can alert on, and a service the deployment does not run should
never count as broken.
"""

[[infobox]]
group = "Identity"
rows = [
  { label = "Address", value = "/health", cite = "route" },
  { label = "Header", value = "X-Secret-Token", note = "once HEALTH_SECRET_TOKEN is set", cite = "token" },
  { label = "Command", value = "php artisan health:check", cite = "schedule" },
]

[[infobox]]
group = "Values"
rows = [
  { label = "Snapshot age", value = "1 minute", cite = "schedule" },
  { label = "Failure status", value = "503", cite = "status" },
  { label = "Reverb probe timeout", value = "2 seconds", cite = "reverb" },
]

[[infobox]]
group = "Rules"
rows = [
  { label = "Skipped check", value = "never a failure", cite = "skipped" },
  { label = "Live run", value = "only with ?fresh", cite = "fresh" },
]
+++

`/health` answers with the result of the last scheduled run of every check, as JSON, and runs no check for
the request itself.[^route][^fresh] The container gate `/up` is described on [Deployment](deployment.md),
what happens when a check fails on [Notifications](health/notifications.md), and the version address
`/release` on [Releases](releases.md).

## Snapshot

The scheduler runs `health:check` every minute and stores the results, so the endpoint serves a snapshot
at most a minute old.[^schedule] A request with `?fresh` runs every check before answering; without it,
none runs.[^fresh] The response carries `Cache-Control: no-store`, so a monitor never reads an earlier
answer from a cache.[^fresh]

A container the scheduler has not reached yet answers `200` with an empty `checkResults`, which proves
nothing about its dependencies; the release scripts wait for a stored result before they trust
it.[^cold] The answer of a local site whose scheduler had not run, copied from that site:[^cold]
```json
{"finishedAt":1788899354,"checkResults":[]}
```

## Checks

Seven checks are registered, and each optional one runs only where its service is configured, so a
deployment without Redis or Reverb is not reported as broken for lacking them.[^checks] A skipped check
is reported as skipped, never as failed.[^skipped]

| Check | Condition to run |
|---|---|
| Used disk space | always; warns above 70 % used and fails above 90 %[^disk] |
| Database | a database name is configured[^checks] |
| Redis | Redis backs the cache, the queue or sessions[^checks] |
| Horizon | the queue driver is `redis`[^checks] |
| Queue | the queue driver is not `sync`; a heartbeat job proves work is processed[^checks] |
| Schedule | always; the scheduler's heartbeat is accepted up to 2 minutes old[^checks] |
| Reverb | broadcasting uses `reverb`; the server must accept a connection within 2 seconds[^reverb] |

The Reverb check connects to the port the Reverb server itself binds, not the public address, so a
connection answered by the web server cannot pass for Reverb.[^reverb] Its failure reads
`Reverb is not accepting connections on 127.0.0.1:8080 (Connection refused)`.[^reverb]

## Failure

When any stored result is not ok, the response status is `503`; otherwise it is `200`.[^status] The body
still lists every check with its status, so a monitor can show which one failed.[^fresh]

## Secret token

Setting `HEALTH_SECRET_TOKEN` closes the endpoint: a request whose `X-Secret-Token` header does not match
is refused with `403` and `Incorrect secret token`.[^token] With the setting empty, the endpoint is open to
every caller.[^token]

[^route]: `routes/web.php` — the `health` route serves `HealthCheckJsonResultsController` behind
    `RequiresSecretToken`.
[^fresh]: `vendor/spatie/laravel-health/src/Http/Controllers/HealthCheckJsonResultsController.php` —
    `__invoke()` runs the checks only when the request has `fresh` or `always_send_fresh_results` is
    true, which `config/health.php` sets to `false`; it answers with `latestResults()` as JSON and
    `Cache-Control: no-store`.
[^schedule]: `bootstrap/app.php` — `withSchedule()` runs `health:check` every minute, on one server and
    without overlapping, with `health:queue-check-heartbeat` and `health:schedule-check-heartbeat`.
[^cold]: `scripts/publish-production-release` — the verification loop's `health_checked` test and its
    comment count a `200` with no stored `checkResults` as not yet healthy; the sample is the answer of
    a local site before its first `health:check`.
[^checks]: `app/Providers/AppServiceProvider.php` — `configureHealthChecks()` registers the seven checks
    with their `->if()` gates and `heartbeatMaxAgeInMinutes(2)` on the schedule check.
[^skipped]: `config/health.php` — `treat_skipped_as_failure` is `false`.
[^disk]: `vendor/spatie/laravel-health/src/Checks/Checks/UsedDiskSpaceCheck.php` — `$warningThreshold` is
    70 and `$errorThreshold` 90, which `configureHealthChecks()` leaves unchanged.
[^reverb]: `app/Health/Checks/ReverbCheck.php` — `run()` opens a socket to the `reverb.servers.reverb`
    host and port, probing `0.0.0.0` over `127.0.0.1`, with `$timeout` of 2 seconds, and fails with
    `Reverb is not accepting connections on {host}:{port} ({error})`.
[^status]: `config/health.php` — `json_results_failure_status` is `503`, which `__invoke()` returns when
    `containsFailingCheck()` finds a stored status outside the ok statuses.
[^token]: `vendor/spatie/laravel-health/src/Http/Middleware/RequiresSecretToken.php` — `handle()` aborts
    with `403` and `Incorrect secret token` when `health.secret_token` is set and the header differs;
    `config/health.php` — `secret_token` reads `HEALTH_SECRET_TOKEN`.
