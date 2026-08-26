<?php

declare(strict_types=1);

namespace App\Console\Commands\Tenancy;

use App\Tenancy\Contracts\ExistingDataMigrator;
use App\Tenancy\NullExistingDataMigrator;
use Illuminate\Console\Command;

/**
 * Skeleton command for migrating an existing pre-tenancy app into per-tenant
 * databases. Refuses to run until a concrete ExistingDataMigrator is bound
 * (the template ships NullExistingDataMigrator as the default).
 *
 * Forks implement App\Tenancy\Contracts\ExistingDataMigrator for their schema
 * and bind it in AppServiceProvider::register(). Then this command iterates
 * the legacy users, provisions a tenant per user, copies their data via the
 * migrator's tablesToMoveToTenant() spec, remaps FKs, and writes an audit log.
 *
 * The iteration loop itself is intentionally NOT shipped in v5.0.0 — every
 * fork's source schema is different enough that the template's "one size fits
 * all" loop would be wrong more often than right. See
 * docs/guides/tenancy-migrating.md for a worked example showing how to
 * extend this command per-fork.
 */
class MigrateExistingCommand extends Command
{
    protected $signature = 'tenancy:migrate-existing
                            {--dry-run : Read-only — report planned actions, write nothing}
                            {--connection= : Source DB connection to read legacy data from}';

    protected $description = 'Migrate existing pre-tenancy data into per-tenant DBs (requires fork-specific ExistingDataMigrator).';

    public function handle(): int
    {
        if (! config('tenancy.enabled')) {
            $this->error('Tenancy is disabled. Run `php artisan tenancy:enable` first.');

            return self::FAILURE;
        }

        // Resolve the migrator only after the enabled-check passes — the
        // binding is conditionally registered in AppServiceProvider, so it
        // doesn't exist in the disabled-state container.
        $migrator = app(ExistingDataMigrator::class);

        if ($migrator instanceof NullExistingDataMigrator) {
            $this->error('No ExistingDataMigrator bound for this fork.');
            $this->newLine();
            $this->line('Steps to enable this command:');
            $this->line('  1. Implement App\Tenancy\Contracts\ExistingDataMigrator for your schema.');
            $this->line('  2. Bind it in AppServiceProvider::register():');
            $this->line('       $this->app->bind(');
            $this->line('           \App\Tenancy\Contracts\ExistingDataMigrator::class,');
            $this->line('           \App\Tenancy\YourMigrator::class,');
            $this->line('       );');
            $this->line('  3. Re-run this command.');
            $this->newLine();
            $this->line('Full worked example: docs/guides/tenancy-migrating.md');

            return self::FAILURE;
        }

        $this->error('A concrete ExistingDataMigrator is bound but the iteration loop is fork-implemented.');
        $this->line('Override App\Console\Commands\Tenancy\MigrateExistingCommand::handle() in your fork');
        $this->line('with the per-user iteration. See docs/guides/tenancy-migrating.md for the pattern.');

        return self::FAILURE;
    }
}
