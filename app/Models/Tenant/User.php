<?php

declare(strict_types=1);

namespace App\Models\Tenant;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Tenant-side user record. Lives in the per-tenant DB and links back to the
 * central App\Models\User via central_user_id (denormalized — not a cross-DB
 * FK, just a reference for lookups).
 *
 * Notes:
 * - This is NOT the auth principal. The auth principal is App\Models\User,
 *   which lives in the central DB (pinned there by the CentralConnection
 *   trait) and is shared across all tenants the user belongs to.
 * - email_cache mirrors the central User.email for fast tenant-side lookups
 *   (notifications, member lists) without a cross-DB query. Forks that need
 *   automatic sync can wire a listener on User updates.
 * - The model does NOT pin a connection — it inherits the active default,
 *   which is the per-tenant DB once tenancy is initialized.
 */
class User extends Model
{
    use HasFactory;
    use SoftDeletes;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'central_user_id',
        'email_cache',
        'first_name',
        'last_name',
        'role',
        'is_active',
        'timezone',
        'last_seen_at',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'last_seen_at' => 'datetime',
        ];
    }
}
