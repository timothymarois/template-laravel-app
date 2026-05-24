# Tenancy — Using It

This guide is for an agent (or human) setting up multi-tenancy in a fresh template clone or in a fork that just upgraded to v5.0.0 and wants to enable tenancy.

If your app already has user data, **stop here and read [`tenancy-migrating.md`](./tenancy-migrating.md) instead** — that guide covers the data migration concerns this guide doesn't.

## What this does

`template-laravel-app` ships [`stancl/tenancy ^3.10`](https://tenancyforlaravel.com/docs/v3/) installed but inert. This guide flips it on. After completing the steps below, your app will:

- Authenticate users against a central DB. The existing `App\Models\User` model carries a `CentralConnection` trait that pins it to the central connection **automatically** when tenancy is enabled — no model swap required.
- Route per-tenant requests to per-tenant databases (DB-per-tenant isolation).
- Default to **path mode** — tenant URLs are `/t/{tenant}/...` on your existing domain. No wildcard DNS or wildcard SSL needed.
- Be one env flip away from subdomain mode (`<tenant>.example.com`) when you're ready for it.

## Prerequisites

- Fork is on template `v5.0.0` or later (`jq -r .version template-version.json` → `5.0.0`).
- A reachable Postgres or MySQL instance for the central DB (can be the same instance your fork already uses).
- The fork's `pnpm check` is green at baseline.

## Step 1 — Enable tenancy

Run the one-shot enable command:

```bash
php artisan tenancy:enable
```

The command:
1. Confirms you want to proceed (use `--force` to skip).
2. Writes two keys to `.env`:
   - `TENANCY_ENABLED=true`
   - `TENANCY_IDENTIFICATION=path`
3. Prints "next steps" reminding you to configure the central DB and run migrations.

**Verify:**
```bash
grep -E '^TENANCY_ENABLED|^TENANCY_IDENTIFICATION' .env
```
→ must show both keys set.

```bash
php artisan tinker --execute='echo (new App\Models\User)->getConnectionName();'
```
→ must print your central connection name (e.g. `pgsql_central`). When tenancy is enabled the `CentralConnection` trait on `User` activates — auth queries always hit the central DB even inside a tenant request.

## Step 2 — Configure the central DB connection

Two options:

**Option A — Reuse the current default connection as the central connection** (simplest; recommended for local dev):

Set `DB_CENTRAL_*` env vars to match your existing `DB_*` vars:
```bash
DB_CENTRAL_CONNECTION=pgsql_central
DB_CENTRAL_HOST="${DB_HOST}"
DB_CENTRAL_PORT="${DB_PORT}"
DB_CENTRAL_DATABASE="${DB_DATABASE}"
DB_CENTRAL_USERNAME="${DB_USERNAME}"
DB_CENTRAL_PASSWORD="${DB_PASSWORD}"
```

**Option B — Separate central DB** (recommended for production):

Provision a dedicated DB (e.g. `myapp_central`) and set the `DB_CENTRAL_*` vars to point at it.

Then uncomment the `pgsql_central` connection block in `config/database.php` and confirm it reads from the `DB_CENTRAL_*` env vars.

**Verify:**
```bash
php artisan tinker --execute='echo config("database.connections.pgsql_central.database");'
```
→ must print the central DB name you configured (not blank, not the tenant default).

## Step 3 — Run central migrations

```bash
php artisan migrate --database=pgsql_central
php artisan migrate --database=pgsql_central --path=database/migrations/central
```

The first command runs the four root migrations (`users`, `cache`, `jobs`, `personal_access_tokens`) against the central DB. The second runs the tenancy-specific migrations (`tenants`, `domains`).

**Verify:**
```bash
php artisan migrate:status --database=pgsql_central
```
→ must show all six migrations as `Ran`.

## Step 4 — Provision your first tenant

```bash
php artisan tenancy:provision acme --owner=you@example.com
```

The command:
1. Creates a `Tenant` row with a UUID id.
2. Creates a `Domain` row with `domain = "acme"` (which becomes the URL slug in path mode).
3. Fires `TenantCreated`, which triggers the package's `CreateDatabase` → `MigrateDatabase` pipeline. The pipeline:
   - Creates a new database named `tenant_<uuid>` on your central connection.
   - Runs every migration in `database/migrations/tenant/` against it.
4. Prints the tenant URL.

**Verify:**
```bash
php artisan tinker --execute='echo App\Models\Tenant::count();'
```
→ must print `1`.

```bash
psql -l | grep '^ tenant_'
```
→ must show one database named `tenant_<uuid>`.

> **Production note — async provisioning.** The stock `TenantCreated` pipeline runs synchronously (`shouldBeQueued(false)` in `app/Providers/TenancyServiceProvider.php`). For production, you may want to flip it to `true` so HTTP signup requests don't block on `CreateDatabase` + `MigrateDatabase`. That introduces a window where `tenant()` resolves but its DB is unmigrated — your first-request UX must handle it. Two options: (a) keep provisioning synchronous and accept slower signup, or (b) add a `ready` boolean column to `tenants` + an `EnsureTenantReady` middleware that redirects unready tenants to a "still provisioning..." loading page. Invelo's `app/Http/Middleware/TenantIsReady.php` is the worked example.

## Step 5 — Visit the tenant

Open in your browser:
```
http://template-laravel-app.test/t/acme/
```

(Replace `template-laravel-app.test` with whatever host your fork serves on.)

The example route at `routes/tenant.php` returns `"This is your multi-tenant application. The id of the current tenant is <uuid>"`. Replace this route with your real tenant dashboard.

## Step 6 — Add tenant-scoped tables

Put new migrations in `database/migrations/tenant/`. They run against every tenant database.

```bash
php artisan make:migration create_projects_table --path=database/migrations/tenant
```

Then for every tenant:
```bash
php artisan tenants:migrate
```

Or for a specific tenant:
```bash
php artisan tenants:migrate --tenants=<uuid>
```

## Step 7 — Using tenancy in code

**Use `tenant_user()` instead of `auth()->user()` for tenant-side user lookups:**
```php
$user = tenant_user();  // App\Models\Tenant\User instance when in tenant context
// or
$centralUser = central_user();  // App\Models\User instance — always central-pinned via the CentralConnection trait
```

**Use `tenant()` to read tenant metadata:**
```php
$name = tenant('data')['name'] ?? 'Untitled';
$tenantId = tenant()?->id;
```

**Use `tenant_url()` to build cross-tenant URLs in path mode:**
```php
$url = tenant_url('/dashboard', 'acme');
// path mode → http://template-laravel-app.test/t/acme/dashboard
```

For URLs **inside** the current tenant request, `route()` and `url()` work as usual — the URL prefix is already part of the request context.

## Step 8 — Tests

For central-side tests (e.g. account picker, billing, OAuth flow):
```php
class AccountPickerTest extends \Tests\CentralBaseTestCase
{
    public function test_picker_lists_owned_tenants(): void
    {
        // User, Tenant, pivot rows here all live in the central DB
    }
}
```

For tenant-side tests (e.g. project CRUD, tenant-scoped features):
```php
class ProjectControllerTest extends \Tests\TenantBaseTestCase
{
    public function test_creates_a_project(): void
    {
        // $this->tenant is initialized; all queries hit the tenant DB
    }
}
```

Run them:
```bash
php artisan test --testsuite=Central
php artisan test --testsuite=Tenant
```

## Step 9 (optional) — Switch to subdomain mode

Path mode is the default because it requires no DNS / SSL changes. Switch to subdomain mode when wildcard DNS + SSL are ready.

**Prerequisites:**
- Wildcard DNS record: `*.example.com` pointing at your app servers.
- Wildcard SSL cert covering `*.example.com`.
- Operations team aware.

**Env flips:**
```bash
TENANCY_IDENTIFICATION=subdomain
TENANCY_CENTRAL_DOMAINS=app.example.com,example.com
SESSION_DOMAIN=.example.com
```

Restart the app. Validate by visiting `https://acme.example.com/` — the same content that was at `/t/acme/` now lives at the subdomain.

The shared session cookie scoped to `.example.com` keeps users signed in across the central domain and every tenant subdomain.

## Common gotchas

| Symptom | Fix |
|---|---|
| "I queried a Tenant model from a central context and got an empty result." | Tenancy isn't initialized in that context. Either use `tenancy()->initialize($tenant)` first, or move the query inside a tenant-routed request. |
| Queue jobs run against the wrong DB. | Confirm `Stancl\Tenancy\Bootstrappers\QueueTenancyBootstrapper::class` is in the `bootstrappers` list in `config/tenancy.php`. |
| Broadcasts leak across tenants. | Update `routes/channels.php` so channel names include `tenant()?->id`. |
| `tenant_url('/foo')` returns the wrong URL. | The helper uses the tenant's key as the URL slug. For pretty domain-based URLs in subdomain mode, use the package's `tenant_route($domain, $route)` instead. |
| `EnsureTenantReady`, `EnsureUserBelongsToTenant`, `RecordTenantVisit` middleware referenced in production-grade docs don't exist. | They're not shipped in v5.0.0 — they're fork-specific (depend on `ready` column / pivot tables you decide on). See the Rundesk Phase 1 PRD for one production example. |

## Going deeper

- Package docs: <https://tenancyforlaravel.com/docs/v3/>
- Production-scale example with multi-account UX, OAuth, creator attribution, import-from-monolith: `multi-tenancy-phase-1-foundation.md` through `multi-tenancy-phase-4-cutover.md` in the `rundesk-web-app` repo.
- Migration guide for existing apps with data: [`tenancy-migrating.md`](./tenancy-migrating.md).
