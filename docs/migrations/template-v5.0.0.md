# Migrating a fork to template v5.0.0

v5.0.0 bundles **three independent upgrades**. Apply them as ordered, self-contained chunks — each has its own official upgrade guide to read first, its own steps, and its own verification gate. Do not bump `template-version.json` until every chunk you apply is green under `pnpm check`.

> **For agents:** read the linked official upgrade guide for a chunk **before** touching files. This template's notes tell you what *we* changed; your fork may have added code the official guide covers that we don't. The official guide is the source of truth for anything beyond what's listed here.

## Migration chunks

| # | Chunk | What changes | Read this first | Required? |
|---|-------|--------------|-----------------|-----------|
| **1** | [Dependencies + Laravel 12 → 13](#chunk-1-dependency-refresh-laravel-12-13) | composer/npm refresh; `laravel/framework ^13`, `laravel/tinker ^3` | **https://laravel.com/docs/13.x/upgrade** | Yes |
| **2** | [Inertia 2 → 3](#chunk-2-inertia-2-3) | `inertiajs/inertia-laravel ^3` + `@inertiajs/vue3 ^3`; config + blade restructure | **https://inertiajs.com/upgrade-guide** (select v3) | Yes |
| **3** | [Optional multi-tenancy](#chunk-3-optional-multi-tenancy) | `stancl/tenancy` scaffolding, inert by default | this guide (below) + `docs/guidelines/tenancy-using.md` | Optional |

Chunks 1 and 2 are framework/library upgrades every fork must take to stay on v5. Chunk 3 is opt-in — a fork that doesn't want tenancy still applies its file copies (Track A) but never enables it. Chunks are independent: you can land 1, verify, commit; land 2, verify, commit; then decide on 3.

## Global prerequisites

- Fork's `template-version.json` is at `4.5.0` (apply earlier migrations first).
- `pnpm check` is green on the baseline before you start.
- PHP **8.4+** (Laravel 13 floor is 8.3; this template targets 8.4).
- You have read-write access to the fork repo and can run `composer`, `pnpm`, `php artisan`.

---

## Chunk 1 — Dependency refresh + Laravel 12 → 13

**Read first:** the official [Laravel 12 → 13 upgrade guide](https://laravel.com/docs/13.x/upgrade).

This chunk refreshes every in-range composer/npm dependency to its latest patch/minor, then bumps the framework from 12 to 13. For **this template** the L13 jump is mechanical — we grepped every documented L13 breaking change and found **zero touchpoints**: no `VerifyCsrfToken`/`PreventRequestForgery` references, no `Inertia::lazy()`, no `->upsert()`, no `JobAttempted`/`QueueBusy` listeners, no published `pagination::` views. **Your fork may differ** — the official guide lists everything; check it against your own code (especially custom cache-serialized classes, queue event listeners, and any `upsert()` calls).

### 1.1 — Bump composer constraints

- [ ] In `composer.json` `require`, set `"laravel/framework": "^13.0"`.
- [ ] In `composer.json` `require`, set `"laravel/tinker": "^3.0"` (L13 requires Tinker 3).
- [ ] **`soloterm/solo`** (dev-only terminal multiplexer): its latest tagged release (`v0.5.0`) caps at Laravel 12. Its `main` branch supports L13 but is not yet tagged, so set `"soloterm/solo": "dev-main"` in `require-dev` **as a temporary pin**. Revert to a stable `^0.x` constraint once soloterm tags an L13 release. (Solo gracefully no-ops when absent — `config/solo.php` guards on `class_exists`, so a fork that prefers to simply drop solo until it tags can remove it instead.)
- [ ] Leave everything else alone — `spatie/laravel-sitemap` (resolves to `^7.4`, which added L13 support — **no v8 major needed**), `phpunit` (**stays `^12`** — Pest 4 requires PHPUnit 12; do **not** bump to 13), `pest ^4`, etc. all update in-range.

### 1.2 — Resolve and reformat

- [ ] Update the lock file, allowing transitive upgrades:
  ```bash
  composer update -W
  ```
- [ ] **Pint preset shifted** (1.27 → 1.29 now enforces `fully_qualified_strict_types` broadly). Re-format the whole tree:
  ```bash
  ./vendor/bin/pint
  ```
  This touches many pre-existing files cosmetically — expected, not a regression. Commit it as a formatting pass.
- [ ] Refresh npm dev tooling in-range (Vitest, ESLint, etc. — no majors here):
  ```bash
  pnpm update
  ```

### 1.3 — Fix Larastan findings in existing code

Larastan got stricter with the framework bump. One pre-existing template file needs editing (your fork may hit others in its own code — fix the underlying cause, never baseline/ignore them):

- [ ] `app/Http/Concerns/InertiaDataTableOptions.php` — `$this->filterCasts ?? null` is dead (`filterCasts` is a non-nullable `array` on the consuming controller). Drop the `?? null` and the now-always-true `if`; cast unconditionally. The template also adds a class-level docblock documenting the consumer-provided properties (`$filterCasts` required, `$indexDefaults`/`$sessionStoreKeys` optional) — copy it if you want the same discoverability; it's inert otherwise.

> The other finding the template fixed — `nullsafe.neverNull` on `$route?->forgetParameter('tenant')` in `app/Http/Middleware/Tenancy/InitializeTenancyBySlug.php` — lives in a **new** tenancy file you only copy in Chunk 3, so it's already fixed in the version you'll copy. No action here.

### 1.4 — Verify chunk 1

- [ ] `pnpm check:php` is green (Pint, Larastan, Pest).
- [ ] `php artisan --version` prints `Laravel Framework 13.x`.
- [ ] **Pest 4.7 empty-suite behavior changed:** running a testsuite *in isolation* that contains no test files now exits `1` ("No tests found"). If your fork runs an isolated empty suite anywhere (a CI step, a script), add `--do-not-fail-on-empty-test-suite` to that command. (This is why the template's `tenancy-enabled.yml` Tenant step — see Chunk 3 — carries that flag: `tests/Tenant/` is an intentionally-empty placeholder.) Running suites together (e.g. `pnpm check:tenancy`) is unaffected, since tests are still found overall.
- [ ] Optional cosmetic note for deploy: L13 hyphenates cache-prefix and session-cookie names. Flush cache and expect users to re-login once after deploy. No code change required.

---

## Chunk 2 — Inertia 2 → 3

**Read first:** the official [Inertia.js upgrade guide](https://inertiajs.com/upgrade-guide) (select **v3**). It covers both the Laravel server adapter and the Vue client adapter.

Inertia 3 is a coordinated server + client major. v3 requires Laravel 11+ and PHP 8.2+ (satisfied after chunk 1). For **this template** the surface is small and contained; the steps below are exactly what we changed.

### 2.1 — Bump the packages

- [ ] `composer.json` `require`: set `"inertiajs/inertia-laravel": "^3.0"`.
- [ ] `package.json` `devDependencies`: set `"@inertiajs/vue3": "^3.0.0"`.
- [ ] Resolve:
  ```bash
  composer update -W
  pnpm install
  ```

### 2.2 — Restructure `config/inertia.php`

v3 reshapes the config. Easiest path: republish the stock v3 config and re-apply your customizations.
```bash
php artisan vendor:publish --provider="Inertia\\ServiceProvider" --force
```
Then reconcile against the template's `config/inertia.php`. Key changes from v2:

- [ ] `ensure_pages_exist`, `page_paths`, `page_extensions` move **under a new `pages` key** (as `ensure_pages_exist`, `paths`, `extensions`).
- [ ] `testing` is simplified to just `ensure_pages_exist`.
- [ ] **Remove `use_script_element_for_initial_page`** — gone in v3 (initial page data always ships via a `<script type="application/json">` element now).
- [ ] New keys you can keep at defaults: `ssr.runtime`, `ssr.ensure_runtime_exists`, `ssr.throw_on_error`, `expose_shared_prop_keys`.
- [ ] Keep the template's customizations: `declare(strict_types=1)`, the `INERTIA_SSR_HOST`/`INERTIA_SSR_PORT`-composed `ssr.url`, vue-only `pages.extensions`, and `history.encrypt`.
- [ ] If your `.env`/`.env.example` set `INERTIA_USE_SCRIPT_ELEMENT_FOR_INITIAL_PAGE`, delete that key (no longer read).

### 2.3 — Fix the blade head + clear caches

- [ ] `resources/views/app.blade.php`: the head element marker attribute changed. Rename `<title inertia>` → `<title data-inertia>`. (Verified against the installed v3 core: it queries `title:not([data-inertia])`.) Vue `<Head>` components need no change — the library manages their attributes internally.
- [ ] Clear compiled views + config (the `@inertia` directive output and the config both changed):
  ```bash
  php artisan view:clear && php artisan config:clear
  ```

### 2.4 — Check the v3 client breaking changes against your fork

The template uses **none** of the following, so we changed nothing — but **grep your fork** and migrate any hits (see the official guide for each):

- [ ] `router.cancel()` → `router.cancelAll()`.
- [ ] Global events renamed: `invalid` → `httpException`, `exception` → `networkError`.
- [ ] `hideProgress()` / `revealProgress()` named exports → `progress.hide()` / `progress.reveal()`.
- [ ] Remove any `future: { ... }` block from `createInertiaApp` (the options are always-on in v3).
- [ ] Axios is no longer bundled by Inertia core. The template imports `axios` directly for background calls (it stays a dependency) — if your fork relied on Inertia's internal axios instance/interceptors, wire your own.
- [ ] `useForm` now resets `processing`/`progress` in `onFinish` (slightly longer processing window). Behavioral, not breaking.

### 2.5 — Verify chunk 2

- [ ] `pnpm check:js` is green (ESLint, Stylelint, Vitest).
- [ ] `pnpm build && pnpm exec vite build --ssr` both succeed — the SSR build is the real Inertia 3 integration test.
- [ ] Inertia feature tests (`assertInertia`) pass under `pnpm check:php`.

---

## Chunk 3 — Optional multi-tenancy

> Chunk 3 is opt-in. A fork that doesn't want tenancy still applies the file copies below (Track A) so it stays structurally aligned with the template, but never runs `tenancy:enable`. Tracks B/C/D are only for forks adopting tenancy. See the [Track summary](#track-summary) at the end.

### 3.0 — Tenancy overview

v5.0.0 adds **optional multi-tenancy scaffolding** via `stancl/tenancy ^3.10`. The scaffolding is shipped but **inert by default** — a fork that pulls v5.0.0 and changes nothing in `.env` sees zero runtime behavior change. Enabling tenancy is a deliberate per-fork action (`php artisan tenancy:enable`), not a side-effect of this upgrade.

**Most forks should follow Track A below.** Tracks B, C, D are only for forks actually adopting tenancy.

**What this release adds (additive only):**
- The `stancl/tenancy ^3.10` composer dependency.
- New `App\Models\Tenant`, `App\Models\Domain`, `App\Models\TenantInvite`.
- New `App\Models\Concerns\CentralConnection` trait, applied to `App\Models\User` (the trait is a no-op when tenancy is disabled).
- New `App\Tenancy\Bootstrappers\SignedUrls` (only used in subdomain mode; commented out in config).
- New `App\Tenancy\Contracts\ExistingDataMigrator` interface + `NullExistingDataMigrator` default.
- New `App\Providers\TenancyServiceProvider` (registered, but its `boot()` short-circuits when `TENANCY_ENABLED` is falsy).
- New helpers in `app/helpers.php`: `tenant_user()`, `central_user()`, `current_actor()`, `tenant_url()`. (The package autoloads its own `tenant()`, `tenant_route()`, `tenant_asset()`, etc.)
- Three new artisan commands: `tenancy:enable`, `tenancy:provision`, `tenancy:migrate-existing`.
- New `config/tenancy.php` (env-driven; `enabled` defaults false).
- New `database/migrations/central/` and `database/migrations/tenant/` folders (empty, gitkeeped; only scanned when tenancy is enabled).
- New `routes/tenant.php` (loaded only when tenancy is enabled).
- New `tests/CentralBaseTestCase.php`, `tests/TenantBaseTestCase.php` (skip automatically when tenancy is disabled).
- New `tests/Feature/Tenancy/DisabledStateTest.php`, `EnableCommandTest.php`, `MigrateExistingCommandTest.php` (~30 tests guarding the inert contract + the .env mutator + the skeleton command).
- New `Central` and `Tenant` suites in `phpunit.xml`.
- New `pgsql_central` and `mysql_central` connection blocks in `config/database.php` (loaded but driven by empty env vars — harmless until tenancy is enabled).
- New env keys in `.env.example` (under a clearly-marked OPTIONAL section).

**What this release modifies (3 small edits to existing files):**
- `app/Models/User.php` — adds the `CentralConnection` trait. **The trait is a no-op when `TENANCY_ENABLED=false`** (it returns null, equivalent to Eloquent's default).
- `app/Http/Middleware/HandleInertiaRequests.php` — adds a `tenancyShared()` branch that returns `[]` when no tenant is active.
- `bootstrap/providers.php` — appends `App\Providers\TenancyServiceProvider`.

**What stays exactly as-is:**
- `config/auth.php` — completely untouched. Same as v4.5.0.
- `routes/api.php`, `routes/channels.php`, `routes/components.php` — unchanged.
- `App\Models\User` is still the auth model (the trait makes it central-aware *only when tenancy is enabled*).
- Every existing service, controller, middleware, command unchanged.

**What changes:**
- `routes/web.php` — gains a tenancy-gated `/invites/*` block. Inert when `TENANCY_ENABLED=false`.
- `database/migrations/0001_01_01_000000_create_users_table.php` — gains a `role` string column on the canonical migration. Existing forks must add the column via a one-off migration (see Group D.5) — running `migrate:fresh` against the upgraded fork would re-create from the new base, but most production forks won't `migrate:fresh`.
- `app/Models/User.php`, `app/Http/Middleware/HandleInertiaRequests.php`, `app/Providers/AppServiceProvider.php`, `bootstrap/providers.php`, `database/factories/UserFactory.php`, `phpunit.xml`, `.env.example`, `composer.json` — each gains additions (no removals).

### 3.1 — Tenancy prerequisites

- Chunks 1 and 2 are applied and green (fork is on Laravel 13 / Inertia 3).
- `pnpm check` is green on the post-chunk-2 baseline.
- You have read-write access to the fork repo.

### 3.2 — Tenancy apply order

Work top-down. Run `pnpm check:php` after each group. Bump `template-version.json` only after the final verification passes.

### Group A — Add the package

- [ ] Add `"files": ["app/helpers.php"]` to the `autoload` block in `composer.json` (next to the existing `psr-4` block).
- [ ] Install the package:
  ```bash
  composer require stancl/tenancy:^3.10
  ```
  Composer adds the entry to `require`, downloads the package, regenerates the lock file, and runs `dump-autoload` automatically.
- [ ] Verify:
  ```bash
  composer show stancl/tenancy | grep -E '^(name|versions)'
  ```
  → must print `name : stancl/tenancy` and `versions : * v3.10.0` (or higher in the 3.x line).

### Group B — Copy the new files

Copy each path verbatim from the template at v5.0.0. If a target directory doesn't exist in the fork, create it.

**Enums:**
- [ ] `app/Enums/TenantRole.php` (per-tenant role: Owner/Admin/Member + capability methods)
- [ ] `app/Enums/UserRole.php` (central role: SuperAdmin/User backed by `'admin'`/`'user'`)

**Models:**
- [ ] `app/Models/Concerns/CentralConnection.php`
- [ ] `app/Models/Domain.php`
- [ ] `app/Models/Tenant.php`
- [ ] `app/Models/TenantInvite.php` (pending tenant invitations)

**Services + contracts + tenancy:**
- [ ] `app/helpers.php`
- [ ] `app/Tenancy/Contracts/ExistingDataMigrator.php`
- [ ] `app/Tenancy/NullExistingDataMigrator.php`
- [ ] `app/Tenancy/Bootstrappers/SignedUrls.php`
- [ ] `app/Services/Tenancy/TenantInviteService.php` (send/accept/decline/revoke invitations)
- [ ] `app/Services/Tenancy/TenantMembershipService.php` (switch/leave/remove/changeRole/transferOwnership)
- [ ] `app/Services/Tenancy/TenantProvisioningService.php` (the actual provisioning logic — `ProvisionCommand` is a thin caller)

**Providers + commands:**
- [ ] `app/Providers/TenancyServiceProvider.php`
- [ ] `app/Console/Commands/Tenancy/EnableCommand.php`
- [ ] `app/Console/Commands/Tenancy/ProvisionCommand.php`
- [ ] `app/Console/Commands/Tenancy/MigrateExistingCommand.php`
- [ ] `app/Console/Commands/Tenancy/PurgeDeletedCommand.php` (force-deletes soft-deleted tenants past the grace window)

**HTTP layer:**
- [ ] `app/Http/Controllers/Tenancy/InviteController.php`
- [ ] `app/Http/Middleware/Tenancy/InitializeTenancyBySlug.php` (path-mode resolver — looks up tenant by domain slug)
- [ ] `app/Http/Middleware/Tenancy/EnsureTenantReady.php` (503 for tenants whose provisioning hasn't completed)
- [ ] `app/Http/Middleware/Tenancy/EnsureUserBelongsToTenant.php` (403 for non-members; redirect to login for guests)
- [ ] `app/Notifications/Tenancy/TenantInvitationNotification.php` (invite email)

**Listeners** (in `app/Tenancy/Listeners/` — intentionally outside `App\Listeners\` to avoid Laravel 11+ auto-discovery):
- [ ] `app/Tenancy/Listeners/MarkTenantReady.php` (flips `tenant->ready=true` after the pipeline succeeds)
- [ ] `app/Tenancy/Listeners/ConditionalDeleteTenantDatabase.php` (drops per-tenant DB on hard delete only — preserves soft-delete recovery)

**Config + routes:**
- [ ] `config/tenancy.php`
- [ ] `routes/tenant.php`

**Migrations:**
- [ ] `database/migrations/central/.gitkeep`
- [ ] `database/migrations/central/2019_09_15_000010_create_tenants_table.php`
- [ ] `database/migrations/central/2019_09_15_000020_create_domains_table.php`
- [ ] `database/migrations/central/2026_05_24_000010_create_tenant_user_table.php` (multi-tenant access pivot)
- [ ] `database/migrations/central/2026_05_24_000020_create_tenant_invites_table.php` (pending invites)
- [ ] `database/migrations/tenant/.gitkeep`

**Tests:**
- [ ] `tests/CentralBaseTestCase.php`
- [ ] `tests/TenantBaseTestCase.php`
- [ ] `tests/Central/.gitkeep`
- [ ] `tests/Central/EnabledStateSmokeTest.php`
- [ ] `tests/Central/MultiTenantAccessTest.php`
- [ ] `tests/Central/ProvisionCommandTest.php`
- [ ] `tests/Central/TenantInviteServiceTest.php`
- [ ] `tests/Central/TenantMembershipServiceTest.php`
- [ ] `tests/Central/InviteControllerTest.php`
- [ ] `tests/Central/SignedUrlsBootstrapperTest.php`
- [ ] `tests/Central/PathModeRoutingTest.php` (integration tests for InitializeTenancyBySlug + EnsureUserBelongsToTenant)
- [ ] `tests/Central/TenantProvisioningServiceTest.php` (service-level coverage for the extracted provisioning logic)
- [ ] `tests/Central/TenantLifecycleTest.php` (ready/failed flags, soft vs hard delete, purge-deleted command)
- [ ] `tests/Tenant/.gitkeep`
- [ ] `tests/Feature/Tenancy/DisabledStateTest.php`
- [ ] `tests/Feature/Tenancy/EnableCommandTest.php`
- [ ] `tests/Feature/Tenancy/MigrateExistingCommandTest.php`
- [ ] `tests/Unit/Enums/TenantRoleTest.php`
- [ ] `tests/Unit/Enums/UserRoleTest.php`
- [ ] `tests/Unit/Models/TenantInviteTest.php`
- [ ] `tests/Unit/Notifications/TenantInvitationNotificationTest.php`
- [ ] `tests/Unit/Tenancy/NullExistingDataMigratorTest.php`

**CI + docs:**
- [ ] `phpunit.tenancy.xml`
- [ ] `docs/guidelines/tenancy-using.md`
- [ ] `docs/guidelines/tenancy-migrating.md`
- [ ] `docs/migrations/template-v5.0.0.md` (this file)
- [ ] `.github/workflows/tenancy-enabled.yml`

**Gitignore update:**
- [ ] Append `/database/tenant*` to `.gitignore` so per-tenant SQLite files don't leak into git.

Verify all files landed:
```bash
ls app/helpers.php app/Models/{Tenant,Domain,TenantInvite}.php app/Models/Concerns/CentralConnection.php app/Tenancy/{Contracts/ExistingDataMigrator,NullExistingDataMigrator}.php app/Tenancy/Bootstrappers/SignedUrls.php app/Tenancy/Listeners/{MarkTenantReady,ConditionalDeleteTenantDatabase}.php app/Providers/TenancyServiceProvider.php app/Services/Tenancy/{TenantProvisioningService,TenantInviteService,TenantMembershipService}.php app/Http/Middleware/Tenancy/{InitializeTenancyBySlug,EnsureTenantReady,EnsureUserBelongsToTenant}.php app/Http/Controllers/Tenancy/InviteController.php app/Notifications/Tenancy/TenantInvitationNotification.php app/Console/Commands/Tenancy/{Enable,Provision,MigrateExisting,PurgeDeleted}Command.php config/tenancy.php routes/tenant.php tests/{Central,Tenant}BaseTestCase.php tests/Feature/Tenancy/{DisabledState,EnableCommand,MigrateExistingCommand}Test.php
```
→ no errors; every path exists.

### Group C — Modify `bootstrap/providers.php`

- [ ] Append `App\Providers\TenancyServiceProvider::class` to the providers array.
- [ ] Verify:
  ```bash
  grep -n 'TenancyServiceProvider' bootstrap/providers.php
  ```
  → must show one match.

### Group D — Modify `app/Models/User.php`

- [ ] Add the new imports:
  ```php
  use App\Enums\UserRole;
  use App\Models\Concerns\CentralConnection;
  use Illuminate\Database\Eloquent\Relations\BelongsToMany;
  ```
- [ ] Add `CentralConnection` to the `use` line inside the class body (alphabetical: `use CentralConnection, HasApiTokens, HasFactory, Notifiable;`).
- [ ] Add `'role'` to the `$fillable` array (alongside `is_active`).
- [ ] Add `'role' => UserRole::class` to the `casts()` return array (alongside `is_active`).
- [ ] Add the `tenants()` relationship method (copy the whole method block verbatim from the template's `app/Models/User.php`). This is the central side of the `tenant_user` pivot.
- [ ] Verify:
  ```bash
  grep -n "CentralConnection\|tenants()\|'role'\|UserRole" app/Models/User.php
  ```
  → must show at least 6 matches (import + use line + fillable entry + casts entry + relationship method + import of UserRole).

  ```bash
  php artisan tinker --execute='echo (new App\Models\User)->getConnectionName() === null ? "OK" : "FAIL";'
  ```
  → must print `OK`. When `TENANCY_ENABLED=false`, the trait returns null — Eloquent default behavior, no query routing change.

### Group D.5 — Schema change: add `role` column to existing `users` tables

v5.0.0 changes the **template's** canonical `0001_01_01_000000_create_users_table.php` to include a `role` string column (backing `App\Enums\UserRole`). **Fork's own copy of that migration is NOT modified** by this guide — fork migrations are immutable once shipped. Instead, add the column with a one-off migration below.

> **Skipping this group is a hard runtime crash.** Group D adds `'role' => UserRole::class` to `User::casts()`. The first `User` query against a DB without the `role` column will throw `SQLSTATE[42S22]: Column not found`. Don't skip.

> **`migrate:fresh` warning.** Your fork's `migrate:fresh` runs YOUR copy of `0001_01_01_000000_create_users_table.php` (without `role`), then this group's new migration (which adds `role`). Both states converge on the same schema. If you ever want to consolidate the schema, you can manually edit your fork's copy of the base migration to match the template — but only after you've also run `migrate:fresh` in production, which is rarely a good idea.

- [ ] Create the migration:
  ```bash
  php artisan make:migration add_role_to_users_table
  ```
- [ ] Replace the generated body with:
  ```php
  use App\Enums\UserRole;
  use Illuminate\Database\Migrations\Migration;
  use Illuminate\Database\Schema\Blueprint;
  use Illuminate\Support\Facades\Schema;

  return new class extends Migration
  {
      public function up(): void
      {
          Schema::table('users', function (Blueprint $table): void {
              $table->string('role')->default(UserRole::User->value)->after('is_active');
          });
      }

      public function down(): void
      {
          Schema::table('users', function (Blueprint $table): void {
              $table->dropColumn('role');
          });
      }
  };
  ```
- [ ] Run the migration:
  ```bash
  php artisan migrate
  ```
- [ ] Verify:
  ```bash
  php artisan tinker --execute='echo Schema::hasColumn("users", "role") ? "OK" : "FAIL";'
  ```
  → must print `OK`.

- [ ] (Optional) Promote any current admins:
  ```bash
  php artisan tinker --execute='\App\Models\User::where("email","you@example.com")->update(["role" => \App\Enums\UserRole::SuperAdmin->value]);'
  ```

- [ ] Update `database/factories/UserFactory.php` to include `role` in the default state (copy from template). Add `use App\Enums\UserRole;` to the imports and `'role' => UserRole::User` to the returned array. Also add the helper:
  ```php
  public function admin(): static
  {
      return $this->state(fn (array $attributes) => [
          'role' => UserRole::SuperAdmin,
      ]);
  }
  ```
  Without this, `User::factory()->make()` (without persisting) returns a model with `role = null`, which breaks any code calling `$user->role->canManageAllTenants()`.

### Group D.7 — Add invite routes to `routes/web.php`

- [ ] At the top of `routes/web.php`, add the controller import:
  ```php
  use App\Http\Controllers\Tenancy\InviteController;
  ```
- [ ] Append the tenancy-gated invite route block (place it near the public routes section):
  ```php
  if (config('tenancy.enabled')) {
      Route::middleware(['throttle:60,1'])->group(function (): void {
          Route::get('invites/{token}', [InviteController::class, 'show'])->name('invites.show');
          Route::post('invites/{token}/accept', [InviteController::class, 'accept'])->name('invites.accept');
          Route::post('invites/{token}/decline', [InviteController::class, 'decline'])->name('invites.decline');
      });
  }
  ```
- [ ] Verify:
  ```bash
  grep -n 'InviteController\|invites/{token}' routes/web.php
  ```
  → must show 4+ matches (1 import + 3 route definitions).

  ```bash
  php artisan tinker --execute='echo \Illuminate\Support\Facades\Route::has("invites.show") ? "FAIL (route exists in disabled state)" : "OK";'
  ```
  → must print `OK`. When tenancy is disabled the routes are not registered.

### Group E — Modify `app/Http/Middleware/HandleInertiaRequests.php`

- [ ] Inside the `share()` method, spread a new `tenancyShared()` call into the returned array:
  ```php
  return array_merge(parent::share($request), [
      'user' => $request->user(),
      ...$this->tenancyShared(),
  ]);
  ```
- [ ] Add the `tenancyShared()` method (copy from the template).
- [ ] Verify:
  ```bash
  grep -n 'tenancyShared' app/Http/Middleware/HandleInertiaRequests.php
  ```
  → must show at least 2 matches (one call site + the method definition).

### Group F — Modify `app/Providers/AppServiceProvider.php`

- [ ] In `register()`, conditionally bind `NullExistingDataMigrator`:
  ```php
  if (config('tenancy.enabled')) {
      $this->app->bind(
          \App\Tenancy\Contracts\ExistingDataMigrator::class,
          \App\Tenancy\NullExistingDataMigrator::class,
      );
  }
  ```
- [ ] Verify:
  ```bash
  grep -n 'ExistingDataMigrator' app/Providers/AppServiceProvider.php
  ```
  → must show two matches.

### Group G — Modify `.env.example`

- [ ] Append the new env keys (copy the `Optional: Multi-tenancy` block from the template's `.env.example`):
  ```
  # ====================================================================
  # Optional: Multi-tenancy
  # ====================================================================
  # ...
  TENANCY_ENABLED=false
  TENANCY_IDENTIFICATION=path
  TENANCY_CENTRAL_DOMAINS=
  TENANCY_PURGE_DELETED_AFTER_HOURS=72

  DB_CENTRAL_CONNECTION=pgsql_central
  DB_CENTRAL_HOST=
  DB_CENTRAL_PORT=
  DB_CENTRAL_DATABASE=
  DB_CENTRAL_USERNAME=
  DB_CENTRAL_PASSWORD=
  ```
- [ ] Verify:
  ```bash
  grep -c '^TENANCY_\|^DB_CENTRAL_' .env.example
  ```
  → must report `10` (4 TENANCY_* + 6 DB_CENTRAL_*).

### Group H — Modify `config/database.php`

- [ ] Add the two new connection blocks (`pgsql_central` and `mysql_central`) inside the `connections` array. Copy verbatim from the template. They're driven by `DB_CENTRAL_*` env vars and only connect when those vars are populated.
- [ ] Verify:
  ```bash
  grep -nE "'(pgsql|mysql)_central'" config/database.php
  ```
  → must show two matches.

### Group I — Modify `phpunit.xml`

- [ ] Add `Central` and `Tenant` testsuites:
  ```xml
  <testsuite name="Central">
      <directory>tests/Central</directory>
  </testsuite>
  <testsuite name="Tenant">
      <directory>tests/Tenant</directory>
  </testsuite>
  ```
- [ ] Add the force env override inside the `<php>` block:
  ```xml
  <env name="TENANCY_ENABLED" value="false" force="true"/>
  ```
- [ ] Verify:
  ```bash
  grep -c 'testsuite name="Central"\|testsuite name="Tenant"' phpunit.xml
  ```
  → must report `2`.

### Group J — Validation

- [ ] Run the full check suite:
  ```bash
  pnpm check
  ```
  → must exit 0. All existing tests + the new `DisabledStateTest` (~22 cases), `EnableCommandTest` (~7 cases), and `MigrateExistingCommandTest` (~3 cases) pass.

- [ ] Confirm migrations are unchanged:
  ```bash
  php artisan migrate:status
  ```
  → must show the **same four migrations** as before (`create_users_table`, `create_cache_table`, `create_jobs_table`, `create_personal_access_tokens_table`). No tenancy migrations.

- [ ] Confirm the tenancy package is installed:
  ```bash
  composer show stancl/tenancy | grep -E '^(name|versions)'
  ```
  → must print `name : stancl/tenancy` and `versions : * v3.10.x`.

  ```bash
  php artisan list tenancy
  ```
  → must list **at least** `tenancy:enable`, `tenancy:migrate-existing`, `tenancy:provision` (the three commands the template ships) plus several `tenants:*` and `tenancy:install` commands that the **package** ships. **Track A operators should ignore the package-shipped commands** — they're for runtime tenant management once tenancy is enabled. Do NOT run `tenancy:install` (it republishes the package's stock provider on top of the template's customized one and dumps fresh migrations into `database/migrations/` root, breaking the disabled-state contract).

  ```bash
  php artisan tinker --execute='echo tenant() === null ? "OK" : "FAIL";'
  ```
  → must print `OK`.

  ```bash
  php artisan tinker --execute='echo \Illuminate\Support\Facades\Event::hasListeners(\Stancl\Tenancy\Events\TenantCreated::class) ? "FAIL" : "OK";'
  ```
  → must print `OK`.

  ```bash
  php artisan tinker --execute='echo app()->bound(\App\Tenancy\Contracts\ExistingDataMigrator::class) ? "FAIL" : "OK";'
  ```
  → must print `OK`.

### Group K — AGENTS.md + CHANGELOG + version bump

- [ ] Copy the `## Optional multi-tenancy` block from the template's `AGENTS.md` into this fork's `AGENTS.md`. Forks need this guidance so their agents know not to wander into tenancy code when tenancy is disabled, and which guides to read when it's enabled.
- [ ] Append the template's `v5.0.0` entry from `CHANGELOG.md` to this fork's `CHANGELOG.md` (verbatim or trimmed to the fork's customer-facing voice — at minimum a one-line entry naming the dependency bump and pointing at this migration guide).
- [ ] Update `template-version.json`:
  ```json
  {
      "template": "template-laravel-app",
      "repo": "https://github.com/timothymarois/template-laravel-app",
      "version": "5.0.0",
      "updated": "YYYY-MM-DD"
  }
  ```
- [ ] Update the row for this fork in `Workspace/projects/template-tracking.md` (workspace-level concern, do this in a separate workspace commit).

## Failure modes & recovery

- **`composer update` reports conflicts.** Apply chunk 1 first — `stancl/tenancy ^3.10` and the rest of the v5 stack assume Laravel 13. A fork still on Laravel 12 should complete chunks 1 and 2 before attempting chunk 3.
- **`pnpm check` fails on Pint.** Run `./vendor/bin/pint` to auto-fix; re-run.
- **`pnpm check` fails on phpstan.** Most common cause is missing return types or strict-types declaration on the copied files. Verify each new file has `declare(strict_types=1);` and every method has a return type.
- **DisabledStateTest fails with "tenancy.enabled is not falsy".** Check phpunit.xml has `<env name="TENANCY_ENABLED" value="false" force="true"/>`; the `force="true"` attribute is required.
- **`Inertia shared props do not include currentTenant` test fails.** The `tenancyShared()` call in `HandleInertiaRequests` was misplaced. Diff against the template.
- **`User::getConnectionName()` returns 'sqlite' instead of null when tenancy is disabled.** The `CentralConnection` trait wasn't updated — check that it has the `if (! config('tenancy.enabled')) return null;` short-circuit.
- **Accidentally ran `tenancy:enable`.** The command only writes env keys — no DB changes, no migrations. To revert: open `.env` and set `TENANCY_ENABLED=false` (or just delete that line). The `TENANCY_IDENTIFICATION` key can stay; it has no effect when enabled is false. Restart any running workers / dev servers to pick up the env change.
- **Accidentally ran `tenancy:install`** (the package's own scaffolding command, not ours). This republishes the package's stock provider over the template's customized `app/Providers/TenancyServiceProvider.php` and may dump fresh tenancy migrations into `database/migrations/` root. Recovery: `git checkout -- app/Providers/TenancyServiceProvider.php database/migrations/`. If the migrations were committed, manually delete the package's `2019_09_15_*` files at the root (the template's copies live under `database/migrations/central/` — those are correct).

## You're done

With chunks 1 and 2 applied, the fork is on **Laravel 13 + Inertia 3** with all dependencies refreshed. With chunk 3 applied (Track A), tenancy scaffolding is installed but disabled — nothing else changes in the fork. If you want to enable tenancy, see [`docs/guidelines/tenancy-using.md`](../guidelines/tenancy-using.md). If you're adopting tenancy on an existing app with user data, see [`docs/guidelines/tenancy-migrating.md`](../guidelines/tenancy-migrating.md).

Bump `template-version.json` to `5.0.0` only after `pnpm check` is green for every chunk you applied.

## Track summary

| Track | Audience | What's involved |
|---|---|---|
| **A** | Fork doesn't want tenancy (most forks) | Apply Groups A–K above. ~15 minutes. No DB changes. No code refactors. Tenancy code installed but inert. |
| **B** | New project wants tenancy from day one | Track A, then `php artisan tenancy:enable`. See `tenancy-using.md`. |
| **C** | Existing project with user data wants tenancy | Track A + tenancy:enable + follow `tenancy-migrating.md`. |
| **D** | Already has tenancy (e.g., Invelo) | Track A only. Optional alignment per `tenancy-migrating.md` § "Aligning an existing tenancy implementation" later. |
