<?php

declare(strict_types=1);

namespace App\Console\Commands\Tenancy;

use Illuminate\Console\Command;

/**
 * Flips a fork from the disabled-state to enabled by writing the required
 * env keys to .env. Idempotent — running twice is a no-op except for the
 * informational output.
 *
 * After this command runs successfully, the operator must:
 *   1. Configure their central DB connection (DB_CENTRAL_* env vars).
 *   2. Run migrations on the central connection.
 *   3. Provision their first tenant via `php artisan tenancy:provision <name>`.
 *
 * The command prints these next steps so the operator never has to guess.
 */
class EnableCommand extends Command
{
    protected $signature = 'tenancy:enable
                            {--force : Skip the confirmation prompt}
                            {--identification=path : Tenant identification mode (path|subdomain)}';

    protected $description = 'Enable multi-tenancy for this app (writes .env keys; no DB changes).';

    public function handle(): int
    {
        if (config('tenancy.enabled') && ! $this->option('force')) {
            $this->info('Tenancy is already enabled. Re-run with --force to overwrite env keys.');

            return self::SUCCESS;
        }

        $identification = (string) $this->option('identification');

        if (! in_array($identification, ['path', 'subdomain'], true)) {
            $this->error("Invalid --identification value: {$identification}. Expected: path or subdomain.");

            return self::FAILURE;
        }

        if (! $this->option('force') && ! $this->confirm('Enable multi-tenancy and rewrite .env keys?', true)) {
            $this->info('Aborted. No changes made.');

            return self::SUCCESS;
        }

        $envPath = base_path('.env');

        if (! file_exists($envPath)) {
            $this->error('.env file not found at '.$envPath.'. Create one (copy from .env.example) and re-run.');

            return self::FAILURE;
        }

        $this->writeEnvKey($envPath, 'TENANCY_ENABLED', 'true');
        $this->writeEnvKey($envPath, 'TENANCY_IDENTIFICATION', $identification);

        $this->info('✔ Tenancy enabled. Wrote 2 keys to .env.');
        $this->newLine();
        $this->line('Next steps:');
        $this->line('  1. Configure your central DB connection in .env:');
        $this->line('       DB_CENTRAL_CONNECTION=pgsql_central   (or mysql_central)');
        $this->line('       DB_CENTRAL_HOST, DB_CENTRAL_PORT, DB_CENTRAL_DATABASE, DB_CENTRAL_USERNAME, DB_CENTRAL_PASSWORD');
        $this->line('       (The pgsql_central / mysql_central connection blocks already exist in config/database.php');
        $this->line('        and read these env vars — you just need to fill the env values in.)');
        $this->line('  2. Create the central DB and run migrations:');
        $this->line('       php artisan migrate --database=pgsql_central');
        $this->line('       php artisan migrate --database=pgsql_central --path=database/migrations/central');
        $this->line('  3. Provision your first tenant:');
        $this->line('       php artisan tenancy:provision acme --owner=you@example.com');
        $this->newLine();
        $this->line('Full guide: docs/guidelines/tenancy-using.md');

        return self::SUCCESS;
    }

    /**
     * Idempotently upsert a key=value pair in a .env file. Adds the key at the
     * bottom if missing; replaces the value otherwise. Preserves comments and
     * surrounding whitespace.
     *
     * Uses preg_replace_callback to avoid the $-backreference interpretation
     * that bare preg_replace does on the replacement string — values
     * containing `$` (e.g. interpolated env defaults) would otherwise corrupt.
     */
    private function writeEnvKey(string $envPath, string $key, string $value): void
    {
        $contents = (string) file_get_contents($envPath);

        // Escape the value if it contains spaces or special chars
        $writableValue = str_contains($value, ' ') || str_contains($value, '"')
            ? '"'.addcslashes($value, '"\\').'"'
            : $value;

        $pattern = '/^'.preg_quote($key, '/').'=.*$/m';
        $replacement = $key.'='.$writableValue;

        if (preg_match($pattern, $contents) === 1) {
            $contents = (string) preg_replace_callback(
                $pattern,
                fn (): string => $replacement,
                $contents,
                1,
            );
        } else {
            $contents = rtrim($contents, "\n")."\n".$replacement."\n";
        }

        file_put_contents($envPath, $contents);
    }
}
