<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Console\Command;

class EnsureStorage extends Command
{
    protected $signature = 'app:ensure-storage';

    protected $description = 'Create any missing Laravel storage directories and generate OAuth signing keys if absent. Idempotent; safe to run on every deploy.';

    public function handle(): int
    {
        $ignoreAll = "*\n!.gitignore\n";
        $ignoreKeepData = "*\n!data/\n!.gitignore\n";

        $directories = [
            'app' => $ignoreAll,
            'app/public' => $ignoreAll,
            'framework' => $ignoreAll,
            'framework/cache' => $ignoreKeepData,
            'framework/cache/data' => $ignoreAll,
            'framework/sessions' => $ignoreAll,
            'framework/testing' => $ignoreAll,
            'framework/views' => $ignoreAll,
            'logs' => $ignoreAll,
        ];

        foreach ($directories as $dir => $gitignore) {
            $path = storage_path($dir);
            if (! is_dir($path)) {
                mkdir($path, 0775, true);
                $this->line("  created  storage/{$dir}");
            }

            $gitignorePath = $path.'/.gitignore';
            if (! is_file($gitignorePath)) {
                file_put_contents($gitignorePath, $gitignore);
                $this->line("  created  storage/{$dir}/.gitignore");
            }
        }

        if (class_exists('Laravel\\Passport\\PassportServiceProvider')
            && ! is_file(storage_path('oauth-private.key'))) {
            $this->line('  generating OAuth signing keys…');
            $this->call('passport:keys');
        }

        $this->info('Storage ready.');

        return self::SUCCESS;
    }
}
