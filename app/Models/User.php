<?php

declare(strict_types=1);

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Enums\UserRole;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Carbon;
use Laravel\Sanctum\HasApiTokens;

/**
 * @property int $id
 * @property string $name
 * @property string $email
 * @property bool $is_active
 * @property UserRole $role
 */
class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

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
     * Does this user hold the operator role?
     *
     * The one-line form every fork of this template wrote for itself. It asks
     * the enum rather than comparing a string, so the definition stays in
     * UserRole. For a specific permission, prefer the capability —
     * `$user->role->canManageAllUsers()` — or a Policy.
     */
    public function isAdmin(): bool
    {
        return $this->role->canAccessAdmin();
    }

    /**
     * Check if the user is currently online (seen within last 5 minutes).
     */
    public function isOnline(): bool
    {
        /** @var Carbon|null $lastSeen */
        $lastSeen = $this->last_seen_at;

        return $lastSeen !== null && $lastSeen->greaterThan(now()->subMinutes(5));
    }
}
