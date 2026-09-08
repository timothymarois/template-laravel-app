<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\User;
use Laravel\Sanctum\PersonalAccessToken;

/**
 * Who may manage API keys. Keys are issued to the acting operator and scoped to
 * them: `viewAny` and `create` gate the screen, and `delete` additionally requires
 * that the key belongs to the actor, so a guessed id cannot revoke someone else's.
 */
class ApiKeyPolicy
{
    public function viewAny(User $actor): bool
    {
        return $actor->role->canManageAllUsers();
    }

    public function create(User $actor): bool
    {
        return $actor->role->canManageAllUsers();
    }

    public function delete(User $actor, PersonalAccessToken $key): bool
    {
        return $actor->role->canManageAllUsers() && $key->tokenable?->is($actor) === true;
    }
}
