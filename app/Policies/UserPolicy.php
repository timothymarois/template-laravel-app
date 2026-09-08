<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\User;

/**
 * Who may administer other users. Every ability resolves to the same question —
 * is the actor an operator — because the template ships one admin tier; a fork
 * that needs finer grades splits the abilities apart rather than bypassing them.
 *
 * The `admin` route prefix is NOT authorization: that group carries `auth:sanctum`
 * only, so without this policy every action under it is reachable by any logged-in
 * user.
 */
class UserPolicy
{
    public function viewAny(User $actor): bool
    {
        return $actor->role->canManageAllUsers();
    }

    public function view(User $actor, User $target): bool
    {
        return $actor->role->canManageAllUsers();
    }

    public function create(User $actor): bool
    {
        return $actor->role->canManageAllUsers();
    }

    public function update(User $actor, User $target): bool
    {
        return $actor->role->canManageAllUsers();
    }

    /**
     * An operator may not delete their own account: it is the one delete that
     * cannot be undone by another operator, and it is almost always a misclick.
     */
    public function delete(User $actor, User $target): bool
    {
        return $actor->role->canManageAllUsers() && ! $actor->is($target);
    }
}
