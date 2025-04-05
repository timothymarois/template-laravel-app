<?php

namespace App\Console\Commands;

use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;

class StartFresh extends Command
{
    protected $signature = 'start:fresh';

    protected $description = 'Drop and refresh the database';

    public function handle(): int
    {
        if (app()->isProduction()) {
            $this->error('Can not execute this command in production!');

            return Command::FAILURE;
        }

        // Cache::flush();
        // Artisan::call('horizon:clear');
        // Artisan::call('cache:clear');
        // Artisan::call('config:clear');
        // Artisan::call('route:clear');
        // Artisan::call('view:clear');

        $this->info('Application cache has been cleared.');

        if (! $this->refreshDatabase()) {
            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }

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
