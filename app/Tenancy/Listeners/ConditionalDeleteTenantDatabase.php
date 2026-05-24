<?php

declare(strict_types=1);

namespace App\Tenancy\Listeners;

use App\Models\Tenant;
use Stancl\Tenancy\Contracts\TenantWithDatabase;
use Stancl\Tenancy\Events\TenantDeleted;
use Stancl\Tenancy\Jobs\DeleteDatabase;

/**
 * Drops the per-tenant DB ONLY on hard delete (forceDelete()), not on soft
 * delete. Without this guard, the package's stock pipeline drops the DB any
 * time `$tenant->delete()` fires — including soft deletes — which destroys
 * data the user might want to restore via `$tenant->restore()`.
 *
 * Pattern:
 *   $tenant->delete()       — soft delete, DB stays, restorable for grace period
 *   $tenant->restore()      — un-soft-delete, DB still there, everything works
 *   $tenant->forceDelete()  — hard delete; THIS listener drops the per-tenant DB
 *
 * Forks that want immediate DB cleanup on soft delete can replace this
 * listener with the package's stock `Jobs\DeleteDatabase` in the pipeline.
 *
 * Forks adding scheduled hard-deletion ("auto-purge soft-deleted tenants
 * after 30 days") run forceDelete() from a scheduled command — this
 * listener then handles the DB cleanup.
 */
class ConditionalDeleteTenantDatabase
{
    public function handle(TenantDeleted $event): void
    {
        $tenant = $event->tenant;

        // For models using SoftDeletes, `trashed()` is true if the model has
        // a deleted_at timestamp. On forceDelete(), `isForceDeleting()` is
        // true during the event firing — we use that to detect hard delete.
        if ($tenant instanceof Tenant && $tenant->trashed() && ! $tenant->isForceDeleting()) {
            return;
        }

        // Hard delete (or a non-SoftDeletes tenant model) — drop the DB.
        // DeleteDatabase requires TenantWithDatabase; our App\Models\Tenant
        // implements it, but guard against fork tenants that may not.
        if (! $tenant instanceof TenantWithDatabase) {
            return;
        }

        (new DeleteDatabase($tenant))->handle();
    }
}
