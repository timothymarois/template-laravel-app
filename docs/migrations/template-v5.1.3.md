# Migrating a fork to template v5.1.3

v5.1.3 aligns the runtime defaults with the mandatory Redis stack and makes the tenant-invite email asynchronous. Patch — no schema, API, or Docker changes. ~2 minutes, mechanical.

- **Every fork:** flip the two config defaults to `redis` and update `.env.example`.
- **Forks with tenancy enabled:** also pick up the `ShouldQueue` change on the invite notification.

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

## Verify

```sh
php artisan config:clear
php artisan cache:clear
php artisan queue:failed   # sanity — Redis reachable, no error

pnpm check:php             # Pint, Larastan, Pest — all green
```

Then, on tenancy-enabled forks, send a test invite and confirm the email lands via the worker (check Horizon's *Recent Jobs*), not inline.

## Finally

Bump the fork's `template-manifest.json` → `"version": "5.1.3"`. Don't copy this changelog into the fork — the manifest `version` is the record (see the template's `AGENTS.md` → Changelog).
