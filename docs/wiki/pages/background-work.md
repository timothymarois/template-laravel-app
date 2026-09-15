+++
title = "Background work"
subtitle = "Horizon, the scheduler, the job timeout and how a failed job or task is logged"
status = "approved"
goals = false
intent = """
Background work exists so that queued jobs and scheduled commands run under supervision in production, a
slow job is never run twice, and a failure can be found in the logs by the name of the job or task.
"""

[[infobox]]
group = "Identity"
rows = [
  { label = "Dashboard", value = "/horizon", cite = "gate" },
  { label = "Setting", value = "HORIZON_ALLOWED_EMAILS", cite = "gate" },
  { label = "Scheduler", value = "php artisan schedule:work", cite = "processes" },
]

[[infobox]]
group = "Values"
rows = [
  { label = "Job timeout", value = "60 seconds", cite = "timeout" },
  { label = "Retry after", value = "90 seconds", cite = "timeout" },
  { label = "Tries", value = "1", cite = "horizon" },
  { label = "Drain on stop", value = "60 minutes", cite = "drain" },
]
+++

Horizon runs the queue workers and `schedule:work` runs the scheduler, both as supervised processes in
the container described on [Deployment](deployment.md); their output joins the log stream described on
[Logging](logging.md).[^processes]

## Horizon

The dashboard at `/horizon` opens only for a signed-in user whose email address is listed in
`HORIZON_ALLOWED_EMAILS`, separated by commas.[^gate] One supervisor works the `default` queue on the
`redis` connection, with up to 10 processes in production and 3 locally.[^horizon]

## Timeouts

A job is stopped after 60 seconds, and a job that has not been acknowledged after 90 seconds is handed
to another worker; the first number stays below the second, because a job that outlives the second is
run twice.[^timeout] A job is tried once, so a retry policy belongs on the job class that can bear
it.[^horizon] When the container stops, Horizon is given 60 minutes to finish the jobs in hand before it
is killed.[^drain]

## Scheduler

`schedule:work` runs the schedule every minute, and the template schedules the three health commands
described on [Health checks](health.md).[^schedule]

## Failures

A failed job, and a scheduled task that throws, are each logged as an error carrying the fields below, so
a log query can filter on the job or task by name.[^failures] Laravel reports the exception and its trace
to the log as well, so each failure produces two lines.[^failures]

| Event | Fields |
|---|---|
| `job.failed` | `job`, `connection`, `queue`, `attempts`, `job_uuid`, `exception`, `message`[^failures] |
| `schedule.failed` | `task`, `expression`, `exception`, `message`[^failures] |

[^processes]: `docker/config/supervisord.conf` — the `horizon` and `scheduler` programs, both logging to
    `/dev/stdout` and `/dev/stderr`.
[^gate]: `app/Providers/HorizonServiceProvider.php` — `gate()` allows a user whose email is in
    `horizon.allowed_emails`; `config/horizon.php` — `allowed_emails` splits `HORIZON_ALLOWED_EMAILS` on
    commas, and `path` is `horizon`.
[^horizon]: `config/horizon.php` — `defaults.supervisor-1` with `connection` `redis`, `queue` `default`,
    `tries` 1 and `timeout` 60; `environments.production` sets `maxProcesses` 10 and `local` 3.
[^timeout]: `config/horizon.php` — `defaults.supervisor-1.timeout` is 60; `config/queue.php` —
    `connections.redis.retry_after` is 90; Laravel Docs —
    [Queues: job expirations and timeouts](https://laravel.com/docs/13.x/queues#job-expirations-and-timeouts):
    `retry_after` must be larger than the timeout, or a job is processed twice.
[^drain]: `docker/config/supervisord.conf` — `horizon` has `stopwaitsecs=3600`, `stopasgroup=true` and
    `killasgroup=true`.
[^schedule]: `bootstrap/app.php` — `withSchedule()` runs `health:check`, `health:queue-check-heartbeat`
    and `health:schedule-check-heartbeat` every minute.
[^failures]: `app/Listeners/LogBackgroundFailures.php` — `jobFailed()` and `scheduledTaskFailed()` log
    an error with those fields, and the class comment states that Laravel reports the exception
    separately; `app/Providers/AppServiceProvider.php` — `configureHealthChecks()` registers both
    listeners.
