+++
title = "Logging"
subtitle = "a daily file on a developer machine, JSON lines in production, and error tracking"
status = "approved"
goals = false
intent = """
Logging exists so that whoever runs the application can see what it did: in a file on a developer
machine, and in production as one line per record that a central store can search by field. A
container writes no log file, so its lines must leave it as they happen.
"""

[[infobox]]
group = "Identity"
rows = [
  { label = "Local file", value = "storage/logs/laravel.log", note = "one file a day", cite = "daily" },
  { label = "Production channel", value = "stderr", cite = "stderr" },
  { label = "Setting", value = "LOG_STDERR_FORMATTER", cite = "formatter" },
]

[[infobox]]
group = "Rules"
rows = [
  { label = "Production format", value = "one JSON object a line", cite = "json" },
  { label = "Empty formatter", value = "channel stops, lines go to the emergency file", cite = "empty" },
  { label = "Exceptions", value = "Sentry, once a DSN is set", cite = "sentry" },
]
+++

The application logs to a daily file on a developer machine and to standard error in production, where
every process in the container writes to the same stream.[^daily][^stream] Searching those lines after
the container is gone is described on [Central store](logging/central-store.md); this page covers what is
written, where, and in what shape.

## Local file

With nothing set, every record goes to `storage/logs/laravel.log`, rotated daily.[^daily] Records at
`debug` and above are kept unless `LOG_LEVEL` raises the floor.[^daily]

## Production stream

Production runs with `LOG_CHANNEL=stderr`, so records go to the container's standard error.[^stderr]
Each record is one JSON object on one line, carrying the message, its context, the level, the channel
and the time, so a collector ships each as a field without parsing prose.[^json] A record written on a
developer machine with that channel selected reads:[^json]

```json
{"message":"probe","context":{"k":1},"level":200,"level_name":"INFO","channel":"local","datetime":"2026-09-15T02:41:00.906925+00:00","extra":{}}
```

The web server, PHP, the server-side renderer, the queue workers and the scheduler each write to the
same standard output and standard error, so their lines arrive together.[^stream] The container keeps
no log file of its own.[^stream]

**`LOG_STDERR_FORMATTER` names the formatter class**, and `Monolog\Formatter\LineFormatter` gives the
familiar bracketed line instead of JSON.[^formatter] Setting it to an empty value does not give plain
lines: the channel cannot be built, every record goes to an emergency file logger at
`storage/logs/laravel.log` inside the container, and nothing reaches standard error.[^empty]

## Background failures

A failed queued job or scheduled task is logged as an error carrying the job or task name, so the store
can be searched for it; the fields are described on [Background work](background-work.md).

## Error tracking

Exceptions are reported to Sentry once `SENTRY_LARAVEL_DSN` is set; with no DSN the integration is
inert.[^sentry] Logging and Sentry are separate: a log line is searched, an exception is grouped and
alerted on.[^sentry]

[^daily]: `config/logging.php` — `default` reads `LOG_CHANNEL` and falls back to `daily`; the `daily`
    channel writes `storage_path('logs/laravel.log')` at the level `LOG_LEVEL` sets, `debug` when unset.
[^stderr]: `.env.example` — the comment above `LOG_STDERR_FORMATTER` names `LOG_CHANNEL=stderr` as the
    production setting; `config/logging.php` — the `stderr` channel streams to `php://stderr`.
[^json]: `config/logging.php` — the `stderr` channel's `formatter` is `Monolog\Formatter\JsonFormatter`
    when `LOG_STDERR_FORMATTER` is unset; the sample is the line a run of
    `Log::channel('stderr')->info('probe', ['k' => 1])` printed.
[^stream]: `docker/config/supervisord.conf` — every `[program:…]` block, `php-fpm`, `nginx`,
    `inertia-ssr`, `horizon` and `scheduler`, sets `stdout_logfile=/dev/stdout` and
    `stderr_logfile=/dev/stderr` with no size limit.
[^formatter]: `config/logging.php` — `formatter` is whatever `LOG_STDERR_FORMATTER` names; a run with
    `Monolog\Formatter\LineFormatter` printed `[2026-09-15T02:41:01.180021+00:00] local.INFO: probe {"k":1} []`.
[^empty]: `vendor/laravel/framework/src/Illuminate/Log/LogManager.php` — `prepareHandler()` resolves any
    `formatter` value other than `default` from the container, and an empty name raises
    `Target class [] does not exist.`; `get()` catches it and switches to `createEmergencyLogger()`,
    which writes `Unable to create configured logger. Using emergency logger.` and every later record to
    `storage/logs/laravel.log`, as a run with `LOG_STDERR_FORMATTER=` showed.
[^sentry]: `config/sentry.php` — `dsn` reads `SENTRY_LARAVEL_DSN`; `bootstrap/app.php` —
    `withExceptions()` calls `Integration::handles()`, which reports rendered exceptions to Sentry.
