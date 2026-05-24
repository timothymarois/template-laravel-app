<?php

declare(strict_types=1);

namespace App\Console\Commands\Tenancy;

use App\Enums\TenantRole;
use App\Models\Domain;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Creates a tenant + its primary domain row, then fires TenantCreated which
 * triggers the package's CreateDatabase → MigrateDatabase → SeedDatabase
 * pipeline.
 *
 * If --owner=<email> is given, the user with that email must already exist in
 * the central `users` table (this command does NOT create users). The user is
 * attached to the new tenant via the `tenant_user` pivot with role=owner.
 *
 * Used by tests and by any fork's own provisioning controller / signup flow.
 */
class ProvisionCommand extends Command
{
    protected $signature = 'tenancy:provision
                            {name : The tenant name (used as the domain slug if --subdomain is omitted)}
                            {--owner= : Email of an existing central user to attach as the tenant owner (optional)}
                            {--subdomain= : Override the auto-derived subdomain slug}';

    protected $description = 'Provision a new tenant (creates Tenant + Domain + tenant DB; optionally attaches owner).';

    public function handle(): int
    {
        if (! config('tenancy.enabled')) {
            $this->error('Tenancy is disabled. Run `php artisan tenancy:enable` first.');

            return self::FAILURE;
        }

        $name = (string) $this->argument('name');
        $subdomain = (string) ($this->option('subdomain') ?? Str::slug($name));

        if ($subdomain === '') {
            $this->error('Could not derive a subdomain from the name. Pass --subdomain=<slug> explicitly.');

            return self::FAILURE;
        }

        if (Domain::where('domain', $subdomain)->exists()) {
            $this->error("A tenant with domain '{$subdomain}' already exists.");

            return self::FAILURE;
        }

        // Resolve the owner user (if given) BEFORE creating the tenant — better
        // to fail fast on a missing user than to leave a dangling tenant behind.
        $owner = null;
        if ($ownerEmail = $this->option('owner')) {
            $owner = User::where('email', $ownerEmail)->first();
            if ($owner === null) {
                $this->error("No user with email '{$ownerEmail}' found in the central DB.");
                $this->line('  Create the user first (via your registration flow or seeder), then re-run.');

                return self::FAILURE;
            }
        }

        // Wrap tenant + domain + pivot in a transaction so a failure mid-way
        // doesn't leave orphan rows. Note: the package's TenantCreated event
        // fires from Tenant::create(), but its CreateDatabase/MigrateDatabase
        // pipeline doesn't write to the central DB — it provisions the per-
        // tenant DB elsewhere — so it's safe inside this transaction.
        /** @var Tenant $tenant */
        $tenant = DB::connection(config('tenancy.database.central_connection'))
            ->transaction(function () use ($name, $subdomain, $owner): Tenant {
                $tenant = Tenant::create([
                    'id' => (string) Str::uuid(),
                    'data' => ['name' => $name],
                ]);

                $tenant->domains()->create(['domain' => $subdomain]);

                if ($owner !== null) {
                    $tenant->users()->attach($owner->id, [
                        'role' => TenantRole::Owner->value,
                        'joined_at' => now(),
                    ]);
                }

                return $tenant;
            });

        if ($owner !== null) {
            $this->line("  Owner attached: {$owner->email} (user_id={$owner->id}, role=".TenantRole::Owner->value.')');
        }

        $this->info("✔ Tenant provisioned. id={$tenant->id} domain={$subdomain}");
        $this->line('  URL: '.tenant_url('/', $subdomain));

        return self::SUCCESS;
    }
}
