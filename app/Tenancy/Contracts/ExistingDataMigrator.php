<?php

declare(strict_types=1);

namespace App\Tenancy\Contracts;

use Closure;

/**
 * Contract a fork implements when adopting tenancy on an existing app with
 * pre-tenancy user data. Bind a concrete implementation in AppServiceProvider:
 *
 *     $this->app->bind(
 *         \App\Tenancy\Contracts\ExistingDataMigrator::class,
 *         \App\Tenancy\YourMigrator::class,
 *     );
 *
 * Then run `php artisan tenancy:migrate-existing`. See
 * docs/guidelines/tenancy-migrating.md for a full worked example.
 *
 * The template ships NullExistingDataMigrator as the default — it throws
 * informative errors directing the operator to implement this interface.
 */
interface ExistingDataMigrator
{
    /**
     * Tables to copy into each newly-provisioned tenant DB, in FK-respecting order.
     *
     * Keyed by table name. Each value is a closure of shape
     *     fn(array $row, array $idMap): array
     * that returns a transformed row ready to insert into the tenant DB. The
     * $idMap argument lets the closure remap FK columns based on previously
     * inserted parents (e.g. legacy user_id → tenant_user_id).
     *
     * @return array<string, Closure>
     */
    public function tablesToMoveToTenant(): array;

    /**
     * A closure of shape
     *     fn(object $legacyUser): array<string, mixed>
     * that maps one row from the legacy users table to the central
     * App\Models\User insert payload. The template stores the auth principal
     * centrally; tenant-side users are represented purely by the tenant_user
     * pivot row (created automatically by the import iteration loop using the
     * tenantProvisioner() output for owner role).
     */
    public function userMapper(): Closure;

    /**
     * A closure of shape
     *     fn(object $legacyUser): array{name: string, subdomain: string, ...}
     * that returns the attributes used to create the Tenant + Domain rows
     * for this legacy user.
     */
    public function tenantProvisioner(): Closure;
}
