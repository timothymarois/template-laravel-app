# Migrating a fork to template v5.1.0

v5.1.0 is **additive** — no framework, schema, or runtime API changes. It does two things:

- **Part A — manifest rename** (`template-version.json` → `template-manifest.json` + a `requires` block). Applies to **every** fork. ~2 minutes, mechanical.
- **Part B — Docker/Coolify deploy setup** (`Dockerfile`, `.dockerignore`, an organized `docker/` tree). Apply **only when the fork is actually deploying on Coolify.** Skip for forks that aren't.

> **For agents:** Part A is mechanical and universal — do it. Part B is a per-project judgment call — do NOT copy Docker files into a fork that isn't moving to Coolify. Nothing in v5.1.0 touches application code, so `pnpm check` should be unaffected; run it anyway if you edit anything outside `docker/`.

## Prerequisites

- Fork is on template **v5.0.1** (apply earlier migrations first). Check: `jq -r .version template-version.json` → expect `5.0.x`.
- Baseline `pnpm check` is green.
- You can run `git`, and (for Part B) inspect the fork's Coolify app.

---

## Part A — Rename the manifest (every fork)

The version file is renamed and gains a machine-readable declaration of the project's Docker requirements.

1. **Rename the file:**
   ```sh
   git mv template-version.json template-manifest.json
   ```
2. **Edit `template-manifest.json`:**
   - Remove `docker.variant` if present (the "simple vs full" split is gone in v5.1.0).
   - Add a `docker.requires` block declaring what the project needs (set each flag to the project's reality):
     ```json
     "docker": {
       "php": "8.4",
       "pkg": "pnpm",
       "build": "build-ssr",
       "requires": {
         "database": true,
         "redis": true,
         "ssr": true,
         "horizon": true,
         "scheduler": true,
         "reverb": false
       }
     }
     ```
     A fork that hasn't adopted Docker yet still records `requires` — it's just a declaration of intent; the real wiring happens in Part B.
   - Set `version` to `5.1.0` (do this last if you're also doing Part B).
3. **Repoint any tooling** that read `template-version.json` to `template-manifest.json`.

**Verify:**
```sh
test -f template-manifest.json && ! test -f template-version.json && echo "renamed OK"
jq -e '.docker.requires' template-manifest.json >/dev/null && echo "requires present"
# Nothing should still reference the old name:
grep -rl "template-version.json" . --exclude-dir=vendor --exclude-dir=node_modules --exclude-dir=.git || echo "no stale refs"
```

---

## Part B — Adopt the Docker setup (forks moving to Coolify)

### B1. Copy the files from the template

Copy into the fork root, preserving structure:

- `Dockerfile`
- `.dockerignore`
- `docker/` — the whole tree: `config/` (nginx.conf · php.ini · supervisord.conf), `deploy/` (entrypoint.sh · pre-deployment.sh · post-deployment.sh), `README.md`.

There is **no `simple/` variant** — one universal setup; each project enables only what it needs.

### B2. Declare the project (the source of truth)

Edit two things to match the app, and keep them in sync:

- `template-manifest.json` → `docker.requires` (from Part A).
- `docker/README.md` → the **"This project's setup"** table (the human mirror).

### B3. Tailor the config to `requires`

In `docker/config/supervisord.conf`, keep `php-fpm` + `nginx` (always required) and **delete the OPTIONAL process blocks the project doesn't use:**

| `requires` | Action in `supervisord.conf` |
|---|---|
| `ssr: false` | delete `[program:inertia-ssr]` |
| `horizon: false` | delete `[program:horizon]` (or swap for the commented `[program:queue]` block) |
| `scheduler: false` | delete `[program:scheduler]` |
| `reverb: true` | uncomment `[program:reverb]` and expose `:8080` in Coolify |

Dockerfile knobs: set `ARG PHP_VERSION` (match `docker.php`), the build command (`build-ssr` vs `build`), and `pnpm` vs `npm`.

DB-less project (`database: false`): set `WAIT_FOR_DB=false` in Coolify env and leave `docker/deploy/post-deployment.sh` a no-op.

### B4. Release tasks

Put migrations in `docker/deploy/post-deployment.sh` (runs in the NEW container, after a successful build):

```sh
php artisan migrate --force
# multi-tenant: php artisan tenants:migrate --force
# storage on a fresh volume: php artisan app:ensure-storage
```

Leave `docker/deploy/pre-deployment.sh` empty (or `php artisan down` for a maintenance window — it runs in the OLD container, so it can't run new migrations).

### B5. Coolify wiring

- **Build Pack:** Dockerfile · **Ports Exposes:** `80` (NOT the default `3000`, or Traefik 502s) · **Health Check:** `http` `/up` `80`.
- **Pre-deployment Command:** `sh /var/www/html/docker/deploy/pre-deployment.sh`
- **Post-deployment Command:** `sh /var/www/html/docker/deploy/post-deployment.sh`
- **Resources:** add Postgres/MySQL + Redis to match `requires.database` / `requires.redis`.
- **Env:** `APP_KEY`, `APP_URL`, `APP_ENV=production`, `LOG_CHANNEL=stderr`, plus the per-project vars (`DB_*`, `REDIS_*`, `REVERB_*`, `WAIT_FOR_DB`, app-specifics). Mark secrets **Runtime-only**.
- **Domains** with `https://` → automatic Let's Encrypt.

### B6. Verify the build paths resolve

A wrong Dockerfile `COPY` path = broken build. Confirm the targets exist under the new structure:

```sh
for f in docker/config/nginx.conf docker/config/php.ini docker/config/supervisord.conf docker/deploy/entrypoint.sh; do
  test -f "$f" && echo "ok  $f" || echo "MISSING  $f"
done
grep -n 'COPY docker/' Dockerfile   # expect docker/config/* and docker/deploy/entrypoint.sh
```

---

## Finish

- Bump `template-manifest.json` `version` → `5.1.0` and refresh `updated`.
- Copy this `CHANGELOG.md` entry into the fork so it tracks which template version it's on.
- Re-read `AGENTS.md` → "Docker / Deployment" and perform the post-task review.
- If you only did Part A, no app behavior changed — a `pnpm check` is optional but cheap insurance.
