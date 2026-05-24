# Migrating a fork to template v5.0.0

This guide walks an agent (or human) through applying v5.0.0 to a fork currently at v4.5.0. Every step has a verification command — do not bump `template-version.json` until every checkbox is verified and `pnpm check` is green.

## Overview

v5.0.0 adds **optional multi-tenancy scaffolding** via `stancl/tenancy ^3.10`. The scaffolding is shipped but **inert by default** — a fork that pulls v5.0.0 and changes nothing in `.env` sees zero runtime behavior change. Enabling tenancy is a deliberate per-fork action (`php artisan tenancy:enable`), not a side-effect of this upgrade.

**Most forks should follow Track A below.** Tracks B, C, D are only for forks actually adopting tenancy.

**What this release adds (additive only):**
- The `stancl/tenancy ^3.10` composer dependency.
- New `App\Models\Tenant`, `App\Models\Domain`, `App\Models\Tenant\User`.
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
- All existing migrations stay at `database/migrations/` root. `php artisan migrate` runs them identically.
- `App\Models\User` is still the auth model (the trait makes it central-aware *only when tenancy is enabled*).
- `routes/web.php`, `routes/api.php`, `routes/channels.php`, `routes/components.php` unchanged.
- Every existing service, controller, middleware, command, and test unchanged.

## Prerequisites

- Fork's `template-version.json` is currently at `4.5.0` (or earlier — apply prior migrations first).
- Tests pass on baseline: `pnpm check` is green.
- You have read-write access to the fork repo.
- Fork is on Laravel 12+ / PHP 8.4+ (`composer.json` requires Laravel `^12.0`).

## Apply order

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

- [ ] `app/helpers.php`
- [ ] `app/Models/Concerns/CentralConnection.php`
- [ ] `app/Models/Domain.php`
- [ ] `app/Models/Tenant.php`
- [ ] `app/Models/Tenant/User.php`
- [ ] `app/Tenancy/Contracts/ExistingDataMigrator.php`
- [ ] `app/Tenancy/NullExistingDataMigrator.php`
- [ ] `app/Tenancy/Bootstrappers/SignedUrls.php`
- [ ] `app/Providers/TenancyServiceProvider.php`
- [ ] `app/Console/Commands/Tenancy/EnableCommand.php`
- [ ] `app/Console/Commands/Tenancy/ProvisionCommand.php`
- [ ] `app/Console/Commands/Tenancy/MigrateExistingCommand.php`
- [ ] `config/tenancy.php`
- [ ] `routes/tenant.php`
- [ ] `database/migrations/central/.gitkeep`
- [ ] `database/migrations/central/2019_09_15_000010_create_tenants_table.php`
- [ ] `database/migrations/central/2019_09_15_000020_create_domains_table.php`
- [ ] `database/migrations/central/2026_05_24_000010_create_tenant_user_table.php` (multi-tenant access pivot)
- [ ] `database/migrations/tenant/.gitkeep`
- [ ] `database/migrations/tenant/0001_01_01_000000_create_users_table.php`
- [ ] `tests/CentralBaseTestCase.php`
- [ ] `tests/TenantBaseTestCase.php`
- [ ] `tests/Central/.gitkeep`
- [ ] `tests/Central/EnabledStateSmokeTest.php` (referenced by the CI workflow below)
- [ ] `tests/Tenant/.gitkeep`
- [ ] `tests/Feature/Tenancy/DisabledStateTest.php`
- [ ] `tests/Feature/Tenancy/EnableCommandTest.php`
- [ ] `tests/Feature/Tenancy/MigrateExistingCommandTest.php`
- [ ] `phpunit.tenancy.xml` (alternate phpunit config used by the tenancy-enabled CI job)
- [ ] `docs/guidelines/tenancy-using.md`
- [ ] `docs/guidelines/tenancy-migrating.md`
- [ ] `docs/migrations/template-v5.0.0.md` (this file)
- [ ] `.github/workflows/tenancy-enabled.yml` (CI job for the enabled state — requires `phpunit.tenancy.xml` above)

Verify all files landed:
```bash
ls app/helpers.php app/Models/{Tenant,Domain}.php app/Models/{Concerns/CentralConnection,Tenant/User}.php app/Tenancy/{Contracts/ExistingDataMigrator,NullExistingDataMigrator}.php app/Tenancy/Bootstrappers/SignedUrls.php app/Providers/TenancyServiceProvider.php app/Console/Commands/Tenancy/{Enable,Provision,MigrateExisting}Command.php config/tenancy.php routes/tenant.php tests/{Central,Tenant}BaseTestCase.php tests/Feature/Tenancy/{DisabledState,EnableCommand,MigrateExistingCommand}Test.php
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
  use App\Models\Concerns\CentralConnection;
  use Illuminate\Database\Eloquent\Relations\BelongsToMany;
  ```
- [ ] Add `CentralConnection` to the `use` line inside the class body (alphabetical: `use CentralConnection, HasApiTokens, HasFactory, Notifiable;`).
- [ ] Add the `tenants()` relationship method (copy the whole method block verbatim from the template's `app/Models/User.php`). This is the central side of the `tenant_user` pivot — `User::tenants()` returns the user's tenants when tenancy is enabled, errors gracefully when disabled (the pivot table doesn't exist; calling the relationship is what triggers the query).
- [ ] Verify:
  ```bash
  grep -n 'CentralConnection\|tenants()' app/Models/User.php
  ```
  → must show at least 3 matches (the import + the `use` line + the relationship method).

  ```bash
  php artisan tinker --execute='echo (new App\Models\User)->getConnectionName() === null ? "OK" : "FAIL";'
  ```
  → must print `OK`. When `TENANCY_ENABLED=false`, the trait returns null — Eloquent default behavior, no query routing change.

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
  → must report `9` (3 TENANCY_* + 6 DB_CENTRAL_*).

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

- **`composer update` reports conflicts.** Fork is likely on Laravel < 12 (the package's v3.9+ requires Laravel 10+; we target 12+). Upgrade Laravel first.
- **`pnpm check` fails on Pint.** Run `./vendor/bin/pint` to auto-fix; re-run.
- **`pnpm check` fails on phpstan.** Most common cause is missing return types or strict-types declaration on the copied files. Verify each new file has `declare(strict_types=1);` and every method has a return type.
- **DisabledStateTest fails with "tenancy.enabled is not falsy".** Check phpunit.xml has `<env name="TENANCY_ENABLED" value="false" force="true"/>`; the `force="true"` attribute is required.
- **`Inertia shared props do not include currentTenant` test fails.** The `tenancyShared()` call in `HandleInertiaRequests` was misplaced. Diff against the template.
- **`User::getConnectionName()` returns 'sqlite' instead of null when tenancy is disabled.** The `CentralConnection` trait wasn't updated — check that it has the `if (! config('tenancy.enabled')) return null;` short-circuit.
- **Accidentally ran `tenancy:enable`.** The command only writes env keys — no DB changes, no migrations. To revert: open `.env` and set `TENANCY_ENABLED=false` (or just delete that line). The `TENANCY_IDENTIFICATION` key can stay; it has no effect when enabled is false. Restart any running workers / dev servers to pick up the env change.
- **Accidentally ran `tenancy:install`** (the package's own scaffolding command, not ours). This republishes the package's stock provider over the template's customized `app/Providers/TenancyServiceProvider.php` and may dump fresh tenancy migrations into `database/migrations/` root. Recovery: `git checkout -- app/Providers/TenancyServiceProvider.php database/migrations/`. If the migrations were committed, manually delete the package's `2019_09_15_*` files at the root (the template's copies live under `database/migrations/central/` — those are correct).

## You're done

Tenancy is installed but disabled. Nothing else changes in the fork. If you want to enable tenancy, see [`docs/guidelines/tenancy-using.md`](../guidelines/tenancy-using.md). If you're adopting tenancy on an existing app with user data, see [`docs/guidelines/tenancy-migrating.md`](../guidelines/tenancy-migrating.md).

## Track summary

| Track | Audience | What's involved |
|---|---|---|
| **A** | Fork doesn't want tenancy (most forks) | Apply Groups A–K above. ~15 minutes. No DB changes. No code refactors. Tenancy code installed but inert. |
| **B** | New project wants tenancy from day one | Track A, then `php artisan tenancy:enable`. See `tenancy-using.md`. |
| **C** | Existing project with user data wants tenancy | Track A + tenancy:enable + follow `tenancy-migrating.md`. |
| **D** | Already has tenancy (e.g., Invelo) | Track A only. Optional alignment per `tenancy-migrating.md` § "Aligning an existing tenancy implementation" later. |
