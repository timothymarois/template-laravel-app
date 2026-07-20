# Guide: Enable and use multi-tenancy

**When to use:** Setting up multi-tenancy in a fresh template clone, or a fork that just upgraded to v5.0.0 and wants tenancy on. If your app **already has user data**, stop and use [`tenancy-migrations.md`](./tenancy-migrations.md) instead — it covers the data-migration concerns this guide doesn't.
**Prerequisites:**
- Fork is on template `v5.0.0`+ (`jq -r .version template-manifest.json` → `5.0.0`).
- A reachable Postgres or MySQL instance for the central DB (can be the instance your fork already uses).
- The fork's `pnpm check` is green at baseline.

## What this does

`template-laravel-app` ships [`stancl/tenancy ^3.10`](https://tenancyforlaravel.com/docs/v3/) installed but inert. This guide flips it on. After the steps below your app will:

- Authenticate users against a central DB. `App\Models\User` carries a `CentralConnection` trait that pins it to the central connection automatically when tenancy is enabled — no model swap.
- Route per-tenant requests to per-tenant databases (DB-per-tenant isolation).
- Support **multi-tenant access**: one user can belong to many tenants via the `tenant_user` pivot (`User::tenants()` / `Tenant::users()` ship with the template).
- Default to **path mode** — tenant URLs are `/t/{tenant}/...` on your existing domain. No wildcard DNS/SSL needed.
- Be one env flip away from subdomain mode (`<tenant>.example.com`) when ready.

## Disabled by default — the contract

`TENANCY_ENABLED=false` is the default. **While disabled, treat tenancy code as nonexistent** — a fork not using tenancy must behave identically to a plain Laravel app:

- Don't import `App\Models\Tenant` or `App\Models\Domain`. (`App\Models\User` is fine — its `CentralConnection` trait is a no-op when disabled.)
- Don't run `tenancy:install` or any `tenants:*` command. `tenancy:install` republishes the package's stock provider over the template's customized one and dumps migrations into `database/migrations/` root — it breaks this contract. The only commands safe while disabled are `tenancy:enable` and `tenancy:provision --help` / `tenancy:migrate-existing --help` (help text only — invoking them fails with "Tenancy is disabled").
- Don't call `tenant_user()`, `central_user()`, `current_actor()`, `tenant_url()` in user-facing code (they fall back to `auth()->user()` / `url()` when disabled — safe but pointless).
- Don't add tenant middleware/routes to `routes/web.php` / `routes/api.php`, and don't register tenancy event listeners.

The guard is `App\Providers\TenancyServiceProvider::boot()`, which short-circuits on `config('tenancy.enabled')`. `tests/Feature/Tenancy/DisabledStateTest.php` enforces this contract — don't weaken those tests. Once tenancy is enabled, never run bare `php artisan test` — use `--testsuite=Central` and `--testsuite=Tenant` separately so the test DBs don't collide.

## Choose your identification mode

Two orthogonal axes — pick each separately.

### Path vs subdomain — auth complexity tradeoff

| Mode | URL shape | Auth complexity | Operational cost |
|---|---|---|---|
| **Path** (default) | `app.example.com/t/acme/dashboard` | **Low.** Session cookie covers the whole app — same domain, same cookie. No special config. | None — works on any domain you already serve. |
| **Subdomain** | `acme.example.com/dashboard` | **Higher.** Session cookie must be scoped to `.example.com` so it carries across subdomains. Cross-subdomain CSRF. Signed URLs need host rewriting (the bundled `SignedUrls` bootstrapper handles this — uncomment it in `config/tenancy.php` when you flip the mode). | Wildcard DNS (`*.example.com`) + wildcard SSL cert. |

**Path mode is permanent-viable.** There is no "graduation" requirement — choose subdomain only for a marketing reason (vanity URLs, white-labeling), not a technical one.

### Single-tenant-per-user vs multi-tenant-per-user

| Pattern | When to use | What you build |
|---|---|---|
| **Single tenant per user** | One personal workspace per signup. No team invites. | Attach the user to their tenant on signup; one row per user in `tenant_user`. Login redirects straight to the tenant. |
| **Multi-tenant per user** | Users join multiple workspaces (team invites, agency, marketplace). | Same pivot, multiple rows per user. Login routes through a picker when the user has 2+ tenants. |

Both use the same `tenant_user` pivot. You don't choose at install time — your code decides whether to attach a user to multiple tenants.

## Step 1 — Enable tenancy

```bash
php artisan tenancy:enable
```

Confirms (use `--force` to skip), writes `TENANCY_ENABLED=true` and `TENANCY_IDENTIFICATION=path` to `.env`, and prints next steps.

**Verify:**
```bash
grep -E '^TENANCY_ENABLED|^TENANCY_IDENTIFICATION' .env    # both keys set
php artisan tinker --execute='echo (new App\Models\User)->getConnectionName();'
```
The tinker call must print your central connection name (e.g. `pgsql_central`). When enabled, the `CentralConnection` trait on `User` activates — auth queries always hit the central DB even inside a tenant request.

## Step 2 — Configure the central DB connection

**Option A — reuse the current default connection** (simplest; recommended for local dev). Set `DB_CENTRAL_*` to match your existing `DB_*`:
```bash
DB_CENTRAL_CONNECTION=pgsql_central
DB_CENTRAL_HOST="${DB_HOST}"
DB_CENTRAL_PORT="${DB_PORT}"
DB_CENTRAL_DATABASE="${DB_DATABASE}"
DB_CENTRAL_USERNAME="${DB_USERNAME}"
DB_CENTRAL_PASSWORD="${DB_PASSWORD}"
```

**Option B — separate central DB** (recommended for production). Provision a dedicated DB (e.g. `myapp_central`), set `DB_CENTRAL_*` to it, then uncomment the `pgsql_central` connection block in `config/database.php` and confirm it reads the `DB_CENTRAL_*` vars.

**Verify:**
```bash
php artisan tinker --execute='echo config("database.connections.pgsql_central.database");'
```
Must print the central DB name (not blank, not the tenant default).

## Step 3 — Run central migrations

```bash
php artisan migrate --database=pgsql_central
```

Runs the four root migrations (`users`, `cache`, `jobs`, `personal_access_tokens`) and the three tenancy migrations (`tenants`, `domains`, `tenant_user`) against the central DB. The tenancy migrations are auto-discovered because `TenancyServiceProvider::boot()` calls `loadMigrationsFrom(database_path('migrations/central'))` whenever `TENANCY_ENABLED=true`.

**Verify:**
```bash
php artisan migrate:status --database=pgsql_central
```
Must show all seven migrations as `Ran` (4 root + 3 central).

## Tenant lifecycle — ready, failed, deleted

| Method | Returns | Set by |
|---|---|---|
| `$tenant->isReady()` | `true` once `CreateDatabase` + `MigrateDatabase` finished | `MarkTenantReady` listener at the end of the `TenantCreated` pipeline |
| `$tenant->hasFailed()` | `true` if provisioning errored | Fork-side: `$tenant->markFailed($reason)` from a `TenantCreationFailed` listener |
| `$tenant->trashed()` | `true` after `$tenant->delete()` (soft delete) | Eloquent's `SoftDeletes` |

**Registration UX — loader while provisioning.** If you flip the package's `JobPipeline` to `shouldBeQueued(true)` for production (in `TenancyServiceProvider`), the signing-up user is redirected to their tenant BEFORE the per-tenant DB exists. Pattern:

```php
// In your RegisterController after creating the tenant:
$tenant = app(TenantProvisioningService::class)->provision($name, $slug, $email);

return redirect()->away(tenant_url('/', $tenant));
// → Browser loads /t/{slug}/
// → InitializeTenancyBySlug resolves tenant
// → EnsureTenantReady middleware checks isReady()
// → if false: returns 503 with Retry-After: 30
// → Vue page shows a loader, retries every 30s
// → eventually MarkTenantReady fires → isReady() = true → page loads
```

The `EnsureTenantReady` middleware (in `app/Http/Middleware/Tenancy/`) ships ready. Add it to your tenant route group:

```php
Route::middleware([
    'web',
    InitializeTenancyBySlug::class,
    EnsureTenantReady::class,         // ← guards against unready tenants
    'auth',
    EnsureUserBelongsToTenant::class,
])->prefix('t/{tenant}')->group(function () { ... });
```

For the loader page itself, set up a polling Vue page hitting an unauthenticated `/t/{slug}/ready` endpoint returning JSON status. Fork-specific UI.

### Tenant deletion (soft vs hard)

- `$tenant->delete()` — soft delete. Sets `deleted_at`. Per-tenant DB stays intact. Restorable via `$tenant->restore()`.
- `$tenant->forceDelete()` — hard delete. Per-tenant DB is dropped permanently (via `ConditionalDeleteTenantDatabase` listener).
- `php artisan tenancy:purge-deleted` — force-deletes soft-deleted tenants older than `config('tenancy.purge_deleted_after_hours')` (default 72). Schedule it (`routes/console.php` for Laravel 11+):

  ```php
  Schedule::command('tenancy:purge-deleted')->hourly();
  ```

  The grace window lets operators recover an accidentally-deleted tenant before its DB is permanently gone. After purging, the command also sweeps **orphan users** — users who were members of the just-purged tenants and now belong to zero tenants. SuperAdmins (`role = admin`) are exempt. Bystander users who were never members of any purged tenant are NOT touched. `--keep-orphan-users` skips the sweep; `--dry-run` previews both.

## Step 4 — Provision your first tenant

```bash
php artisan tenancy:provision acme --owner=you@example.com
```

Creates a `Tenant` row (UUID id) + a `Domain` row (`domain = "acme"`, the URL slug in path mode), fires `TenantCreated` → the package's `CreateDatabase` → `MigrateDatabase` pipeline (creates `tenant_<uuid>` on the central connection and runs every migration in `database/migrations/tenant/`), and prints the tenant URL. The `--owner` also attaches that user to the tenant via `tenant_user` with role `owner`.

**Verify:**
```bash
php artisan tinker --execute='echo App\Models\Tenant::count();'   # → 1
psql -l | grep '^ tenant_'                                        # → one tenant_<uuid> database
```

**Production note — async provisioning.** The stock pipeline runs synchronously (`shouldBeQueued(false)`). For production you may flip it to `true` so signup requests don't block on `CreateDatabase` + `MigrateDatabase` — but that opens a window where `tenant()` resolves before its DB is migrated. Handle it via the `ready` flag + `EnsureTenantReady` middleware (returns 503 + `Retry-After: 30`). Two caveats when flipping `shouldBeQueued(true)`: (1) add `EnsureTenantReady::class` to your tenant route group; (2) **move `MarkTenantReady` into the JobPipeline as the final job**, not a sibling listener on `TenantCreated` — sibling listeners fire after pipeline *dispatch*, not *completion*, which would mark tenants ready before their DB exists. Inline: `JobPipeline::make([CreateDatabase::class, MigrateDatabase::class, MarkTenantReadyJob::class])`.

## Step 5 — Visit the tenant

```
http://template-laravel-app.test/t/acme/
```
(Replace the host with whatever your fork serves on.) The example route at `routes/tenant.php` returns a string with the current tenant's UUID. Replace it with your real tenant dashboard.

## Step 6 — Add tenant-scoped tables

Put new migrations in `database/migrations/tenant/`; they run against every tenant DB.

```bash
php artisan make:migration create_projects_table --path=database/migrations/tenant
php artisan tenants:migrate                        # every tenant
php artisan tenants:migrate --tenants=<uuid>        # a specific tenant
```

**Don't reuse a framework or central table name.** The flat test suite merges tenant and framework
migrations into one database, so a new tenant model whose inferred table collides with a framework/central
table (e.g. `jobs`, the reserved queue table) breaks. Give it an explicit `protected $table` — a `Job`
model mapping to `job_manuals`, not the inferred `jobs`.

## Step 7 — Using tenancy in code

`tenant_user()` and `central_user()` both return the central `App\Models\User`:
```php
$user = tenant_user();   // authenticated central user (current actor inside the tenant)
$user = central_user();  // identical — kept for symmetry / explicitness
```
The auth principal is stored centrally; the `tenant_user` pivot holds membership and role. Read the per-tenant role via `app(\App\Services\Tenancy\TenantMembershipService::class)->roleOf($user, tenant())`.

Read tenant metadata with `tenant()`:
```php
$name = tenant('data')['name'] ?? 'Untitled';
$tenantId = tenant()?->id;
```

Build cross-tenant URLs with `tenant_url()` (path mode):
```php
$url = tenant_url('/dashboard', 'acme');   // → http://template-laravel-app.test/t/acme/dashboard
```
For URLs **inside** the current tenant request, `route()` and `url()` work as usual — the prefix is already in the request context.

## Step 8 — Multi-tenant access (one user, many tenants)

Use the `tenant_user` pivot and `User::tenants()` / `Tenant::users()` whether single- or multi-tenant.

Attach a user (after the account exists):
```php
$tenant->users()->attach($user->id, [
    'role' => 'owner',          // owner | admin | member — customize per fork
    'joined_at' => now(),
]);
```
`tenancy:provision --owner=<email>` does this for the first tenant. Detach: `$tenant->users()->detach($user->id);`

> **Soft-delete gotcha.** `Tenant` uses `SoftDeletes`. `$tenant->delete()` sets `deleted_at` but does **NOT** cascade pivot rows — they're only removed by the FK cascade on `forceDelete()`. To revoke access immediately on soft delete, detach first: `$tenant->users()->detach()` then `$tenant->delete()`. Restoring (`$tenant->restore()`) leaves membership intact.

List a user's tenants:
```php
foreach ($user->tenants as $tenant) {
    echo $tenant->id, ' role=', $tenant->pivot->role, "\n";
}
```

**Login routing** — add to `SessionController::authenticate()` after `$request->authenticate()`:
```php
$tenants = $request->user()->tenants;

if ($tenants->count() === 0) {
    return redirect()->route('onboarding');          // no tenants — onboard
}
if ($tenants->count() === 1) {
    return redirect()->away(tenant_url('/', $tenants->first()));   // jump straight in
}
return redirect()->route('tenants.pick');            // multi — let them pick
```
The single-tenant fast path keeps the common case trivial; the picker only loads when needed.

**The picker page** — the template doesn't ship the UI (Inertia + your design system). The data side:
```php
// app/Http/Controllers/TenantPickerController.php
public function index(Request $request): \Inertia\Response
{
    return Inertia::render('tenants/Pick', [
        'tenants' => $request->user()->tenants->map(fn ($t) => [
            'id' => $t->id,
            'name' => $t->data['name'] ?? $t->id,
            'role' => $t->pivot->role,
            'url' => tenant_url('/', $t),
        ]),
    ]);
}
```
The Vue side renders one link per tenant pointing at `tenant_url('/', $tenant)`. No tenancy code in the picker route itself — it lives on the central domain.

**Switching tenants.** In path mode, "switching" is just navigating to a different `/t/{slug}/`; put a "Switch workspace" link posting to `route('tenants.pick')`. In subdomain mode, the link goes to `https://other-tenant.example.com/`; the `.example.com`-scoped cookie keeps the session alive.

**Authorization inside a tenant** — after tenancy initializes:
```php
$tenant = tenant();
if (! $tenant || ! $tenant->users()->where('user_id', auth()->id())->exists()) {
    abort(403);
}
```
Forks wanting this enforced globally write an `EnsureUserBelongsToTenant` middleware and attach it to the tenant route group in `routes/tenant.php`.

## Step 9 — Tests

Central-side (account picker, billing, OAuth):
```php
class AccountPickerTest extends \Tests\CentralBaseTestCase
{
    public function test_picker_lists_owned_tenants(): void
    {
        // User, Tenant, pivot rows all live in the central DB
    }
}
```
Tenant-side (project CRUD, tenant-scoped features):
```php
class ProjectControllerTest extends \Tests\TenantBaseTestCase
{
    public function test_creates_a_project(): void
    {
        // $this->tenant is initialized; all queries hit the tenant DB
    }
}
```
Run them separately:
```bash
php artisan test --testsuite=Central
php artisan test --testsuite=Tenant
```

## Step 10 (optional) — Switch to subdomain mode

Switch only when wildcard DNS + SSL are ready. **Prerequisites:** wildcard DNS `*.example.com` → your app servers; wildcard SSL covering `*.example.com`; ops aware.

**Env flips:**
```bash
TENANCY_IDENTIFICATION=subdomain
TENANCY_CENTRAL_DOMAINS=app.example.com,example.com
SESSION_DOMAIN=.example.com
```
Restart the app. Validate by visiting `https://acme.example.com/` — the content that was at `/t/acme/` now lives at the subdomain. The `.example.com`-scoped cookie keeps users signed in across the central domain and every tenant subdomain.

## Pitfalls

| Symptom | Fix |
|---|---|
| "I queried a Tenant model from a central context and got an empty result." | Tenancy isn't initialized there. Use `tenancy()->initialize($tenant)` first, or move the query inside a tenant-routed request. |
| Queue jobs run against the wrong DB. | Confirm `Stancl\Tenancy\Bootstrappers\QueueTenancyBootstrapper::class` is in the `bootstrappers` list in `config/tenancy.php`. |
| Broadcasts leak across tenants. | Update `routes/channels.php` so channel names include `tenant()?->id`. |
| `tenant_url('/foo')` returns the wrong URL. | The helper uses the tenant's key as the slug. For pretty domain-based URLs in subdomain mode, use the package's `tenant_route($domain, $route)`. |
| `EnsureTenantReady`, `EnsureUserBelongsToTenant`, `RecordTenantVisit` middleware referenced elsewhere don't exist. | They're not shipped in v5.0.0 — they're fork-specific (depend on a `ready` column / pivot tables you decide on). |
| A tenant-scoped change passes the default suite but breaks in production. | The default `phpunit.xml` runs with tenancy disabled — tenant routes 404 and tenant tables aren't exercised. Run `pnpm check:tenancy` (`phpunit.tenancy.xml`) for anything tenant-scoped. |
| A new tenant model errors in the test suite about a missing/duplicate table. | Its inferred table name collides with a framework/central table in the merged test DB. Set an explicit `protected $table`. |

Package docs: <https://tenancyforlaravel.com/docs/v3/>. For migrating a fork that already has data, see [`tenancy-migrations.md`](./tenancy-migrations.md).
