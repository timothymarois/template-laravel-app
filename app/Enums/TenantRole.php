<?php

declare(strict_types=1);

namespace App\Enums;

/**
 * Roles a user can hold within a tenant, stored on the `tenant_user.role`
 * pivot column. Default ladder:
 *
 *   - Owner  — full control, exclusive. Cannot be removed or have role changed
 *              except via ownership transfer. There is always exactly one owner
 *              per tenant (enforced at the application layer, not the DB).
 *   - Admin  — can invite/remove members and change other members' roles, but
 *              not the owner's role.
 *   - Member — can use the tenant's features but not manage membership.
 *
 * Forks can extend this enum (e.g. add `Viewer`, `Billing`) by editing this
 * file. The capability methods below define the default permission model;
 * forks may override the methods entirely if they need different semantics.
 */
enum TenantRole: string
{
    case Owner = 'owner';
    case Admin = 'admin';
    case Member = 'member';

    /**
     * The default role new members receive when attached without an explicit role.
     */
    public static function default(): self
    {
        return self::Member;
    }

    /**
     * Can this role invite new members, remove members, or change member roles?
     * Owners + admins can; members cannot.
     */
    public function canManageMembers(): bool
    {
        return in_array($this, [self::Owner, self::Admin], true);
    }

    /**
     * Can this role transfer ownership to another user? Owners only.
     */
    public function canTransferOwnership(): bool
    {
        return $this === self::Owner;
    }

    /**
     * Can $actor change the role of a user holding $this role?
     *
     * Rules:
     *   - Owner's role can never be changed (only ownership transfer).
     *   - Otherwise, $actor must be able to manage members.
     *   - Admins cannot demote / promote other admins (only owners can).
     */
    public function canBeChangedBy(self $actor): bool
    {
        if ($this === self::Owner) {
            return false;
        }

        if ($this === self::Admin && $actor !== self::Owner) {
            return false;
        }

        return $actor->canManageMembers();
    }

    /**
     * Can $actor remove a user holding $this role from the tenant?
     *
     * Owners cannot be removed (transfer ownership first). Admins and members
     * can be removed by anyone who can manage members, with the admin-on-admin
     * restriction (only owners can remove admins).
     *
     * NOTE: this is intentionally identical to canBeChangedBy() in v5.0.0 —
     * "can change someone's role" and "can remove someone" follow the same
     * permission ladder by default. Kept as separate methods so forks can
     * diverge them later (e.g. "admins can demote other admins but not
     * remove them") without breaking callers.
     */
    public function canBeRemovedBy(self $actor): bool
    {
        if ($this === self::Owner) {
            return false;
        }

        if ($this === self::Admin && $actor !== self::Owner) {
            return false;
        }

        return $actor->canManageMembers();
    }
}
