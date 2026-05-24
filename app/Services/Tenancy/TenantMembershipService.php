<?php

declare(strict_types=1);

namespace App\Services\Tenancy;

use App\Enums\TenantRole;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Database\ConnectionResolverInterface;
use RuntimeException;

/**
 * Operations on the central `tenant_user` pivot — switching, leaving,
 * removing members. Separated from TenantInviteService because invites are
 * a different lifecycle (pending → accepted/declined) vs membership which
 * is the active relationship.
 */
class TenantMembershipService
{
    public function __construct(
        private readonly ConnectionResolverInterface $db,
    ) {}

    /**
     * Resolve the URL a user should be sent to in order to "switch" to a
     * tenant. The user must be a member of the tenant — fails fast if not.
     *
     * In path mode this is `<central>/t/{slug}/`. In subdomain mode it's
     * the tenant's primary host. Both produced via tenant_url().
     */
    public function switchTo(User $user, Tenant $tenant): string
    {
        if (! $this->isMember($user, $tenant)) {
            throw new RuntimeException('User is not a member of this tenant.');
        }

        return tenant_url('/', $tenant);
    }

    /**
     * Remove the user from the tenant (their own self-leave action). Fails
     * if the user is the tenant's owner — ownership must be transferred to
     * another user before the owner can leave.
     */
    public function leave(User $user, Tenant $tenant): void
    {
        $role = $this->roleOf($user, $tenant);

        if ($role === null) {
            throw new RuntimeException('User is not a member of this tenant.');
        }

        if ($role === TenantRole::Owner) {
            throw new RuntimeException(
                'The owner cannot leave the tenant. Transfer ownership to another member first.',
            );
        }

        $tenant->users()->detach($user->id);
    }

    /**
     * Remove $member from the tenant. $actor must have the authority to do
     * so per TenantRole::canBeRemovedBy().
     */
    public function remove(User $actor, Tenant $tenant, User $member): void
    {
        $actorRole = $this->roleOf($actor, $tenant);
        $memberRole = $this->roleOf($member, $tenant);

        if ($actorRole === null) {
            throw new RuntimeException('Actor is not a member of this tenant.');
        }

        if ($memberRole === null) {
            throw new RuntimeException('Target user is not a member of this tenant.');
        }

        if (! $memberRole->canBeRemovedBy($actorRole)) {
            throw new RuntimeException(
                sprintf(
                    'A %s cannot remove a %s from the tenant.',
                    $actorRole->value,
                    $memberRole->value,
                ),
            );
        }

        $tenant->users()->detach($member->id);
    }

    /**
     * Change $member's role inside $tenant. $actor must have the authority
     * to do so per TenantRole::canBeChangedBy(), and the target $newRole
     * cannot be Owner (use a separate transferOwnership flow for that).
     */
    public function changeRole(User $actor, Tenant $tenant, User $member, TenantRole $newRole): void
    {
        if ($newRole === TenantRole::Owner) {
            throw new RuntimeException(
                'Use transferOwnership() to grant the Owner role — direct role change is not allowed.',
            );
        }

        $actorRole = $this->roleOf($actor, $tenant);
        $memberRole = $this->roleOf($member, $tenant);

        if ($actorRole === null) {
            throw new RuntimeException('Actor is not a member of this tenant.');
        }

        if ($memberRole === null) {
            throw new RuntimeException('Target user is not a member of this tenant.');
        }

        if (! $memberRole->canBeChangedBy($actorRole)) {
            throw new RuntimeException(
                sprintf(
                    'A %s cannot change the role of a %s.',
                    $actorRole->value,
                    $memberRole->value,
                ),
            );
        }

        $tenant->users()->updateExistingPivot($member->id, ['role' => $newRole->value]);
    }

    /**
     * Transfer ownership of $tenant from the current owner to $newOwner.
     * The new owner must already be a member of the tenant. The previous
     * owner is demoted to Admin (configurable per fork).
     */
    public function transferOwnership(User $tenant_owner, Tenant $tenant, User $newOwner): void
    {
        $actorRole = $this->roleOf($tenant_owner, $tenant);

        if ($actorRole !== TenantRole::Owner) {
            throw new RuntimeException('Only the current owner can transfer ownership.');
        }

        if (! $this->isMember($newOwner, $tenant)) {
            throw new RuntimeException('The new owner must already be a member of the tenant.');
        }

        // Atomicity: swapping two roles needs both updates to succeed or both
        // to roll back. Without the transaction a mid-failure leaves the
        // tenant with zero owners — the "always exactly one owner" invariant
        // would silently break.
        $this->db->connection(config('tenancy.database.central_connection'))
            ->transaction(function () use ($tenant, $tenant_owner, $newOwner): void {
                $tenant->users()->updateExistingPivot($tenant_owner->id, ['role' => TenantRole::Admin->value]);
                $tenant->users()->updateExistingPivot($newOwner->id, ['role' => TenantRole::Owner->value]);
            });
    }

    /**
     * Is $user currently a member of $tenant?
     */
    public function isMember(User $user, Tenant $tenant): bool
    {
        return $tenant->users()->where('user_id', $user->id)->exists();
    }

    /**
     * The user's role inside the tenant, or null if not a member.
     */
    public function roleOf(User $user, Tenant $tenant): ?TenantRole
    {
        $pivot = $tenant->users()->where('user_id', $user->id)->first()?->pivot;

        if ($pivot === null) {
            return null;
        }

        return TenantRole::from((string) $pivot->getAttribute('role'));
    }
}
