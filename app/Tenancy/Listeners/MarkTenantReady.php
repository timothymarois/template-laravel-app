<?php

declare(strict_types=1);

namespace App\Tenancy\Listeners;

use App\Models\Tenant;
use Stancl\Tenancy\Events\TenantCreated;

/**
 * Marks the tenant as `ready=true` after the CreateDatabase + MigrateDatabase
 * pipeline succeeds. Runs as the LAST step in the TenantCreated event chain
 * — see App\Providers\TenancyServiceProvider::events().
 *
 * If anything earlier in the pipeline throws (CreateDatabase fails because
 * the central DB user lacks CREATE privilege, MigrateDatabase fails on a
 * malformed tenant migration), this listener never runs and the tenant
 * stays at `ready=false`. Forks can:
 *
 *   - Gate tenant access in middleware on `tenant()->isReady()`
 *   - Surface unready tenants in an admin dashboard for retry
 *   - Wire a separate "MarkTenantFailed" listener to the exception, calling
 *     `$tenant->markFailed($e->getMessage())`
 */
class MarkTenantReady
{
    public function handle(TenantCreated $event): void
    {
        $tenant = $event->tenant;

        if ($tenant instanceof Tenant) {
            $tenant->markReady();
        }
    }
}
