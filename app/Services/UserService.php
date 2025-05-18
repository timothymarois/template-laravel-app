<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;


class UserService
{
    public function buildQuery(array $options = []): Builder
    {
        return User::query()
            ->when($options['search'] ?? false, function ($q) use ($options) {
                return $q->where(function ($q) use ($options) {
                    $q->where('name', 'like', "%{$options['search']}%")
                        ->orWhere('email', 'like', "%{$options['search']}%");
                });
            });
    }

    public function listPaginated(int $perPage = 15, array $options = []): LengthAwarePaginator
    {
        return $this->buildQuery($options)
            ->when($options['sortField'] ?? false, function ($q) use ($options) {
                return $q->orderBy($options['sortField'], $options['sortOrder'] ?? 'asc');
            })
            ->paginate($perPage)
            ->withQueryString();
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
