# Docker — Production Deploy (Coolify)

Canonical container setup for this template and its forks. The root `Dockerfile`
+ `docker/` deploy this app as the **full** variant. For a DB-less site, switch
to the **simple** variant (two-file swap). Pick the variant and knobs that match
the project's requirements — guide below.

## 1. Choose your variant

One question: **does the app need a database, cache, or queue workers?**

- **No** — brochure/marketing site, forms email out, nothing persisted →
  **simple**. No DB/Redis resources; file sessions + cache; just web + SSR.
- **Yes** — auth, persisted data, queued jobs (Horizon), websockets →
  **full** (the default). Add MySQL/Postgres + Redis resources in Coolify.

| | full (default) | simple |
|---|---|---|
| Use for | DB / cache / queue apps | DB-less sites |
| Processes | php-fpm·nginx·ssr·horizon·scheduler | php-fpm·nginx·ssr |
| Needs | MySQL/Postgres + Redis | nothing external |
| Entrypoint | waits for DB, then `optimize` | `optimize` only |
| Coolify env | `QUEUE_CONNECTION=redis`, DB + Redis | `SESSION_DRIVER=file`, `CACHE_STORE=file`, `QUEUE_CONNECTION=sync` |
| Pre-deploy | `sh /var/www/html/docker/deploy.sh` | none |

### Switch a fork to simple

```sh
cp docker/simple/supervisord.conf docker/supervisord.conf
cp docker/simple/entrypoint.sh    docker/entrypoint.sh
rm -f docker/deploy.sh
rm -rf docker/simple
```

Same Dockerfile either way — the variant is just the process list + entrypoint.

## Deploy phases — where each script runs

Three distinct phases; keep each piece in the right place:

| Phase | When | Where | Examples |
|-------|------|-------|----------|
| **Build** | once, when the image is built | `Dockerfile` | composer install, `pnpm build-ssr` |
| **Release** | once per deploy, after build, before the new container goes live | `docker/deploy.sh` (Coolify pre-deploy command) | `migrate --force`, one-time release tasks |
| **Start** | every container boot (incl. restarts/scaling) | `docker/entrypoint.sh` | `optimize`, `storage:link`, DB-readiness wait |

**Migrations run in the Release phase — `docker/deploy.sh`.** Never in the
Dockerfile (no DB at build time) and never in `entrypoint.sh` (it runs on every
restart and would race across replicas). Wire Coolify's pre-deployment command to:

```
sh /var/www/html/docker/deploy.sh
```

The `simple` variant has no Release phase (no DB) — no `deploy.sh`, no pre-deploy command.

## 2. Customize to the project's requirements

The Dockerfile is universal; change only what the app actually needs:

| If the app… | Do this |
|---|---|
| runs on **PHP 8.3** | set `ARG PHP_VERSION=8.3` in the Dockerfile |
| uses **npm** (not pnpm) | swap `pnpm`→`npm` and `pnpm-lock.yaml`→`package-lock.json` in the build stage |
| has **no `build-ssr`** script | use `pnpm build`, and remove the `inertia-ssr` process |
| is **Blade-only** (no Vue/Inertia) | also delete the two Node install blocks (leaner image) |
| uses **Reverb** websockets | uncomment the `reverb` program in supervisord + expose port 8080 in Coolify |
| **doesn't use Horizon** | swap the `horizon` program for the commented `queue:work` block |
| is **multi-tenant** in prod | append `&& php artisan tenants:migrate --force` to the pre-deploy command |
| uses **Postgres** | nothing — `pdo_pgsql` is already built in |
| needs a **PHP extension** not listed | add it to the `docker-php-ext-install` line (+ any apt `-dev` lib) |

The extension set is already a superset (MySQL + Postgres, Redis, Horizon,
atlas-php's `ext-sockets`, image handling), so most apps need no extension change.

## 3. Coolify settings (every app)

- **Build Pack:** Dockerfile · **Ports Exposes:** `80` — Coolify defaults to `3000`; you MUST change it to `80` or Traefik returns 502 (the app's nginx listens on 80).
- **Health Check:** scheme **`http`**, path **`/up`**, port **`80`** — the image ships `wget` for Coolify's probe. Never `https` (port 80 is the internal plain-HTTP port). If it misbehaves, disable it; the app is healthy without it.
- **Env:** `APP_KEY`, `APP_URL`, `APP_ENV=production`, `LOG_CHANNEL=stderr`, plus
  the variant-specific vars above and app-specifics (Sentry, S3…).
- **Mark secrets** (`APP_KEY`, `MAIL_PASSWORD`, `AWS_*`) as **Runtime only** so
  they aren't baked into image layers.
- **Domains** with `https://` → automatic Let's Encrypt SSL (issued once, cached,
  auto-renewed). Wildcard needs Traefik DNS-01.
- **Logs:** `LOG_CHANNEL=stderr` → Coolify **Logs** tab. **Uploads:** S3, or a
  volume on `/var/www/html/storage/app/public`.

## Extensions baked in

`pdo_mysql` · `pdo_pgsql` · `redis` · `bcmath` · `intl` · `zip` · `gd` · `exif`
· `pcntl` · `opcache` · `sockets` · `gmp` (`posix` is built into the base image).

## Drift policy & versioning

Because these files are customized per fork, "in sync" means the **managed core**
matches this template (at the fork's `version`), while the **knobs** may
differ. That boundary defines drift.

| Managed core (tracks template) | Knobs (per-fork, may differ) |
|---|---|
| Dockerfile build stages, extensions, `CMD` | `ARG PHP_VERSION` |
| `docker/nginx.conf`, `docker/php.ini` | asset-build command, pnpm/npm |
| `docker/entrypoint.sh`, `docker/deploy.sh` | `supervisord.conf` variant (full/simple) |
| `.dockerignore` | env-driven settings |

Rules:

- Forks change **only** knobs. New capabilities go into this template first.
- A managed-core change bumps the **template version** + a `CHANGELOG.md` entry
  tagged `Docker:` (patch = fix, minor = feature).
- Each fork records its variant + knobs in `template-version.json`:
  ```json
  "docker": { "variant": "simple", "php": "8.4", "pkg": "pnpm", "build": "build-ssr" }
  ```
  The Docker setup tracks the fork's template `version` — there is no separate
  Docker version. The per-fork knobs above are expected to differ.
