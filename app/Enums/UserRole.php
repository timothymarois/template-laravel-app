<?php

declare(strict_types=1);

namespace App\Enums;

/**
 * Central-level role on a User. Distinguished from App\Enums\TenantRole,
 * which is per-tenant. Stored as a string on the `users.role` column and
 * cast to this enum by the User model.
 *
 *   - SuperAdmin — central operator. Can see and manage ALL tenants in the
 *                  central admin dashboard. Usually 1-3 people per deployment.
 *   - User       — everyone else. Their authority comes from per-tenant
 *                  membership rows in `tenant_user` (see TenantRole).
 *
 * Backing value 'admin' (not 'super_admin') matches existing role-string
 * conventions in the template — gates and policies that check the literal
 * string 'admin' work out of the box.
 *
 * Distinction summary:
 *
 *   $user->role         - central authority (this enum). Forks rarely change.
 *   tenant_user.role    - tenant-scoped authority (TenantRole enum).
 *                         Most authorization in a fork uses this.
 *
 * Promotion: `\App\Models\User::where('email', 'you@example.com')
 *               ->update(['role' => UserRole::SuperAdmin]);`
 */
enum UserRole: string
{
    case SuperAdmin = 'admin';
    case User = 'user';

    /**
     * Can this central role see/manage all tenants in the admin dashboard?
     */
    public function canManageAllTenants(): bool
    {
        return $this === self::SuperAdmin;
    }

    /**
     * Can this central role impersonate another user / a tenant member?
     * Defaults to SuperAdmin only; forks that need this disabled (e.g. for
     * compliance) can override.
     */
    public function canImpersonate(): bool
    {
        return $this === self::SuperAdmin;
    }
}
