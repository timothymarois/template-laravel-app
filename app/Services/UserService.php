<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

class UserService extends ModelService
{
    protected string $model = User::class;

    public function buildQuery(array $options = []): Builder
    {
        return parent::buildQuery($options)
            ->when($options['search'] ?? false, function ($q) use ($options) {
                $q->where(function ($q) use ($options) {
                    $q->where('name', 'like', "%{$options['search']}%")
                        ->orWhere('email', 'like', "%{$options['search']}%");
                });
            })
            ->when($options['filters']['user_id'] ?? null, function ($q, $userId) {
                $q->where('id', $userId);
            });
    }

    public function create(array $data): User
    {
        // Use provided password when available; otherwise assign a random one.
        if (empty($data['password'])) {
            $data['password'] = Str::random(24);
        }

        return parent::create($data);
    }

    public function update(User|\Illuminate\Database\Eloquent\Model $model, array $data): User
    {
        $model->update($data);

        return $model;
    }
}
