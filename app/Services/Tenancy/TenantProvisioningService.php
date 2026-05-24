<?php

declare(strict_types=1);

namespace App\Services\Tenancy;

use App\Enums\TenantRole;
use App\Models\Domain;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Database\ConnectionResolverInterface;
use Illuminate\Support\Str;
use RuntimeException;

/**
 * Encapsulates tenant provisioning. Used by `tenancy:provision` (CLI), by
 * fork-side registration controllers, and by anywhere a new tenant needs to
 * be created programmatically.
 *
 * Steps performed in a single central-DB transaction:
 *   1. Create the Tenant row (uuid id + data payload).
 *   2. Create the Domain row with the provided slug.
 *   3. (Optional) Attach an existing central User to the tenant via the
 *      `tenant_user` pivot as Owner.
 *
 * The transaction rolls back on any failure — no orphan rows.
 *
 * Note: this service triggers the package's TenantCreated event when
 * Tenant::create() returns. That event drives the CreateDatabase →
 * MigrateDatabase → SeedDatabase pipeline (synchronous by default).
 * Callers that want to defer DB creation should fire TenantCreated
 * separately or queue the pipeline jobs.
 */
class TenantProvisioningService
{
    public function __construct(
        private readonly ConnectionResolverInterface $db,
    ) {}

    /**
     * Provision a new tenant.
     *
     * @param  string  $name  Human-readable tenant name (stored in data->name)
     * @param  string|null  $slug  URL slug; defaults to Str::slug($name). Any unique string works.
     * @param  string|null  $ownerEmail  Optional email of an existing central user to attach as Owner.
     *
     * @throws RuntimeException If slug is empty, slug is already taken, or owner email doesn't match any user.
     */
    public function provision(string $name, ?string $slug = null, ?string $ownerEmail = null): Tenant
    {
        $slug = $slug ?? Str::slug($name);

        if ($slug === '') {
            throw new RuntimeException('Cannot derive a slug from the name. Pass an explicit slug.');
        }

        if (Domain::where('domain', $slug)->exists()) {
            throw new RuntimeException("A tenant with domain '{$slug}' already exists.");
        }

        // Resolve the owner BEFORE creating the tenant — fail fast on missing user
        // rather than leaving a dangling tenant behind.
        $owner = null;
        if ($ownerEmail !== null && $ownerEmail !== '') {
            $owner = User::where('email', $ownerEmail)->first();
            if ($owner === null) {
                throw new RuntimeException(
                    "No user with email '{$ownerEmail}' found in the central DB. ".
                    'Create the user first (via your registration flow or seeder), then retry.'
                );
            }
        }

        /** @var Tenant $tenant */
        $tenant = $this->db->connection(config('tenancy.database.central_connection'))
            ->transaction(function () use ($name, $slug, $owner): Tenant {
                // Pass `name` as a top-level attribute so the package's
                // VirtualColumn trait stores it inside the JSON `data` column
                // — mass-assigning `'data' => [...]` bypasses VirtualColumn
                // and leaves the column empty.
                $tenant = Tenant::create([
                    'id' => (string) Str::uuid(),
                    'name' => $name,
                ]);

                $tenant->domains()->create(['domain' => $slug]);

                if ($owner !== null) {
                    $tenant->users()->attach($owner->id, [
                        'role' => TenantRole::Owner->value,
                        'joined_at' => now(),
                    ]);
                }

                return $tenant;
            });

        return $tenant;
    }
}
