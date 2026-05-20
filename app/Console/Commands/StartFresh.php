<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;

class StartFresh extends Command
{
    protected $signature = 'start:fresh
                            {--non-interactive : Skip confirmation prompts}';

    protected $description = 'Clear caches and refresh the database';

    public function handle(): int
    {
        if (app()->isProduction()) {
            $this->error('Can not execute this command in production!');

            return Command::FAILURE;
        }

        $this->clearCaches();

        if (! $this->refreshDatabase()) {
            return Command::FAILURE;
        }

        $this->info('Database refresh completed successfully!');

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

        $this->info('Application cache has been cleared.');
    }

    /**
     * Refresh the database.
     */
    private function refreshDatabase(): bool
    {
        $this->info('Refreshing the database...');

        try {
            $this->call('migrate:fresh', ['--force' => true]);

            return true;
        } catch (Exception $e) {
            $this->error('Failed to refresh the database: '.$e->getMessage());

            return false;
        }
    }
}
