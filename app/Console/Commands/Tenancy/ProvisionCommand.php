<?php

declare(strict_types=1);

namespace App\Console\Commands\Tenancy;

use App\Models\Domain;
use App\Models\Tenant;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

/**
 * Creates a tenant + its primary domain row, then fires TenantCreated which
 * triggers the package's CreateDatabase → MigrateDatabase → SeedDatabase
 * pipeline. The optional --owner flag records an owner email on the tenant
 * record but does not create or link a central User row — that's a separate
 * concern handled by your registration/onboarding flow.
 *
 * Used by tests and by any fork's own provisioning controller / signup flow.
 */
class ProvisionCommand extends Command
{
    protected $signature = 'tenancy:provision
                            {name : The tenant name (used as the domain slug if --subdomain is omitted)}
                            {--owner= : Email of the user who owns this tenant (optional; recorded on the tenant data column)}
                            {--subdomain= : Override the auto-derived subdomain slug}';

    protected $description = 'Provision a new tenant (creates Tenant + Domain + tenant DB).';

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

        /** @var Tenant $tenant */
        $tenant = Tenant::create([
            'id' => (string) Str::uuid(),
            'data' => [
                'name' => $name,
                'owner_email' => $this->option('owner'),
            ],
        ]);

        $tenant->domains()->create(['domain' => $subdomain]);

        $this->info("✔ Tenant provisioned. id={$tenant->id} domain={$subdomain}");
        $this->line('  URL: '.tenant_url('/', $subdomain));

        return self::SUCCESS;
    }
}
