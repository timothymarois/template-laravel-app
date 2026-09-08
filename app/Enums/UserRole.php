<?php

declare(strict_types=1);

namespace App\Enums;

/**
 * Application-level role on a User. Stored as a string on the `users.role`
 * column and cast to this enum by the User model.
 *
 *   - SuperAdmin — operator. Can see and manage every user in the admin
 *                  dashboard. Usually 1-3 people per deployment.
 *   - User       — everyone else. No admin access.
 *
 * Backing value 'admin' (not 'super_admin') matches existing role-string
 * conventions in the template — gates and policies that check the literal
 * string 'admin' work out of the box.
 *
 * Promotion: `\App\Models\User::where('email', 'you@example.com')
 *               ->update(['role' => UserRole::SuperAdmin]);`
 */
enum UserRole: string
{
    case SuperAdmin = 'admin';
    case User = 'user';

    /**
     * Can this role see and manage every user in the admin dashboard?
     */
    public function canManageAllUsers(): bool
    {
        return $this === self::SuperAdmin;
    }

    /**
     * Can this role impersonate another user?
     * Defaults to SuperAdmin only; forks that need this disabled (e.g. for
     * compliance) can override.
     */
    public function canImpersonate(): bool
    {
        return $this === self::SuperAdmin;
    }
}
