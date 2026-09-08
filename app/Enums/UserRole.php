<?php

declare(strict_types=1);

namespace App\Enums;

use ArchTech\Enums\Values;

/**
 * Application-level role on a User, stored as a string on `users.role` and cast
 * to this enum by the model.
 *
 * **Ask a capability, not a role.** Call `canManageAllUsers()`, not
 * `$user->role === UserRole::SuperAdmin`. Comparing the case scatters the
 * definition of "what an admin may do" across every call site, so the day a
 * fork adds a third role every one of them has to be found. The capability
 * methods are the contract; the cases behind them are an implementation detail.
 *
 * Two roles ship deliberately — a template that guesses at a fork's permission
 * model is worse than one that gives it a clean place to add roles. To add one:
 * add the case, give it a `label()`, and decide each capability. Nothing else
 * needs to change, because nothing else compares cases.
 *
 * Promotion: `\App\Models\User::where('email', 'you@example.com')
 *               ->update(['role' => UserRole::SuperAdmin]);`
 */
enum UserRole: string
{
    use Values;

    /**
     * Operator. Sees and manages every user, and reaches everything under the
     * `admin` prefix. Usually 1-3 people per deployment.
     *
     * Backed by 'admin', not 'super_admin': gates and policies that check the
     * literal string 'admin' work out of the box, and the value is what lives
     * in the database, so changing it later is a data migration.
     */
    case SuperAdmin = 'admin';

    /** Everyone else. No admin access. */
    case User = 'user';

    /**
     * The role a newly created user gets. Referenced by the users migration and
     * the factory so the default lives in one place.
     */
    public static function default(): self
    {
        return self::User;
    }

    /** Human-readable name, for a UI that lets an operator pick a role. */
    public function label(): string
    {
        return match ($this) {
            self::SuperAdmin => 'Administrator',
            self::User => 'User',
        };
    }

    /** Can this role see and manage every user in the admin dashboard? */
    public function canManageAllUsers(): bool
    {
        return $this === self::SuperAdmin;
    }

    /** Can this role reach the admin surface at all? */
    public function canAccessAdmin(): bool
    {
        return $this === self::SuperAdmin;
    }

    /**
     * Can this role impersonate another user?
     * Defaults to SuperAdmin only; a fork that needs it disabled (for
     * compliance, say) narrows it here rather than at the call sites.
     */
    public function canImpersonate(): bool
    {
        return $this === self::SuperAdmin;
    }
}
