<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

use function Laravel\Prompts\confirm;
use function Laravel\Prompts\info;
use function Laravel\Prompts\warning;

class StartFresh extends Command
{
    protected $signature = 'start:fresh
                            {--non-interactive : Skip confirmation prompts}';

    protected $description = 'Drop and refresh the database (including tenant databases if tenancy is enabled)';

    public function handle(): int
    {
        if (app()->isProduction()) {
            $this->error('Can not execute this command in production!');

            return Command::FAILURE;
        }

        $this->clearCaches();

        // Drop tenant databases if tenancy is configured
        if ($this->isTenancyEnabled()) {
            if (! $this->dropTenantDatabases()) {
                return Command::FAILURE;
            }
        }

        if (! $this->refreshCentralDatabase()) {
            return Command::FAILURE;
        }

        info('Database refresh completed successfully!');

        return Command::SUCCESS;
    }

    /**
     * Clear all application caches.
     */
    private function clearCaches(): void
    {
        Cache::flush();

        // Only call horizon:clear if Horizon is installed
        if (class_exists(\Laravel\Horizon\HorizonServiceProvider::class)) {
            Artisan::call('horizon:clear');
        }

        Artisan::call('cache:clear');
        Artisan::call('config:clear');
        Artisan::call('route:clear');
        Artisan::call('view:clear');

        info('Application cache has been cleared.');
    }

    /**
     * Check if tenancy is enabled.
     */
    private function isTenancyEnabled(): bool
    {
        return file_exists(config_path('tenancy.php'));
    }

    /**
     * Drop all tenant databases.
     */
    private function dropTenantDatabases(): bool
    {
        $prefix = config('tenancy.database.prefix', 'tenant_');
        $connection = config('database.default');
        $driver = config("database.connections.{$connection}.driver");

        // Only MySQL/MariaDB support the INFORMATION_SCHEMA query approach
        if (! in_array($driver, ['mysql', 'mariadb'])) {
            warning("Tenant database cleanup not supported for {$driver}. Skipping.");

            return true;
        }

        try {
            // Find all tenant databases
            $databases = DB::select('
                SELECT SCHEMA_NAME as db_name
                FROM information_schema.SCHEMATA
                WHERE SCHEMA_NAME LIKE ?
            ', [$prefix.'%']);

            if (empty($databases)) {
                info('No tenant databases found to drop.');

                return true;
            }

            $count = count($databases);

            // Only prompt for confirmation in interactive mode
            if (! $this->option('non-interactive') && $this->input->isInteractive()) {
                if (! confirm("Found {$count} tenant database(s). Drop all tenant databases?", true)) {
                    info('Skipping tenant database cleanup.');

                    return true;
                }
            } else {
                $this->line("  Found {$count} tenant database(s). Dropping...");
            }

            // Drop each tenant database
            foreach ($databases as $database) {
                $dbName = $database->db_name;

                try {
                    DB::statement("DROP DATABASE IF EXISTS `{$dbName}`");
                    $this->line("  Dropped database: {$dbName}");
                } catch (Exception $e) {
                    $this->error("  Failed to drop {$dbName}: ".$e->getMessage());
                }
            }

            info("Dropped {$count} tenant database(s).");

            return true;
        } catch (Exception $e) {
            $this->error('Failed to query tenant databases: '.$e->getMessage());

            return false;
        }
    }

    /**
     * Refresh the central database.
     */
    private function refreshCentralDatabase(): bool
    {
        info('Refreshing the central database...');

        try {
            $this->call('migrate:fresh', ['--force' => true]);

            return true;
        } catch (Exception $e) {
            $this->error('Failed to refresh the database: '.$e->getMessage());

            return false;
        }
    }
}
