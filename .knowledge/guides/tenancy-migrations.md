# Guide: Migrate an existing app to multi-tenancy

**When to use:** Adopting multi-tenancy in a fork that **already has user data**. If your fork is brand new with no user data, use [`tenancy-usage.md`](./tenancy-usage.md) instead.
**Prerequisites:** Fork at template v5.0.0+; you've read [`tenancy-usage.md`](./tenancy-usage.md) for the basic enable-flow; a full, verified DB backup.

This is a one-time, somewhat-manual, recoverable process. The template automates env, central DB setup, tenant provisioning, and the import-loop scaffolding. What's necessarily manual is schema-specific decision-making — only your fork knows which tables are tenant-scoped, what your User model is used for, and how to remap FKs.

## Automated vs. manual

| Automated by template | Manual per-fork |
|---|---|
| Writing `.env` keys (`tenancy:enable`) | Deciding which tables are tenant-scoped |
| Configuring central DB connection | Implementing `App\Tenancy\Contracts\ExistingDataMigrator` |
| Running central migrations | Auditing existing tests for cross-DB assumptions |
| Pinning `App\Models\User` central (auto via trait) | The per-user iteration loop body |
| Provisioning tenants (`tenancy:provision`) | Backups + the actual cutover runbook |
| The `tenancy:migrate-existing` command shell | |
| Audit log format spec | |

## Pre-migration checklist

- [ ] Fork is at template v5.0.0+ (apply `.template/migrations/template-v5.0.0.md` first).
- [ ] You've read `tenancy-usage.md` for the enable-flow.
- [ ] Full backup of the current DB taken and verified (`pg_restore --list` on the dump).
- [ ] If production: maintenance window scheduled, status page draft prepared, on-call paged.
- [ ] If S3 / object storage holds per-user paths, snapshot or version those too.
- [ ] No code-freeze concerns (multi-PR change; coordinate with the team).

## Step 1 — Enable tenancy

```bash
php artisan tenancy:enable
```
Then complete Steps 2 and 3 of `tenancy-usage.md` — configure the central DB connection and run central migrations. **Stop before Step 4 (provisioning)** — you don't provision your first tenant manually; the import command does it for every existing user.

## Step 2 — Categorize your tables

Decide which tables go central (auth, infra, cross-cutting) vs. per-tenant (domain data):

| Category | Examples | Goes to |
|---|---|---|
| Auth & identity | `users`, `sessions`, `password_reset_tokens`, `personal_access_tokens`, `oauth_*` | Central |
| Cross-cutting infra | `cache`, `jobs`, `failed_jobs`, `job_batches`, `migrations` | Central |
| Domain data | `projects`, `tasks`, `posts`, `orders` — anything user-owned | Tenant |
| App config | `settings` (per-user → Tenant; global → Central), `feature_flags` (usually Central) | Depends |
| Billing | `subscriptions`, `invoices` (Cashier) | Central |

Make the list explicit in a doc you commit to the fork (`docs/tenancy-table-plan.md`).

## Step 3 — Audit `App\Models\User` references

**`App\Models\User` becomes central-pinned automatically** via the `CentralConnection` trait once tenancy is enabled. Most code importing `User` keeps working — auth lookups, FK joins on central tables, mail notifications all route to the central DB correctly.

The one case needing attention: **FK columns on tenant-scoped tables.** If a tenant-scoped table like `projects` has a `user_id` pointing at `users.id`, that FK won't resolve at the DB level because `users` lives in the central DB and `projects` in each per-tenant DB.

The template's pattern: **denormalize.** Drop the cross-DB FK constraint, keep the column as a plain `unsignedBigInteger` (rename to `central_user_id` for clarity), and resolve to `User` via a manual relation:

```php
// Migration (in database/migrations/tenant/):
$table->unsignedBigInteger('central_user_id')->index();
// Note: no ->constrained() — that table lives in a different DB.

// Model:
public function user(): \Illuminate\Database\Eloquent\Relations\BelongsTo
{
    // BelongsTo across DBs works because User is central-pinned via
    // the CentralConnection trait (which it is in the template).
    return $this->belongsTo(\App\Models\User::class, 'central_user_id');
}
```

Application-level integrity is preserved; only the DB-level FK constraint (which can't span databases) is gone. Audit recipe:

```bash
rg "belongsTo\(.*User::class\)" --type=php
rg "User::class" --type=php
```

For each tenant-scoped model holding a `user_id`, rename the column to `central_user_id` in the tenant migration and drop the FK. Everything else (auth flow, controllers, central tables) keeps using `User` as-is.

## Step 4 — Implement `ExistingDataMigrator`

The template ships `App\Tenancy\Contracts\ExistingDataMigrator`. Write a concrete implementation for your schema. Worked example for a fictional "Projects + Tasks" app where each user owns their own data:

```php
<?php

declare(strict_types=1);

namespace App\Tenancy;

use App\Tenancy\Contracts\ExistingDataMigrator;
use Closure;

class ProjectsAppMigrator implements ExistingDataMigrator
{
    public function tablesToMoveToTenant(): array
    {
        return [
            'projects' => fn (array $row, array $idMap): array => [
                ...$row,
                'user_id' => $idMap['users'][$row['user_id']],
            ],
            'tasks' => fn (array $row, array $idMap): array => [
                ...$row,
                'user_id' => $idMap['users'][$row['user_id']],
                'project_id' => $idMap['projects'][$row['project_id']],
            ],
        ];
    }

    public function userMapper(): Closure
    {
        // Payload for the central `users` table (App\Models\User).
        // Tenant-side membership is created automatically by the iteration loop
        // (Step 5) attaching the user to the provisioned tenant via the
        // `tenant_user` pivot with role 'owner'.
        return fn (object $legacyUser): array => [
            'id' => $legacyUser->id,
            'name' => $legacyUser->name,
            'email' => $legacyUser->email,
            'password' => $legacyUser->password,
            'email_verified_at' => $legacyUser->email_verified_at,
            'remember_token' => $legacyUser->remember_token,
            'is_active' => $legacyUser->is_active,
            'timezone' => $legacyUser->timezone,
            'created_at' => $legacyUser->created_at,
            'updated_at' => $legacyUser->updated_at,
        ];
    }

    public function tenantProvisioner(): Closure
    {
        return fn (object $legacyUser): array => [
            'name' => $legacyUser->name."'s workspace",
            'subdomain' => 'user-'.$legacyUser->id,
            'plan' => 'free',
        ];
    }
}
```

Bind it in `AppServiceProvider::register()`, **replacing** the `NullExistingDataMigrator` binding:

```php
$this->app->bind(
    \App\Tenancy\Contracts\ExistingDataMigrator::class,
    \App\Tenancy\ProjectsAppMigrator::class,
);
```

## Step 5 — Implement the iteration loop

The `tenancy:migrate-existing` command **does not include the iteration loop** — every fork's source schema differs too much for a one-size-fits-all loop. Override the command's `handle()` in your fork (or write a sibling command) to:

1. Loop over rows from the source connection (the legacy DB), batched.
2. For each legacy user:
   a. Insert a `User` row (central DB) via `$migrator->userMapper()($legacy)`.
   b. Provision a tenant via `app(\App\Services\Tenancy\TenantProvisioningService::class)->provision(name: ..., ownerEmail: $legacy->email)` — this also attaches the user via `tenant_user` with role `owner`.
   c. Inside `tenancy()->run($tenant, function () use ($legacy, $migrator) { ... })`: for each table in `$migrator->tablesToMoveToTenant()`, read from legacy, transform via the closure, insert into the tenant DB. Build the `idMap` as you go.
3. Write an audit log line per user (JSONL: `user_id`, `tenant_id`, `status`, `rows_moved`, `duration_ms`, `errors`).
4. Handle `--dry-run` by doing all reads but skipping writes.
5. Handle `--continue-from={user_id}` by skipping users with ids ≤ that value.

## Step 6 — Dry-run

Restore your prod backup to a separate connection (`pgsql_legacy`):
```bash
createdb myapp_legacy
pg_restore -d myapp_legacy --no-owner --no-acl prod_backup_<date>.dump
```
Add to `config/database.php`:
```php
'pgsql_legacy' => [
    'driver' => 'pgsql',
    'host' => env('LEGACY_DB_HOST', '127.0.0.1'),
    'database' => env('LEGACY_DB_DATABASE', 'myapp_legacy'),
    // ... etc
],
```
Run and inspect the audit log; iterate your migrator until the dry-run reports clean for every user:
```bash
php artisan tenancy:migrate-existing --dry-run --connection=pgsql_legacy --audit-log=storage/logs/migrate-dryrun.jsonl
```

## Step 7 — Real run (staging)

```bash
php artisan tenancy:migrate-existing --connection=pgsql_legacy --audit-log=storage/logs/migrate-staging.jsonl
```
Spot-check 3 random users:
1. Log in via the central domain with their pre-migration email + password (passwords carry over verbatim — bcrypt hashes don't need re-hashing).
2. Confirm they land on their tenant (path mode: `/t/user-<id>/`).
3. Confirm their projects/tasks are visible.
4. Confirm any per-user uploads / S3 paths still resolve.

## Step 8 — Production cutover

1. Maintenance mode on: `php artisan down --refresh=3600 --secret={ops-token}`.
2. Fresh backup of prod DB. Verify file size + `pg_restore --list`.
3. Deploy the v5.0.0 + tenancy-enabled revision. **Disable auto-migrate** in your deploy pipeline first.
4. Restore the dump to `pgsql_legacy` on prod.
5. Run central migrations: `php artisan migrate --database=pgsql_central --path=database/migrations` + `--path=database/migrations/central`.
6. Run the import: `php artisan tenancy:migrate-existing --connection=pgsql_legacy --audit-log=storage/logs/cutover.jsonl`.
7. Verify totals (per-table count match between legacy and the sum across tenant DBs).
8. Spot-check 3 random users (as in Step 7).
9. If going subdomain mode: flip DNS now (wildcard already provisioned).
10. Maintenance mode off: `php artisan up`.
11. Monitor for 30 minutes (error rate, DB connections, Reverb client count, S3 4xx/5xx).

## Rollback plan

If anything goes wrong before users hit the app:
1. `php artisan down`.
2. Restore the pre-cutover dump over the legacy DB.
3. `TRUNCATE` central tables (`tenants`, `domains`, plus any pivot tables).
4. Drop all `tenant_*` databases.
5. Redeploy the pre-tenancy code revision.
6. `php artisan up`.

The S3 source paths were untouched (the import only `copyObject`s, never deletes), so file storage rolls back automatically.

## What the template can't help you with

- **Billing migration.** With Cashier, `subscriptions` / `invoices` stay central — the `billable` relationship still points at `App\Models\User`, now central-pinned via the `CentralConnection` trait. No model swap; just verify the tables live in the central DB.
- **OAuth client tenant-scoping.** Pre-tenancy OAuth tokens were `(user_id)`-scoped; multi-tenant they're `(user_id, tenant_id)`-scoped — the `oauth_*` tables need a `tenant_id` column.
- **Multi-account UX** (one user ↔ many tenants). The template ships the data side — `tenant_user` pivot, `User::tenants()` / `Tenant::users()` — so the import can call `$tenant->users()->attach($user->id, ['role' => 'owner'])`. **Not** shipped: the picker UI, the invite/accept flow, the in-session switcher. See `tenancy-usage.md` Step 8 for the login-redirect pattern and a picker controller skeleton; the Vue side is fork-specific.

## Pitfalls

- Don't provision your first tenant by hand (Step 4 of the usage guide) — the import command provisions one per existing user. Stop the usage flow at central migrations.
- Cross-DB FK constraints can't span databases — drop them and denormalize to `central_user_id` (Step 3); application-level integrity is preserved via the central-pinned `User`.
- Always dry-run against a restored copy until the audit log is clean for every user before touching real data.
