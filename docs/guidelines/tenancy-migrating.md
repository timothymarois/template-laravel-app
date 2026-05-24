# Tenancy — Migrating an Existing App

This guide is for an agent (or human) adopting multi-tenancy in a fork that **already has user data**. If your fork is brand new with no user data, stop here and read [`tenancy-using.md`](./tenancy-using.md) instead.

This is a one-time, somewhat-manual, recoverable process. The template automates as much as is safely automatable (env, central DB setup, tenant provisioning, the import loop scaffolding). What's necessarily manual is the schema-specific decision-making — only your fork knows which tables are tenant-scoped, what your User model is used for, and how to remap your FKs.

## What's automated vs. what's manual

| Automated by template | Manual per-fork |
|---|---|
| Writing `.env` keys (`tenancy:enable`) | Deciding which tables are tenant-scoped |
| Configuring central DB connection | Implementing `App\Tenancy\Contracts\ExistingDataMigrator` |
| Running central migrations | Auditing existing tests for cross-DB assumptions |
| Pinning `App\Models\User` to the central DB (auto via trait) | The per-user iteration loop body (fork-implemented) |
| Provisioning tenants (`tenancy:provision`) | Backups + the actual cutover runbook |
| The `tenancy:migrate-existing` command shell | (`App\Models\User` references in code keep working — no refactor needed) |
| Audit log format spec | |

## Pre-migration checklist

- [ ] Fork is at template v5.0.0+ (apply [`docs/migrations/template-v5.0.0.md`](../migrations/template-v5.0.0.md) first).
- [ ] You've read [`tenancy-using.md`](./tenancy-using.md) for the basic enable-flow.
- [ ] Full backup of the current DB taken and verified (`pg_restore --list` on the dump file).
- [ ] If production: maintenance window scheduled, status page draft prepared, on-call paged.
- [ ] If S3 / object storage holds per-user paths, snapshot or version those too.
- [ ] No code freeze concerns (this is a multi-PR change; coordinate with the team).

## Step 1 — Enable tenancy

```bash
php artisan tenancy:enable
```

Then complete Steps 2 and 3 of [`tenancy-using.md`](./tenancy-using.md) — configure the central DB connection and run central migrations. Stop before Step 4 (provisioning) — you don't provision your first tenant manually; the import command does it for every existing user.

## Step 2 — Categorize your tables

Decide which tables go to the central DB (auth, infra, cross-cutting) and which go to per-tenant DBs (domain data).

| Category | Examples | Goes to |
|---|---|---|
| Auth & identity | `users`, `sessions`, `password_reset_tokens`, `personal_access_tokens`, `oauth_*` | Central |
| Cross-cutting infra | `cache`, `jobs`, `failed_jobs`, `job_batches`, `migrations` | Central |
| Domain data | `projects`, `tasks`, `posts`, `orders` — anything user-owned | Tenant |
| App config | `settings` (if per-user → Tenant; if global → Central), `feature_flags` (usually Central) | Depends |
| Billing | `subscriptions`, `invoices` (Cashier) | Central |

Make the list explicit in a doc you commit to the fork (`docs/tenancy-table-plan.md`). Future-you will want it.

## Step 3 — Audit `App\Models\User` references

Good news: **`App\Models\User` becomes central-pinned automatically** via the `CentralConnection` trait once tenancy is enabled. Most existing code that imports `User` keeps working without modification — auth lookups, FK joins on central tables, mail notifications all route to the central DB correctly.

The one case that does need attention: **FK columns on tenant-scoped tables**. If a tenant-scoped table like `projects` has a `user_id` column pointing at `users.id`, that FK constraint won't resolve at the database level because `users` lives in the central DB and `projects` lives in each per-tenant DB.

The template's pattern: **denormalize**. Drop the cross-DB FK constraint, keep the column as a plain `unsignedBigInteger` (rename to `central_user_id` for clarity), and resolve to `App\Models\User` via a manual query when needed:

```php
// Migration (in database/migrations/tenant/):
$table->unsignedBigInteger('central_user_id')->index();
// Note: no ->constrained() — that table lives in a different DB.

// Model:
public function user(): \Illuminate\Database\Eloquent\Relations\BelongsTo
{
    // BelongsTo across DBs works as long as the User model is central-pinned
    // via the CentralConnection trait (which it is in the template).
    return $this->belongsTo(\App\Models\User::class, 'central_user_id');
}
```

This works because the `CentralConnection` trait pins `App\Models\User` to the central connection regardless of which tenant context is active, so the BelongsTo eager-loads correctly. The only thing that breaks is the DB-level FK constraint (which can't span databases) — application-level integrity is preserved.

Audit recipe:
```bash
rg "belongsTo\(.*User::class\)" --type=php
rg "User::class" --type=php
```

For each tenant-scoped model holding a `user_id`, rename the column to `central_user_id` in the tenant migration and drop the FK constraint. Anything else (auth flow, controllers, central-side tables) keeps using `User` as-is.

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

Then bind it in `AppServiceProvider::register()`, **replacing** the `NullExistingDataMigrator` binding:
```php
$this->app->bind(
    \App\Tenancy\Contracts\ExistingDataMigrator::class,
    \App\Tenancy\ProjectsAppMigrator::class,
);
```

## Step 5 — Implement the iteration loop

The `tenancy:migrate-existing` command shipped in the template **does not include the iteration loop** — every fork's source schema is different enough that a "one size fits all" loop would be wrong more often than right. Override the command's `handle()` in your fork (or write a sibling command) to do:

1. Loop over rows from the source connection (the legacy DB), batched.
2. For each legacy user:
   a. Insert a `User` row (in the central DB) using `$migrator->userMapper()($legacy)`.
   b. Provision a tenant via `app(\App\Services\Tenancy\TenantProvisioningService::class)->provision(name: ..., ownerEmail: $legacy->email)` — this also attaches the user to the tenant via the `tenant_user` pivot with role `owner`.
   c. Inside `tenancy()->run($tenant, function () use ($legacy, $migrator) { ... })`:
      - For each table in `$migrator->tablesToMoveToTenant()`: read rows from legacy, transform via the closure, insert into tenant DB. Build the `idMap` as you go.
3. Write an audit log line per user (JSONL: `user_id`, `tenant_id`, `status`, `rows_moved`, `duration_ms`, `errors`).
4. Handle `--dry-run` by doing all the reads but skipping the writes.
5. Handle `--continue-from={user_id}` by skipping users with ids ≤ that value.

The Rundesk Phase 3 PRD (`multi-tenancy-phase-3-migration.md` in the rundesk-web-app repo) is a worked production example with all of the above plus token migration, asset path migration, and idempotency. Borrow heavily.

## Step 6 — Dry-run

Restore your prod backup to a separate DB connection (`pgsql_legacy`):
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

Run:
```bash
php artisan tenancy:migrate-existing --dry-run --connection=pgsql_legacy --audit-log=storage/logs/migrate-dryrun.jsonl
```

Inspect the audit log. Iterate your migrator until the dry-run reports clean for every user.

## Step 7 — Real run (staging)

Same command, no `--dry-run`:
```bash
php artisan tenancy:migrate-existing --connection=pgsql_legacy --audit-log=storage/logs/migrate-staging.jsonl
```

Spot-check 3 random users:
1. Log in via the central domain with their pre-migration email + password (passwords carry over verbatim — bcrypt hashes don't need re-hashing).
2. Confirm they land on their tenant (path-mode: `/t/user-<id>/`).
3. Confirm their projects/tasks are visible.
4. Confirm any per-user uploads / S3 paths still resolve.

## Step 8 — Production cutover

Follow the Rundesk Phase 4 runbook (`multi-tenancy-phase-4-cutover.md`) — it's the gold standard for this kind of cutover. Abbreviated:

1. Maintenance mode on: `php artisan down --refresh=3600 --secret={ops-token}`.
2. Fresh backup of prod DB. Verify file size + `pg_restore --list`.
3. Deploy the v5.0.0 + tenancy-enabled code revision. **Disable auto-migrate** in your deploy pipeline first.
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

- **Billing migration.** If you use Cashier, the `subscriptions` / `invoices` tables stay central — the `billable` relationship still points at `App\Models\User`, which is now central-pinned via the `CentralConnection` trait. No model swap needed; just verify the tables live in the central DB.
- **OAuth client tenant-scoping.** Pre-tenancy OAuth tokens were `(user_id)`-scoped; in a multi-tenant world they're `(user_id, tenant_id)`-scoped — the `oauth_*` tables need a `tenant_id` column. See the Rundesk Phase 2 PRD for the worked design.
- **Multi-account UX** (one user ↔ many tenants). The template ships the data side — `tenant_user` pivot table, `User::tenants()` and `Tenant::users()` relationships — so the import command can simply call `$tenant->users()->attach($user->id, ['role' => 'owner'])`. What's **not** shipped: the picker UI, the invite/accept flow, the in-session switcher. See `docs/guidelines/tenancy-using.md` Step 8 for the 5-line login redirect pattern and a picker controller skeleton; the Vue side is fork-specific. Rundesk Phase 2 is the reference for full production multi-account UX.

## Per-fork applicability

- `aprillaneart-site` — N/A. Marketing site, single-org.
- `versado-site` — N/A. Marketing.
- `imex-directory` — Optional. Single-org directory app today; revisit if/when you pivot to multi-org.
- `newsoul-web-app` — Optional. Single-org today.
- `rocketquote-os` — Optional. Single-org today.
- `rundesk-web-app` — **The worked production example.** See `multi-tenancy-phase-{1,2,3,4}.md` in the rundesk-web-app repo for the full plan that extends this template's scaffolding with OAuth tenant-scoping, creator attribution, multi-account UX, and the import command implementation.
