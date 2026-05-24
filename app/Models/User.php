<?php

declare(strict_types=1);

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Enums\UserRole;
use App\Models\Concerns\CentralConnection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use CentralConnection, HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'timezone',
        'is_active',
        'role',
        'last_seen_at',
        'last_ip_address',
        'last_user_agent',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'is_active' => 'boolean',
            'role' => UserRole::class,
            'last_seen_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * The tenants this user can access. Resolves via the central `tenant_user`
     * pivot table — see database/migrations/central/2026_05_24_000010_create_tenant_user_table.php.
     *
     * Always declared, regardless of whether tenancy is enabled. When tenancy
     * is disabled the table doesn't exist; the method itself is harmless until
     * something actually calls `->tenants()` or `->tenants` on a User instance.
     *
     * Pivot columns: `role` (owner|admin|member, fork-customizable) and `joined_at`.
     */
    public function tenants(): BelongsToMany
    {
        return $this->belongsToMany(Tenant::class, 'tenant_user', 'user_id', 'tenant_id')
            ->withPivot('role', 'joined_at')
            ->withTimestamps();
    }

    /**
     * Check if the user is currently online (seen within last 5 minutes).
     */
    public function isOnline(): bool
    {
        /** @var \Illuminate\Support\Carbon|null $lastSeen */
        $lastSeen = $this->last_seen_at;

        return $lastSeen !== null && $lastSeen->greaterThan(now()->subMinutes(5));
    }
}
