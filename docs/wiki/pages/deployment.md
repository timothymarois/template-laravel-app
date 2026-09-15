+++
title = "Deployment"
subtitle = "the container image, its supervised processes, the /up gate, the deploy phases and the fork knobs"
status = "approved"
intent = """
Deployment exists so that a product built on the template ships as one container image that runs every
process it needs, on Coolify, with a fork changing only the knobs it is given. The container should
stay in rotation while a dependency is degraded, and migrations should run once per deploy, never on
every restart.
"""

[[infobox]]
group = "Identity"
rows = [
  { label = "Base image", value = "ghcr.io/timothymarois/docker-laravel-base:8.4-v1", cite = "image" },
  { label = "Container gate", value = "/up", cite = "up" },
  { label = "Port", value = "80", cite = "nginx" },
]

[[infobox]]
group = "Values"
rows = [
  { label = "Upload limit", value = "25 MB", cite = "nginx" },
  { label = "Horizon drain on stop", value = "60 minutes", cite = "processes" },
]

[[infobox]]
group = "Rules"
rows = [
  { label = "Migrations", value = "post-deployment only", cite = "phases" },
  { label = "Project configuration", value = "docker/project/", cite = "project" },
]
+++

The image is built from the template's `Dockerfile` on a pre-built PHP-FPM base, and one container runs
nginx, PHP-FPM, the Inertia SSR server, Horizon and the scheduler under supervisord.[^image][^processes]
Dependency health is reported on [Health checks](health.md), the queue and scheduler on
[Background work](background-work.md), the log stream on [Logging](logging.md), and the release that
lands here on [Releases](releases.md).

## Processes

Every process restarts on its own if it dies, and writes its output to the container's log
stream.[^processes]

| Process | Runs |
|---|---|
| `php-fpm` | `php-fpm --nodaemonize`[^processes] |
| `nginx` | on port 80, with uploads limited to 25 MB[^nginx] |
| `inertia-ssr` | `php artisan inertia:start-ssr`[^processes] |
| `horizon` | `php artisan horizon`, given 60 minutes to finish in-flight jobs when stopped[^processes] |
| `scheduler` | `php artisan schedule:work`[^processes] |

A plain queue worker in place of Horizon, and a Reverb server on port 8080, are written in the same file
as blocks a fork switches on.[^optional]

## Container gate

Coolify's health check points at `/up`, which answers `200` once the framework boots and is excluded from
maintenance mode.[^up] It consults no database, cache or queue, so a degraded dependency does not take
the container out of rotation; `/health` reports that instead.[^up] The answer of a local site, copied
from a run, is a bare `200`.[^up]

## Phases

Coolify runs `docker/deploy/pre-deployment.sh` in the old container before the swap and
`docker/deploy/post-deployment.sh` in the new one after the build; `php artisan migrate --force` runs in
the second, so migrations run once per deploy and never on a restart.[^phases] The pre-deployment script
does nothing until a fork fills it in.[^phases] The entrypoint runs on every start: it creates missing
storage directories with `app:ensure-storage`, caches configuration with `php artisan optimize`, and
links `public/storage`.[^entrypoint] The dashboard settings a Coolify application takes, such as ports and
the health check, are described in Coolify's own documentation.[^coolify]

## Knobs

A fork changes four things in the managed core and nothing else: the base image tag, the asset build
command, the process list, and the post-deployment command.[^knobs] Its own nginx and PHP settings go in
`docker/project/`, whose three folders are copied into the image after the template's and ship
empty.[^project] The `docker` block of `template-manifest.json` records the PHP line, package manager,
build command, base image and which services the deployment requires.[^manifest]

[^image]: `Dockerfile` — `FROM ghcr.io/timothymarois/docker-laravel-base:8.4-v1 AS base`, the `build`
    stage, and the `runtime-config` and `runtime` stages; its header names Coolify as the deploy target
    with `Build Pack = Dockerfile · Port = 80 · Health check = /up`.
[^processes]: `docker/config/supervisord.conf` — the `php-fpm`, `nginx`, `inertia-ssr`, `horizon` and
    `scheduler` programs, each with `autorestart=true` and logs to `/dev/stdout` and `/dev/stderr`;
    `horizon` has `stopwaitsecs=3600`.
[^nginx]: `docker/config/nginx.conf` — `listen 80 default_server` and `client_max_body_size 25M`.
[^optional]: `docker/config/supervisord.conf` — the commented `[program:queue]` and `[program:reverb]`
    blocks under the optional alternatives.
[^up]: `bootstrap/app.php` — `withRouting(health: '/up')`;
    `vendor/laravel/framework/src/Illuminate/Foundation/Configuration/ApplicationBuilder.php` —
    `withRouting()` registers that path and excepts it from `PreventRequestsDuringMaintenance`; the
    `Dockerfile` header names it as the Coolify health check; the sample is the status of a local site.
[^phases]: `docker/deploy/post-deployment.sh` — runs `php artisan migrate --force` and its comment names
    the Coolify `Post-deployment Command`; `docker/deploy/pre-deployment.sh` — `exit 0` with a comment
    naming the `Pre-deployment Command` and forbidding migrations there; `docker/deploy/entrypoint.sh` —
    the comment that migrations do not run on start.
[^entrypoint]: `docker/deploy/entrypoint.sh` — `php artisan app:ensure-storage`, `php artisan optimize`
    and `php artisan storage:link --force`, then `exec "$@"`.
[^coolify]: Coolify Docs — [Applications](https://coolify.io/docs/applications/): configuration covers
    domains, ports, variables, health checks and advanced settings.
[^knobs]: `Dockerfile` — the `PER-PROJECT KNOBS` header lists the base image tag, the asset build command,
    `docker/config/supervisord.conf` and the post-deployment command.
[^project]: `Dockerfile` — the three `COPY docker/project/...` directives after the managed-core copies,
    and their comment; `docker/project/nginx/http`, `docker/project/nginx/server` and
    `docker/project/php` hold only `.gitkeep`.
[^manifest]: `template-manifest.json` — the `docker` block: `php`, `pkg`, `build`, `baseImage` and
    `requires`.
