<?php

declare(strict_types=1);

namespace App\Console\Commands\Tenancy;

use App\Services\Tenancy\TenantProvisioningService;
use Illuminate\Console\Command;
use RuntimeException;

/**
 * Thin CLI wrapper around App\Services\Tenancy\TenantProvisioningService.
 *
 * All business logic — slug validation, owner lookup, transactional row
 * insertion, pivot attach — lives in the service. This command parses CLI
 * input, delegates, and renders output. Forks building a registration
 * controller should inject the same service rather than duplicate the logic.
 */
class ProvisionCommand extends Command
{
    protected $signature = 'tenancy:provision
                            {name : The tenant name (used as the URL slug if --slug is omitted)}
                            {--owner= : Email of an existing central user to attach as the tenant owner (optional)}
                            {--slug= : Override the auto-derived URL slug. Any unique string works — slugified name (default), UUIDs, custom names, or full hostnames for subdomain mode. Stored as the `domain` value on the Domain row.}';

    protected $description = 'Provision a new tenant (creates Tenant + Domain + tenant DB; optionally attaches owner).';

    public function __construct(
        private readonly TenantProvisioningService $provisioning,
    ) {
        parent::__construct();
    }

    public function handle(): int
    {
        if (! config('tenancy.enabled')) {
            $this->error('Tenancy is disabled. Run `php artisan tenancy:enable` first.');

            return self::FAILURE;
        }

        try {
            $tenant = $this->provisioning->provision(
                name: (string) $this->argument('name'),
                slug: $this->option('slug'),
                ownerEmail: $this->option('owner'),
            );
        } catch (RuntimeException $e) {
            $this->error($e->getMessage());

            return self::FAILURE;
        }

        $primaryDomain = $tenant->domains->first();
        /** @var string $slug */
        $slug = $primaryDomain?->getAttribute('domain') ?? '(none)';
        $this->info("✔ Tenant provisioned. id={$tenant->id} domain={$slug}");

        if ($ownerEmail = $this->option('owner')) {
            $this->line("  Owner attached: {$ownerEmail} (role=owner)");
        }

        $this->line('  URL: '.tenant_url('/', $slug));

        return self::SUCCESS;
    }
}
