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
 * arbitrary attributes in a JSON `data` column. Forks that want first-class
 * columns (name, plan, etc.) should add a migration in database/migrations/central/
 * and list them via the static getCustomColumns() method per the package docs.
 *
 * SoftDeletes is enabled so deleting a tenant is recoverable. Forks that
 * want true async cleanup with a grace window can follow Invelo's pattern
 * (see docs/guidelines/tenancy-using.md "Deletion lifecycle").
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
}
