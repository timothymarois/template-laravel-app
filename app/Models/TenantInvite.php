<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\TenantRole;
use App\Models\Concerns\CentralConnection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * A pending invitation for a user to join a tenant. Lives in the central DB
 * (pinned via CentralConnection) — invites are issued before the recipient
 * has any central-side relationship to the tenant.
 *
 * Lifecycle:
 *   1. Created by TenantInviteService::send() with a fresh random token + 14d expiry.
 *   2. Recipient clicks the email link (/invites/{token}).
 *   3. Recipient accepts → accepted_at is set; pivot row added to tenant_user.
 *   4. Or recipient declines → row deleted.
 *   5. Or it expires unaccepted → service can sweep stale rows periodically.
 *
 * @property int $id
 * @property string $tenant_id
 * @property string $email
 * @property TenantRole $role
 * @property string $token
 * @property int|null $invited_by_user_id
 * @property Carbon $expires_at
 * @property Carbon|null $accepted_at
 */
class TenantInvite extends Model
{
    use CentralConnection;

    protected $fillable = [
        'tenant_id',
        'email',
        'role',
        'token',
        'invited_by_user_id',
        'expires_at',
        'accepted_at',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'role' => TenantRole::class,
            'expires_at' => 'datetime',
            'accepted_at' => 'datetime',
        ];
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function invitedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'invited_by_user_id');
    }

    public function isExpired(): bool
    {
        // expires_at is non-nullable per the schema (NOT NULL); cast assures Carbon.
        return $this->expires_at->isPast();
    }

    public function isAccepted(): bool
    {
        return $this->accepted_at !== null;
    }

    public function isPending(): bool
    {
        return ! $this->isAccepted() && ! $this->isExpired();
    }
}
