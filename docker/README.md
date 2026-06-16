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
  config/                    ← service config baked into the image
    nginx.conf
    php.ini
    supervisord.conf         ← the process list (enable only what's needed)
  deploy/                    ← lifecycle scripts
    entrypoint.sh            ← Start phase: runs every container boot
    pre-deployment.sh        ← Coolify "Pre-deployment Command" (old container)
    post-deployment.sh       ← Coolify "Post-deployment Command" (new container)
```

`template-manifest.json` (repo root) records the template `version` + the build
knobs (`php`, `pkg`, `build`). This README's table is the human-readable companion.

## This project's setup

Human-readable mirror of `template-manifest.json` → `docker` (the machine-readable
declaration of what this project requires — `php`/`pkg`/`build` plus the
`requires` flags that say which resources + processes it needs). Forks: edit
both, then trim `config/supervisord.conf` and set Coolify to agree.

| Setting            | This project        | Drives                                            |
|--------------------|---------------------|---------------------------------------------------|
| PHP                | `8.4`               | `ARG PHP_VERSION` in the Dockerfile               |
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
3. Set the build knobs in the `Dockerfile` (`ARG PHP_VERSION`; swap `pnpm`→`npm`
   or `build-ssr`→`build` if needed) and update `template-manifest.json`.
4. Fill `docker/deploy/post-deployment.sh` with this project's release tasks
   (migrations) — or leave it a no-op for a DB-less site.
5. Set Coolify env + resources to agree (see below).

## Deploy phases — where each script runs

| Phase | When | Where | Examples |
|-------|------|-------|----------|
| **Build** | once, when the image is built | `Dockerfile` | composer install, `pnpm build-ssr` |
| **Pre-deploy** | before the swap, in the **OLD** container (old code) | `docker/deploy/pre-deployment.sh` (Coolify **Pre-deployment Command**) | maintenance mode, backups — **never migrations** |
| **Post-deploy** | once per deploy, in the **NEW** container (new code), after build | `docker/deploy/post-deployment.sh` (Coolify **Post-deployment Command**) | `migrate --force`, `tenants:migrate --force` |
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
- **Logs:** `LOG_CHANNEL=stderr` → Coolify **Logs** tab. **Uploads:** S3, or a volume on `/var/www/html/storage/app/public`.

## Extensions baked in

`pdo_mysql` · `pdo_pgsql` · `redis` · `bcmath` · `intl` · `zip` · `gd` · `exif`
· `pcntl` · `opcache` · `sockets` · `gmp` (`posix` is built into the base image).
This superset covers every project, so most forks need no extension change.

## Drift policy & versioning

"In sync" means the **managed core** matches this template (at the fork's
`version`), while the **knobs** may differ. That boundary defines drift.

| Managed core (tracks template) | Knobs (per-fork, may differ) |
|---|---|
| `Dockerfile` build stages, extensions, `CMD` | `ARG PHP_VERSION` |
| `docker/config/nginx.conf`, `docker/config/php.ini` | asset-build command, pnpm/npm |
| `docker/deploy/entrypoint.sh` | which `supervisord.conf` process blocks are enabled |
| `.dockerignore` | env-driven settings |

Rules:

- Forks change **only** knobs. New capabilities go into this template first.
- A managed-core change bumps the **template version** + a `CHANGELOG.md` entry
  tagged `Docker:` (patch = fix, minor = feature).
- Each fork records its knobs + requirements in `template-manifest.json`:
  ```json
  "docker": {
    "php": "8.4", "pkg": "pnpm", "build": "build-ssr",
    "requires": { "database": true, "redis": true, "ssr": true, "horizon": true, "scheduler": true, "reverb": false }
  }
  ```
  The Docker setup tracks the fork's template `version` — there is no separate
  Docker version. The per-fork knobs + `requires` flags are expected to differ.
