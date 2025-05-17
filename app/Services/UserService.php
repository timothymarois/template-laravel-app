<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserService
{
    public function listPaginated(int $perPage = 15)
    {
        return User::query()
            ->orderByDesc('created_at')
            ->paginate($perPage);
    }

    public function create(array $data): User
    {
        return DB::transaction(function () use ($data) {
            $data['password'] = Hash::make(Str::random(24));

            return User::create($data);
        });
    }

    public function update(User $user, array $data): User
    {
        return DB::transaction(function () use ($user, $data) {
            $user->update($data);

            return $user;
        });
    }

    public function delete(User $user): void
    {
        $user->delete();
    }
}
