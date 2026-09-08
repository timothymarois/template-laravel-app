# Docker — Production Deploy (Coolify)

Canonical container setup for this template and its forks. **One universal image** —
the root `Dockerfile` + `docker/` deploy every project. There is **no "simple vs
full" variant**: each project uses the same files and enables only the processes
and resources it actually needs. What a given project needs is declared in
**[This project's setup](#this-projects-setup)** below — keep that section, the
config files, and the Coolify settings matched.

## `docker/` layout

```
docker/
  README.md                  ← this file (declares the project's setup)
  config/                    ← service config baked into the image — MANAGED CORE
    nginx.conf
    php.ini
    supervisord.conf         ← the process list (enable only what's needed)
    nginx-snippets/          ← reusable FastCGI blocks a project location can include
  project/                   ← THIS FORK's config — empty by default, never tracked upstream
    nginx/http/*.conf        ← http context: limit_req_zone, limit_conn_zone, map, geo
    nginx/server/*.conf      ← server context: locations needing their own body ceiling
    php/*.ini                ← loaded from conf.d AFTER the template's php.ini
  deploy/                    ← lifecycle scripts
    entrypoint.sh            ← Start phase: runs every container boot
    pre-deployment.sh        ← Coolify "Pre-deployment Command" (old container)
    post-deployment.sh       ← Coolify "Post-deployment Command" (new container)
```

`template-manifest.json` (repo root) records the template `version` + the build
knobs (`php`, `pkg`, `build`, `baseImage`). This README's table is the
human-readable companion.

## This project's setup

Human-readable mirror of `template-manifest.json` → `docker` (the machine-readable
declaration of what this project requires — `php`/`pkg`/`build` plus the
`requires` flags that say which resources + processes it needs). Forks: edit
both, then trim `config/supervisord.conf` and set Coolify to agree.

| Setting            | This project        | Drives                                            |
|--------------------|---------------------|---------------------------------------------------|
| PHP                | `8.4`               | the `docker-laravel-base` tag you pin (`:8.4-v1`)  |
| Package manager    | `pnpm`              | build stage (`pnpm` vs `npm`)                     |
| Build command      | `build-ssr`         | client + SSR bundles (`pnpm build-ssr`)           |
| Database           | **yes**             | DB resource + post-deploy migrations              |
| Redis              | **yes**             | Redis resource; `QUEUE`/`CACHE` drivers           |
| Queue (Horizon)    | **yes**             | `horizon` process                                 |
| Scheduler          | **yes**             | `scheduler` process (`schedule:work`)             |
| Inertia SSR        | **yes**             | `inertia-ssr` process                             |
| Reverb websockets  | **no**              | `reverb` process + expose `:8080`                 |

**Processes enabled** (`docker/config/supervisord.conf`): `php-fpm` · `nginx` ·
`inertia-ssr` · `horizon` · `scheduler`.

> This is the template's default (a full-featured app). A DB-less brochure site
> would set Database/Redis/Queue/Scheduler to **no** and delete the
> `horizon`/`scheduler` blocks from `supervisord.conf`.

## Tailoring a fork (do this once)

1. Edit **This project's setup** above to match the app.
2. In `docker/config/supervisord.conf`, **delete the OPTIONAL process blocks you
   don't use** (`inertia-ssr`, `horizon`, `scheduler`, `reverb`). Leave `php-fpm`
   + `nginx` (always required).
3. Set the build knobs in the `Dockerfile` (pin the `docker-laravel-base` tag for
   the PHP line; swap `pnpm`→`npm` or `build-ssr`→`build` if needed) and update
   `template-manifest.json`.
4. Fill `docker/deploy/post-deployment.sh` with this project's release tasks
   (migrations) — or leave it a no-op for a DB-less site.
5. Set Coolify env + resources to agree (see below).

## Deploy phases — where each script runs

| Phase | When | Where | Examples |
|-------|------|-------|----------|
| **Build** | once, when the image is built | `Dockerfile` | composer install, `pnpm build-ssr` |
> **Stages.** `base` → `build` (composer, pnpm, Vite) and `base` → `runtime-config` → `runtime`.
> `runtime-config` is the image without the application: the same configuration COPY directives the
> shipped image uses, with the app copied in afterwards. Configuration layers therefore sit before the
> app copy, so a source change no longer invalidates them, and CI can prove the effective web-tier
> configuration without running a full application build. Build `--target runtime` for anything real.

| **Pre-deploy** | before the swap, in the **OLD** container (old code) | `docker/deploy/pre-deployment.sh` (Coolify **Pre-deployment Command**) | maintenance mode, backups — **never migrations** |
| **Post-deploy** | once per deploy, in the **NEW** container (new code), after build | `docker/deploy/post-deployment.sh` (Coolify **Post-deployment Command**) | `migrate --force` |
| **Start** | every container boot (restarts/scaling) | `docker/deploy/entrypoint.sh` (automatic) | `ensure-storage`, `optimize`, `storage:link` |

**Migrations go in the Post-deployment phase** — the new container has the new
code + new migrations, and the build (`composer install` + `pnpm build-ssr`) is a
hard gate before it ever runs. Never migrate in the Dockerfile (no DB at build)
or in `entrypoint.sh` (runs every restart + races across replicas). The new
container is already serving when post-deploy runs, so keep migrations
**backward-compatible** (additive; split destructive changes across two deploys).

```
Pre-deployment Command:  sh /var/www/html/docker/deploy/pre-deployment.sh
Post-deployment Command: sh /var/www/html/docker/deploy/post-deployment.sh
```

Both scripts are always present (a no-op if empty) so the Coolify commands are
wired once and deploy behavior lives in versioned scripts, not the Coolify UI.

## Options reference — what each one turns on

| Option | Supervisord process | Coolify needs |
|--------|---------------------|---------------|
| Database | — | DB resource (Postgres/MySQL) + `migrate` in post-deploy |
| Redis | — | Redis resource; `QUEUE_CONNECTION=redis`, `CACHE_STORE=redis` |
| Horizon | `horizon` | Redis |
| Scheduler | `scheduler` | — |
| Inertia SSR | `inertia-ssr` | `build-ssr` build command |
| Reverb | `reverb` | expose `:8080` as a second domain/port; `REVERB_*` env |

DB-less project? Set `SESSION_DRIVER=file`, `CACHE_STORE=file`,
`QUEUE_CONNECTION=sync`, and leave `post-deployment.sh` empty.

## Coolify settings (every app)

- **Build Pack:** Dockerfile · **Ports Exposes:** `80` — Coolify defaults to `3000`; you MUST change it to `80` or Traefik returns 502 (nginx listens on 80).
- **Health Check:** scheme **`http`**, path **`/up`**, port **`80`** (the image ships `wget`). Never `https` on port 80. If it misbehaves, disable it; the app is healthy without it.
- **Pre/Post-deployment Commands:** wire both scripts (above).
- **Env:** `APP_KEY`, `APP_URL`, `APP_ENV=production`, `LOG_CHANNEL=stderr`, plus the per-project vars from the setup table (DB, Redis, `REVERB_*`, app-specifics like Sentry/S3).
- **`VITE_*` are build-time, not runtime.** Vite **inlines** `VITE_*` into the client bundle at build, reading them from `.env.example` (the throwaway build env) — Coolify's runtime env can't reach an already-compiled bundle. If the frontend needs a public value in prod (Reverb host, analytics id…), set it in committed `.env.example` (these are public, not secret).
- **Mark secrets** (`APP_KEY`, `MAIL_PASSWORD`, `AWS_*`) as **Runtime only** so they aren't baked into image layers.
- **Resources:** add Postgres/MySQL + Redis only if the project's setup uses them.
- **Domains** with `https://` → automatic Let's Encrypt SSL (issued once, cached, auto-renewed). Wildcard needs Traefik DNS-01.
- **Logs:** `LOG_CHANNEL=stderr` → every process logs to stdout/stderr as **one-line JSON by default** (Coolify **Logs** tab + the feed for centralized logging). Ship those streams to one central sink — the recommended default is Coolify → Loki/Grafana via Alloy (see the [Logging guide](../docs/concepts/logging.md)). Set `LOG_STDERR_FORMATTER=` (empty) for human-readable lines. The container is ephemeral with no log file to browse. **Uploads:** S3, or a volume on `/var/www/html/storage/app/public`.

## Base image & extensions

The PHP-FPM base — the extension superset below **plus composer** — is **not built
here**. It is pre-built and published as a standalone image:

- **Image:** `ghcr.io/timothymarois/docker-laravel-base` (public GHCR package)
- **Source + publish CI:** [`timothymarois/docker-laravel-base`](https://github.com/timothymarois/docker-laravel-base)

The root `Dockerfile` consumes it directly —
`FROM ghcr.io/timothymarois/docker-laravel-base:8.4-v1 AS base` — so the heavy
extension compile runs **once in CI**, pulls in seconds on deploy, and the named
image survives `docker image prune`.

`pdo_mysql` · `pdo_pgsql` · `redis` · `bcmath` · `intl` · `zip` · `gd` · `exif`
· `pcntl` · `opcache` · `sockets` · `gmp` (`posix` is built into the base image).
This superset covers every project, so most forks need no extension change.

**To change the extension set:** edit + bump the
[`docker-laravel-base`](https://github.com/timothymarois/docker-laravel-base) repo,
publish a new tag, then re-pin the `FROM` tag here and bump the template version.
Forks adopt the new pin deliberately. **Never re-add the apt/extension compile to
this `Dockerfile`.**

## Project configuration

`docker/config/` is **managed core** — the template owns it, and a fork that
edits it drifts. `docker/project/` is the supported way to add what only this
fork needs. It ships empty; a wildcard include that matches nothing is not an
error, so a fork that adds nothing gets byte-identical behavior.

| Directory | Copied to | Context | Use it for |
|---|---|---|---|
| `docker/project/nginx/http/*.conf` | `/etc/nginx/project/http/` | http | `limit_req_zone`, `limit_conn_zone`, `map`, `geo` — anything that cannot be declared inside a `server` block |
| `docker/project/nginx/server/*.conf` | `/etc/nginx/project/server/` | server | `location` blocks, most often one that needs its own `client_max_body_size` |
| `docker/project/php/*.ini` | `/usr/local/etc/php/conf.d/` | — | PHP directives that must beat the template's. **Prefix `zzz-`**: PHP scans `conf.d` in filename order and the template lands at `zz-app.ini`, so an earlier name loses. |

### Raising the body ceiling for one route

The server-wide `client_max_body_size 25M` is a deliberate protection: nginx
buffers a request body to `client_body_temp_path` **before** PHP sees it, so
raising it at server level hands every endpoint — including unauthenticated
ones — that much temp disk and inbound bandwidth per concurrent request. Scope
the increase to the URI that needs it instead.

A project location must terminate in FastCGI **itself**. Wrapping the existing
`location /` does not work: `try_files $uri /index.php` issues an internal
redirect, nginx re-runs location matching, and the elevated ceiling is lost to
`location ~ \.php$` before the body is read. Include the front-controller
snippet to hand any URI to Laravel without copying managed-core wiring:

```nginx
# docker/project/nginx/http/uploads.conf  (http context — zones live here)
limit_req_zone  $binary_remote_addr zone=upload_req:1m rate=10r/m;
limit_conn_zone $binary_remote_addr zone=upload_conn:1m;
```

```nginx
# docker/project/nginx/server/uploads.conf  (server context)
location ~ ^/admin/products/[0-9]+/media$ {
    client_max_body_size 110M;

    # Preaccess phase — runs BEFORE the body is read, so a refused request
    # never reaches client_body_temp_path. Without these, one elevated route
    # is an unmetered temp-disk and bandwidth sink.
    limit_conn upload_conn 2;
    limit_req  zone=upload_req burst=2 nodelay;
    limit_req_status 429;
    limit_conn_status 429;

    client_body_timeout 120s;

    include /etc/nginx/snippets/laravel-front-controller.conf;
}
```

**Match carefully.** The project include is the **last** thing in the `server`
block, and nginx tries regex locations in configuration order — so everything
the template ships wins a tie. A project regex can neither shadow
`location ~ \.php$` nor punch a hole in the dotfile `deny all`. Use
`location = /exact/uri`, or a regex that cannot match either.

**Raise PHP to match.** nginx accepting the body is only half of it —
`upload_max_filesize` is the file limit and `post_max_size` bounds the whole
multipart body, so `post_max_size` must exceed the file limit by enough to
carry boundary lines and part headers:

```ini
; docker/project/php/zzz-project.ini
upload_max_filesize=100M
post_max_size=110M
```

### Proving it

`.github/workflows/docker-config.yml` builds the image twice — once with an
empty `docker/project/`, once with the fixtures in `tests/docker/fixtures/` —
and asserts against the **running container**, not the source files:
`nginx -t`, the effective `nginx -T` (right context, loaded exactly once, no
duplicate directive, front-controller wiring present), `ini_get()`, and a real
oversized POST that must be refused on a default route and accepted on the
elevated one. A file copied to the wrong path or included in the wrong context
passes every source-level check and fails here.

## Drift policy & versioning

"In sync" means the **managed core** matches this template (at the fork's
`version`), while the **knobs** may differ. That boundary defines drift.

| Managed core (tracks template) | Knobs (per-fork, may differ) |
|---|---|
| `Dockerfile` build stages, base image, `CMD` | base image tag (PHP line / version) |
| `docker/config/nginx.conf`, `docker/config/php.ini`, `docker/config/nginx-snippets/` | asset-build command, pnpm/npm |
| — | **`docker/project/**`** — this fork's own nginx/PHP configuration |
| `docker/deploy/entrypoint.sh` | which `supervisord.conf` process blocks are enabled |
| `.dockerignore` | env-driven settings |

Rules:

- Forks change **only** knobs. New capabilities go into this template first.
- A managed-core change bumps the **template version** + a `.template/CHANGELOG.md`
  entry tagged `Docker:` (patch = fix, minor = feature).
- Each fork records its knobs + requirements in `template-manifest.json`:
  ```json
  "docker": {
    "php": "8.4", "pkg": "pnpm", "build": "build-ssr",
    "baseImage": "ghcr.io/timothymarois/docker-laravel-base:8.4-v1",
    "requires": { "database": true, "redis": true, "ssr": true, "horizon": true, "scheduler": true, "reverb": false }
  }
  ```
  The Docker setup tracks the fork's template `version` — there is no separate
  Docker version. The per-fork knobs + `requires` flags are expected to differ.
