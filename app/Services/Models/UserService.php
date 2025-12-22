<?php

declare(strict_types=1);

namespace App\Services\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

/**
 * @extends ModelService<User>
 */
class UserService extends ModelService
{
    protected string $model = User::class;

    /**
     * @param  array<string, mixed>  $options
     * @return Builder<User>
     */
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

    /**
     * @param  array<string, mixed>  $data
     */
    public function create(array $data): User
    {
        if (empty($data['password'])) {
            $data['password'] = Str::random(24);
        }

        /** @var User */
        return parent::create($data);
    }

    /**
     * @param  User  $model
     * @param  array<string, mixed>  $data
     */
    public function update(Model $model, array $data): User
    {
        $model->update($data);

        /** @var User */
        return $model;
    }
}
