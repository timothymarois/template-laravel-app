# Guide: Add multi-tenancy

**When to use:** Your product needs isolated workspaces — many customers, each with their own data — and
you have decided the template's single-tenant default is not enough.
**Prerequisites:** A green `pnpm check`. A reachable PostgreSQL or MySQL server (SQLite cannot do
database-per-tenant). Read the whole page before running anything: step 1 is a decision you should not
reverse later.

> **This template does not ship multi-tenancy.** It was built in until v6.0.0 and removed there, because
> almost no fork enabled it and every fork carried it. What follows rebuilds it deliberately, in your fork,
> where you own it. Nothing here is template-managed: once you follow this guide, upgrades will not touch
> your tenancy code and you maintain it yourself.

> **Guide budget exception.** This page is deliberately longer than the ~200-line guide budget. Tenancy
> used to be spread over two cross-referencing guides plus a migration document, and the scattering was
> the problem. One page that runs long beats three that disagree — keep it that way.

---

## 1. Choose the shape — before you install anything

Two independent decisions. Both are expensive to reverse once you have customers.

### Path vs subdomain

| Mode | URL shape | Auth complexity | Operational cost |
|---|---|---|---|
| **Path** (recommended) | `app.example.com/t/acme/dashboard` | **Low.** One domain, one session cookie, no special config. | None — works on any domain you already serve. |
| **Subdomain** | `acme.example.com/dashboard` | **Higher.** The session cookie must be scoped to `.example.com` to carry across subdomains; cross-subdomain CSRF; signed URLs need host rewriting. | Wildcard DNS (`*.example.com`) **and** a wildcard TLS certificate. |

**Path mode is permanently viable.** There is no technical graduation to subdomains — choose them for a
marketing reason (vanity URLs, white-labeling), never because you assume you will outgrow paths.

### One tenant per user, or many

| Pattern | When | What you build |
|---|---|---|
| **Single** | One personal workspace per signup, no team invites | Attach the user to their tenant at signup; login redirects straight in. |
| **Multiple** | Users join several workspaces (teams, agencies, marketplaces) | Same pivot, several rows per user; login routes through a picker when a user has 2+. |

Both use the same `tenant_user` pivot, so this is not an install-time switch — it is what your code does
with the pivot. Build for single, and the move to multiple is additive.

---

## 2. Install and wire it

### 2.1 The package

```bash
composer require stancl/tenancy:^3.10
php artisan tenancy:install
```

`tenancy:install` publishes `config/tenancy.php`, a `TenancyServiceProvider`, the tenants/domains
migrations, and `routes/tenant.php`. Register the provider in `bootstrap/providers.php`:

```php
use App\Providers\TenancyServiceProvider;

return [
    AppServiceProvider::class,
    HorizonServiceProvider::class,
    TenancyServiceProvider::class,
];
```

### 2.2 Split the migrations

The package expects central and tenant migrations in separate directories. Laravel's default
`database/migrations/` stays central; add a tenant directory:

```bash
mkdir -p database/migrations/tenant
```

Move the published `tenants` and `domains` migrations into `database/migrations/central/`, and load that
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

**Which table goes where** — get this list right before you write a migration:

| Category | Examples | Goes |
|---|---|---|
| Auth & identity | `users`, `sessions`, `password_reset_tokens`, `personal_access_tokens` | Central |
| Cross-cutting infra | `cache`, `jobs`, `failed_jobs`, `job_batches`, `migrations` | Central |
| Billing (Cashier) | `subscriptions`, `invoices` | Central |
| Domain data | `projects`, `tasks`, `orders` — anything a customer owns | Tenant |
| App config | `settings` (per-customer → tenant; global → central), `feature_flags` | Depends |

Commit that list to your fork. It is the single most-consulted decision in a tenanted codebase.

### 2.3 Pin the central models

Every model that must always read the central database — `User` above all — needs an explicit connection,
or it will silently follow whatever tenant context happens to be active:

```php
// app/Models/Concerns/CentralConnection.php
trait CentralConnection
{
    public function getConnectionName(): ?string
    {
        return config('tenancy.database.central_connection', config('database.default'));
    }
}
```

Apply it to `User` and to anything else in the central list above:

```php
class User extends Authenticatable
{
    use CentralConnection, HasApiTokens, HasFactory, Notifiable;
```

Add the tenant relationship and the pivot migration (`tenant_user`: `user_id`, `tenant_id`, `role`,
`joined_at`) if you chose the many-tenants pattern.

### 2.4 Central database connection

Add a dedicated central connection in `config/database.php` (copy your existing `pgsql`/`mysql` block,
rename it `pgsql_central`, and drive it from `DB_CENTRAL_*` env vars), then set
`tenancy.database.central_connection` to it. In development it is fine to point the central connection at
the same database you already use; in production give it its own.

### 2.5 Routes and middleware

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

`EnsureUserBelongsToTenant` is yours to write — it checks the pivot and 403s otherwise. **Write it before
you write a single tenant route**: without it, tenant identification only proves which tenant was asked
for, not that the caller is entitled to it. That is the whole security boundary.

Login, registration and password reset stay on the **central** domain. A tenant subdomain has no login page
unless you build one.

### 2.6 Run the central migrations

```bash
php artisan migrate --database=pgsql_central
```

---

## 3. Use it

### Provisioning and the lifecycle

| State | Means | Set by |
|---|---|---|
| `$tenant->isReady()` | The per-tenant database exists and is migrated | A listener at the end of the `TenantCreated` pipeline |
| `$tenant->hasFailed()` | Provisioning errored | Your own `markFailed($reason)` on a `TenantCreationFailed` listener |
| `$tenant->trashed()` | Soft-deleted; the database still exists | Eloquent `SoftDeletes` |

**The race worth knowing about.** If you queue the creation pipeline (`shouldBeQueued(true)`, which you
want in production), the signing-up user is redirected to their tenant *before* the database exists. Guard
the tenant route group with a readiness middleware that returns 503 + `Retry-After` while `isReady()` is
false, and show a polling loader. Skip this and every production signup is a race you lose sometimes.

**Deletion:** `delete()` soft-deletes and keeps the database; `forceDelete()` drops it permanently. Put a
grace window between them — a scheduled command that force-deletes tenants soft-deleted more than N hours
ago — so an accidental deletion is recoverable.

### In code

- `tenant()` — the current tenant, or `null` in central context.
- `tenancy()->initialize($tenant)` / `tenancy()->end()` — enter and leave tenant context by hand, which is
  what you need in commands, jobs and tests.
- Queries in a tenant-routed request are already scoped; queries anywhere else are not.

---

## 4. Test it

Tenant behavior needs its own suite. The default `phpunit.xml` has no tenant context, so tenant routes
404 and tenant tables are never touched — **a green default run proves nothing about tenant code.**

Add a second config (`phpunit.tenancy.xml`) with tenancy enabled, base test cases that initialize and tear
down tenant context, and a script to run it:

```json
"check:tenancy": "php -d memory_limit=512M ./vendor/bin/pest --configuration=phpunit.tenancy.xml",
"check:all": "pnpm check && pnpm check:tenancy"
```

Run `check:all` — not `check` — before committing anything tenant-scoped, and run the tenancy suite in CI
as its own job.

---

## 5. Adopting it on an app that already has data

Green-field is the easy case. If you already have customers, the import is the risky part.

1. **Categorize every existing table** using the table above. Write the list down.
2. **Audit every `App\Models\User` reference.** Cross-database foreign keys cannot span databases — drop
   those constraints and denormalize to a `central_user_id` column, keeping integrity in the application.
3. **Write the migrator**: for each existing account, create a tenant, provision its database, copy that
   account's rows across, and attach the user to the pivot. Make it **idempotent and resumable** — it will
   fail partway at least once.
4. **Dry-run against a restored production copy.** Repeat until the audit log is clean for every single
   user. This is the step people skip and regret.
5. **Staging**, full run, with the app up. Then **production cutover** in maintenance mode.
6. **Have a rollback plan written down before cutover** — the central database untouched, per-tenant
   databases droppable, and a tested path back.

What this guide cannot do for you: Cashier billing stays central (verify, don't assume), OAuth tokens need
tenant scoping, and the multi-workspace UX — picker, invites, in-session switcher — is entirely yours.

---

## Pitfalls

| Symptom | Fix |
|---|---|
| A `Tenant` query from central context returns nothing | Tenancy is not initialized there. `tenancy()->initialize($tenant)` first, or move the query into a tenant-routed request. |
| Queue jobs hit the wrong database | `QueueTenancyBootstrapper` is missing from `bootstrappers` in `config/tenancy.php`. |
| Broadcasts leak across tenants | `routes/channels.php` channel names must include `tenant()?->id`. |
| A tenant-scoped change passes CI but breaks in production | You ran the default suite. Run the tenancy suite (§4). |
| A new tenant model errors about a missing or duplicate table | Its inferred table name collides with a central/framework table. Set an explicit `protected $table`. |
| Users see another tenant's data | Identification without authorization. `EnsureUserBelongsToTenant` (§2.5) is missing or not on that route group. |
| Session lost when moving between subdomains | `SESSION_DOMAIN` must be `.example.com`, not the bare host. |

Package documentation: <https://tenancyforlaravel.com/docs/v3/>.
