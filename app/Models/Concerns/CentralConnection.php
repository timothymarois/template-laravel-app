<?php

declare(strict_types=1);

namespace App\Models\Concerns;

/**
 * Pins a model to the central DB connection when tenancy is enabled.
 *
 * When tenancy is disabled (the default), the trait returns null —
 * Eloquent's default behavior — so the host model behaves identically to
 * a non-trait model.
 *
 * When tenancy is enabled, the trait returns the configured central
 * connection name (config('tenancy.database.central_connection')), so
 * queries on the host model always hit the central DB even when an
 * outer tenant context has swapped the default connection.
 *
 * Used by App\Models\User to keep authentication queries on the central
 * DB regardless of tenant context. Forks adding additional central-only
 * models (OAuth clients, billing records, etc.) can apply this trait to
 * them.
 */
trait CentralConnection
{
    public function getConnectionName(): ?string
    {
        if (! config('tenancy.enabled')) {
            return null;
        }

        return config('tenancy.database.central_connection');
    }
}
