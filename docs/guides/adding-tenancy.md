# Guide: Add multi-tenancy

**When to use:** Your product needs isolated workspaces — many customers, each with their own data — and
you have decided the template's single-tenant default is not enough.
**Prerequisites:** A green `pnpm check`, and PostgreSQL or MySQL (SQLite cannot do
database-per-tenant). Read *Decide first* before running anything.

> **This is not template-managed.** Tenancy was removed in v6.0.0. What you build here is yours —
> template upgrades will not touch it, and you maintain it.

---

## Decide first

Two decisions, both expensive to reverse once you have customers.

### Path vs subdomain

| Mode | URL shape | Auth complexity | Operational cost |
|---|---|---|---|
| **Path** (recommended) | `app.example.com/t/acme/dashboard` | **Low.** One domain, one session cookie, no special config. | None — works on any domain you already serve. |
| **Subdomain** | `acme.example.com/dashboard` | **Higher.** The session cookie must be scoped to `.example.com` to carry across subdomains; cross-subdomain CSRF; signed URLs need host rewriting. | Wildcard DNS (`*.example.com`) **and** a wildcard TLS certificate. |

**Path mode is permanently viable.** Choose subdomains for a marketing reason, never because you
assume you will outgrow paths.

### One tenant per user, or many

| Pattern | When | What you build |
|---|---|---|
| **Single** | One personal workspace per signup, no team invites | Attach the user to their tenant at signup; login redirects straight in. |
| **Multiple** | Users join several workspaces (teams, agencies, marketplaces) | Same pivot, several rows per user; login routes through a picker when a user has 2+. |

Both use the same `tenant_user` pivot, so this is not an install-time switch. Build for single; the
move to multiple is additive.

## Steps

### 1. Install the package

```bash
composer require stancl/tenancy:^3.10
php artisan tenancy:install
```

`tenancy:install` publishes `config/tenancy.php`, a `TenancyServiceProvider`, the tenants/domains
migrations, and `routes/tenant.php`. Add `TenancyServiceProvider::class` to `bootstrap/providers.php`.

### 2. Split the migrations

Central and tenant migrations live in separate directories. `mkdir -p database/migrations/tenant`,
move the published `tenants`/`domains` migrations into `database/migrations/central/`, and load that
directory from `TenancyServiceProvider::boot()`:

```php
$this->loadMigrationsFrom(database_path('migrations/central'));
```

Point the package at the tenant directory in `config/tenancy.php`:

```php
'migration_parameters' => [
    '--path' => [database_path('migrations/tenant')],
    '--realpath' => true,
],
```

**Which table goes where** — settle this before writing a migration:

| Category | Examples | Goes |
|---|---|---|
| Auth & identity | `users`, `sessions`, `password_reset_tokens`, `personal_access_tokens` | Central |
| Cross-cutting infra | `cache`, `jobs`, `failed_jobs`, `job_batches`, `migrations` | Central |
| Billing (Cashier) | `subscriptions`, `invoices` | Central |
| Domain data | `projects`, `tasks`, `orders` — anything a customer owns | Tenant |
| App config | `settings` (per-customer → tenant; global → central), `feature_flags` | Depends |

Commit that list to your fork; it is the most-consulted decision in a tenanted codebase.

### 3. Pin the central models

Any model that must always read the central database — `User` above all — needs an explicit
connection, or it silently follows whatever tenant context is active:

```php
// app/Models/Concerns/CentralConnection.php — then `use CentralConnection;` on User
trait CentralConnection
{
    public function getConnectionName(): ?string
    {
        return config('tenancy.database.central_connection', config('database.default'));
    }
}
```

Apply it to everything in the central list above. Add the tenant relationship and the `tenant_user`
pivot migration (`user_id`, `tenant_id`, `role`, `joined_at`).

### 4. Add the central connection

Copy your `pgsql`/`mysql` block in `config/database.php`, rename it `pgsql_central`, drive it from
`DB_CENTRAL_*`, and set `tenancy.database.central_connection` to it. In development it may point at
the same database; in production give it its own.

### 5. Wire routes and middleware

Tenant routes live in `routes/tenant.php`, registered from the provider. A path-mode group:

```php
Route::middleware([
    'web',
    InitializeTenancyByPath::class,
    'auth:sanctum',
    EnsureUserBelongsToTenant::class,
])->prefix('t/{tenant}')->group(function () {
    // tenant-scoped routes
});
```

`EnsureUserBelongsToTenant` is yours to write: check the pivot, 403 otherwise. **Write it before the
first tenant route** — identification proves which tenant was asked for, not that the caller is
entitled to it. That is the whole security boundary.

Login, registration and password reset stay on the **central** domain.

### 6. Run the central migrations

```bash
php artisan migrate --database=pgsql_central
```

### 7. Provision and use a tenant

| State | Means | Set by |
|---|---|---|
| `$tenant->isReady()` | The per-tenant database exists and is migrated | A listener at the end of the `TenantCreated` pipeline |
| `$tenant->hasFailed()` | Provisioning errored | Your own `markFailed($reason)` on a `TenantCreationFailed` listener |
| `$tenant->trashed()` | Soft-deleted; the database still exists | Eloquent `SoftDeletes` |

**The race to know about.** A queued creation pipeline (`shouldBeQueued(true)`, which you want in
production) redirects the new user to their tenant *before* the database exists. Guard the route
group with a readiness middleware returning 503 + `Retry-After` while `isReady()` is false. Skip
this and every production signup is a race you sometimes lose.

**Deletion:** `delete()` soft-deletes and keeps the database; `forceDelete()` drops it. Put a grace
window between them — a scheduled purge of tenants soft-deleted over N hours ago — so an accidental
deletion is recoverable.

`tenant()` returns the current tenant or `null`; `tenancy()->initialize($tenant)` / `->end()` enter
and leave context by hand, which is what commands, jobs and tests need. Queries in a tenant-routed
request are scoped; queries anywhere else are not.

### 8. Add the tenancy test suite

**A green default run proves nothing about tenant code:** `phpunit.xml` has no tenant context, so
tenant routes 404 and tenant tables are never touched. Add `phpunit.tenancy.xml` with tenancy
enabled, base test cases that initialise and tear down context, and a script:

```json
"check:tenancy": "php -d memory_limit=512M ./vendor/bin/pest --configuration=phpunit.tenancy.xml",
"check:all": "pnpm check && pnpm check:tenancy"
```

Run `check:all` — not `check` — before committing anything tenant-scoped, and as its own CI job.

### 9. Adopt it on existing data

1. **Categorize every existing table** using the table above. Write the list down.
2. **Audit every `App\Models\User` reference.** Foreign keys cannot span databases — drop those
   constraints, denormalize to `central_user_id`, keep integrity in the application.
3. **Write the migrator** — create a tenant, provision, copy that account's rows, attach the pivot.
   Make it **idempotent and resumable**; it will fail partway at least once.
4. **Dry-run against a restored production copy** until the audit log is clean for every user. This
   is the step people skip and regret.
5. **Staging**, full run, app up. Then **production cutover** in maintenance mode.
6. **Write the rollback plan before cutover** — central untouched, tenant databases droppable, a
   tested path back.

## What this does not cover

Cashier billing stays central (verify, don't assume), OAuth tokens need tenant scoping, and the
multi-workspace UX — picker, invites, in-session switcher — is entirely yours.

## Verify

```sh
php artisan tinker --execute="var_dump(config('tenancy.enabled'));"   # true
php artisan migrate:status --database=pgsql_central                   # central tables Ran
php artisan route:list | grep -c 't/{tenant}'                         # > 0
pnpm check:all                                                        # both suites green
```

Then request a tenant path as a user who does **not** belong to it. A 403 is
`EnsureUserBelongsToTenant` working; a 200 means identification is wired and authorization is not.

## Pitfalls

| Symptom | Fix |
|---|---|
| A `Tenant` query from central context returns nothing | Tenancy is not initialized there. `tenancy()->initialize($tenant)` first, or move the query into a tenant-routed request. |
| Queue jobs hit the wrong database | `QueueTenancyBootstrapper` is missing from `bootstrappers` in `config/tenancy.php`. |
| Broadcasts leak across tenants | `routes/channels.php` channel names must include `tenant()?->id`. |
| A tenant-scoped change passes CI but breaks in production | You ran the default suite. Run the tenancy suite (step 8). |
| A new tenant model errors about a missing or duplicate table | Its inferred table name collides with a central/framework table. Set an explicit `protected $table`. |
| Users see another tenant's data | Identification without authorization. `EnsureUserBelongsToTenant` (step 5) is missing or not on that route group. |
| Session lost when moving between subdomains | `SESSION_DOMAIN` must be `.example.com`, not the bare host. |

Package documentation: <https://tenancyforlaravel.com/docs/v3/>.
