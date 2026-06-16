# Migrating a fork to template v5.1.3

v5.1.3 aligns the runtime defaults with the mandatory Redis stack, makes the tenant-invite email asynchronous, and reorganizes the template's own lineage docs into a `.template/` directory. Patch — no schema, API, or Docker changes. ~2 minutes, mechanical.

- **Every fork:** flip the two config defaults to `redis` and update `.env.example`.
- **Forks with tenancy enabled:** also pick up the `ShouldQueue` change on the invite notification.
- **Template housekeeping (no fork action):** the template's lineage docs moved into `.template/` — see Part C.
- **Sync check (recommended):** confirm the fork's template-managed + Docker-core files haven't drifted from the template — excluding your own knobs/customizations — see Part D.

> **⚠️ Requires Redis to be reachable.** Redis is already a documented stack requirement (queue + cache via Horizon). After this change, when `CACHE_STORE` / `QUEUE_CONNECTION` are **unset**, the app falls back to `redis` instead of `database`. A fork that left those vars unset **and** has no reachable Redis will break on the next deploy. Provision Redis (`REDIS_HOST` / `REDIS_PORT`) — or, if you genuinely intend the database driver, pin `CACHE_STORE=database` / `QUEUE_CONNECTION=database` explicitly in your env before upgrading.

> **Why this matters:**
> - **Cache.** `TrackLastSeen` throttles its DB writes via `Cache::has()` / `Cache::put()` on every authenticated request. On the `database` cache store that throttle was itself a per-request `SELECT` + `INSERT` — the exact write it exists to prevent. On `redis` it's an in-memory op.
> - **Queue.** Horizon only consumes the `redis` connection. A production deploy that didn't inject `QUEUE_CONNECTION=redis` silently ran jobs on the `database` driver, bypassing Horizon entirely.
> - **Invite email.** `TenantInvitationNotification` sent synchronously inside `TenantInviteService::send()`, blocking the response for the full SMTP round-trip (200ms–seconds). Queuing it moves that off the request.

## Prerequisites

- Fork is on template **v5.1.2** (apply earlier migrations first). Check: `jq -r .version template-manifest.json` → expect `5.1.2`.
- Redis is reachable from the app (`REDIS_HOST` / `REDIS_PORT` set), or you will pin the drivers explicitly (see the warning above).
- Baseline `pnpm check` is green.

---

## Part A — Config defaults (every fork)

`config/cache.php`:

```diff
-    'default' => env('CACHE_STORE', 'database'),
+    'default' => env('CACHE_STORE', 'redis'),
```

`config/queue.php`:

```diff
-    'default' => env('QUEUE_CONNECTION', 'database'),
+    'default' => env('QUEUE_CONNECTION', 'redis'),
```

`.env.example` — set the cache store to `redis` (queue is already `redis`):

```diff
-# Cache: Use "database" for local, "redis" for production
-CACHE_STORE=database
+# Cache: Redis (matches the mandatory stack — Herd ships Redis locally, prod via Coolify)
+CACHE_STORE=redis
```

> `phpunit.xml` already forces `CACHE_STORE=array` and `QUEUE_CONNECTION=sync`, so your test suite is unaffected and needs no Redis. If your fork's `phpunit.xml` does **not** override these, add the overrides now so tests don't depend on a Redis server.

---

## Part B — Queue the invite notification (tenancy-enabled forks only)

`app/Notifications/Tenancy/TenantInvitationNotification.php` — implement `ShouldQueue` and pull in `SerializesModels`:

```diff
 use Illuminate\Bus\Queueable;
+use Illuminate\Contracts\Queue\ShouldQueue;
 use Illuminate\Notifications\Messages\MailMessage;
 use Illuminate\Notifications\Notification;
+use Illuminate\Queue\SerializesModels;

-class TenantInvitationNotification extends Notification
+class TenantInvitationNotification extends Notification implements ShouldQueue
 {
-    use Queueable;
+    use Queueable, SerializesModels;
```

> If your fork extended this notification or wired a different invite mailer, apply the same `ShouldQueue` + `SerializesModels` to whichever class actually sends the email. A queue worker / Horizon must be running to deliver it; under `QUEUE_CONNECTION=sync` it still sends inline.

---

## Part C — Template docs layout (informational; no fork code change)

v5.1.3 also reorganized the **template's own** lineage docs: its changelog and migration guides now live in a top-level **`.template/`** directory (previously `CHANGELOG.md` at the repo root + `docs/migrations/`), with **`.template/README.md`** defining the changelog + versioning rules.

This is template-repo housekeeping — **a fork applies no code change for it.** What it means for your fork:

- The repo-root **`CHANGELOG.md` is now a stub for your fork's own product changelog** — use it (or your existing one) for your app's releases. It's independent of the template's history.
- You still track which template version you're on via **`template-manifest.json`** (`version`) at the repo root — never by copying `.template/CHANGELOG.md`.
- If your fork happened to carry a copy of the template's changelog/migration docs, move them under `.template/` to match — or just delete them and read the upstream repo when you need a guide.

---

## Part D — Sync check: template + Docker managed core

While upgrading, confirm the fork hasn't **drifted** from the template on files the template owns. The goal is to catch *unintended* divergence — your project's own customizations (the documented knobs + your product code) are expected and explicitly excluded.

Compare against a checkout of the template at the version you're moving to (`v5.1.3`).

**1. Docker managed core** — these must match the template; only the knobs may differ.

| Must match the template | Allowed to differ (your knobs) |
|---|---|
| `Dockerfile` build stages, base image, `CMD` | base image **tag** (PHP version), build command (`build` vs `build-ssr`), `pnpm`/`npm` |
| `docker/config/nginx.conf`, `docker/config/php.ini` | which `docker/config/supervisord.conf` process blocks are enabled |
| `docker/deploy/entrypoint.sh`, `pre-deployment.sh` | env values; the migration lines inside `post-deployment.sh` |
| `.dockerignore` | — |

```sh
# from the fork, against a template checkout at v5.1.3 (TPL=path/to/template):
for f in Dockerfile .dockerignore docker/config/nginx.conf docker/config/php.ini \
         docker/deploy/entrypoint.sh docker/deploy/pre-deployment.sh; do
  diff "$f" "$TPL/$f" && echo "  ✓ $f in sync"
done
```

A clean diff (after allowing for the knobs above) = Docker core in sync. Anything left over is **drift**: reconcile it back to the template, or — if it's a genuine improvement — promote it to the template and bump the template version (don't leave it fork-only). Full boundary: [`docker/README.md`](../../docker/README.md) → "Drift policy & versioning" and `AGENTS.md` → "Optional Docker / Deployment".

**2. Docker README + manifest are current.** Confirm `docker/README.md` → "This project's setup" and `template-manifest.json` → `docker` still describe the app (`php` / `pkg` / `build` / `baseImage` / `requires`). These are the fork's own declarations — keep them, just make sure they're accurate.

**3. `AGENTS.md`.** The shared stub ships with every clone and should track the template's baseline rules — v5.1.3 slimmed the tenancy + Docker sections to short pointers and dropped the "Template version tracking" section. Reconcile your `AGENTS.md` against the template's: pull in the updated shared sections, but **keep your project-specific additions** (a fork legitimately extends `AGENTS.md` with its own conventions).

**4. Other template-managed files.** For non-Docker scaffolding the template ships (auth middleware, base services, UI primitives, tooling configs), a fork legitimately customizes for its product — **don't blanket-revert**. Only realign files you did *not* intend to change that have fallen behind the template; your custom revisions stay.

> Drift is rare for a patch like v5.1.3 — this step just guards against a fork having quietly edited managed core where it should have used a knob.

---

## Verify

```sh
php artisan config:clear
php artisan cache:clear
php artisan queue:failed   # sanity — Redis reachable, no error

pnpm check:php             # Pint, Larastan, Pest — all green
```

Then, on tenancy-enabled forks, send a test invite and confirm the email lands via the worker (check Horizon's *Recent Jobs*), not inline.

## Finally

Bump the fork's `template-manifest.json` → `"version": "5.1.3"`. Don't copy this changelog into the fork — the manifest `version` is the record (see the template's [`.template/README.md`](../README.md)).
