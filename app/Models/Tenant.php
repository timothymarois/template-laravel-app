<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Stancl\Tenancy\Contracts\TenantWithDatabase;
use Stancl\Tenancy\Database\Concerns\HasDatabase;
use Stancl\Tenancy\Database\Concerns\HasDomains;
use Stancl\Tenancy\Database\Models\Tenant as BaseTenant;

/**
 * The template's Tenant model. Extends the package's base which stores
 * arbitrary attributes in a JSON `data` column. Top-level attributes set
 * here (like `$tenant->name = 'Acme'`) get persisted into `data` via the
 * package's VirtualColumn trait.
 *
 * SoftDeletes is enabled so deleting a tenant is recoverable. Forks that
 * want true async cleanup with a grace window can follow Invelo's pattern
 * (see docs/guides/tenancy-using.md "Deletion lifecycle").
 *
 * Status: a tenant's provisioning state is tracked via the `ready` virtual
 * attribute. `false` until the TenantCreated job pipeline (CreateDatabase
 * → MigrateDatabase) completes successfully. Use `isReady()` in
 * application code to gate access to tenants still being provisioned.
 */
class Tenant extends BaseTenant implements TenantWithDatabase
{
    use HasDatabase;
    use HasDomains;
    use SoftDeletes;

    /**
     * Users with access to this tenant, via the central `tenant_user` pivot.
     * Mirrors User::tenants(). See database/migrations/central/2026_05_24_000010_create_tenant_user_table.php.
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'tenant_user', 'tenant_id', 'user_id')
            ->withPivot('role', 'joined_at')
            ->withTimestamps();
    }

    /**
     * True when the tenant's per-tenant DB has been created and migrated.
     * Defaults to false on a fresh tenant; flipped to true by the
     * MarkTenantReady listener at the end of the TenantCreated pipeline.
     *
     * Use this to gate access to a tenant in your routes/middleware so users
     * don't try to query an empty/missing per-tenant DB during the brief
     * provisioning window when running the pipeline asynchronously.
     */
    public function isReady(): bool
    {
        return (bool) ($this->getAttribute('ready') ?? false);
    }

    /**
     * Mark this tenant as ready. Idempotent. Called by the MarkTenantReady
     * listener after CreateDatabase + MigrateDatabase succeed.
     */
    public function markReady(): void
    {
        $this->setAttribute('ready', true);
        $this->save();
    }

    /**
     * Mark this tenant as failed (provisioning errored). Forks that want
     * "stuck tenant" cleanup can query for `failed=true` and offer retry
     * or hard-delete from an admin UI.
     */
    public function markFailed(?string $reason = null): void
    {
        $this->setAttribute('failed', true);
        if ($reason !== null) {
            $this->setAttribute('failed_reason', $reason);
        }
        $this->save();
    }

    /**
     * True when provisioning is known to have failed. Forks should surface
     * these tenants in an admin dashboard for retry/deletion.
     */
    public function hasFailed(): bool
    {
        return (bool) ($this->getAttribute('failed') ?? false);
    }
}
