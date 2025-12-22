<?php

declare(strict_types=1);

namespace App\Services\Models;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Base service for Eloquent models providing simple CRUD helpers.
 *
 * Extend this service and set the model class on the consumer side:
 *
 * ```php
 * class UserService extends ModelService
 * {
 *     protected string $model = User::class;
 * }
 * ```
 *
 * @template TModel of Model
 */
abstract class ModelService
{
    /**
     * The model class managed by the service.
     *
     * @var class-string<TModel>
     */
    protected string $model;

    /**
     * Get a new query builder for the model.
     *
     * @return Builder<TModel>
     */
    public function query(): Builder
    {
        /** @var Builder<TModel> */
        return ($this->model)::query();
    }

    /**
     * Build a base query for the model. Override to apply filters.
     *
     * @param  array<string, mixed>  $options
     * @return Builder<TModel>
     */
    public function buildQuery(array $options = []): Builder
    {
        return $this->query();
    }

    /**
     * Retrieve all models.
     *
     * @param  array<int, string>  $columns
     * @param  array<string, mixed>  $options
     * @return Collection<int, TModel>
     */
    public function list(array $columns = ['*'], array $options = []): Collection
    {
        /** @var Collection<int, TModel> */
        return $this->buildQuery($options)->get($columns);
    }

    /**
     * Retrieve a paginated list of models.
     *
     * @param  array<string, mixed>  $options
     * @return LengthAwarePaginator<int, TModel>
     */
    public function listPaginated(int $perPage = 15, array $options = []): LengthAwarePaginator
    {
        /** @var LengthAwarePaginator<int, TModel> */
        return $this->buildQuery($options)
            ->when($options['sortField'] ?? false, function ($q) use ($options) {
                $direction = ($options['sortOrder'] ?? 1) === 1 ? 'asc' : 'desc';

                return $q->orderBy($options['sortField'], $direction);
            })
            ->paginate($perPage)
            ->withQueryString();
    }

    /**
     * Find a model by primary key.
     *
     * @return TModel|null
     */
    public function find(mixed $id): ?Model
    {
        return $this->query()->find($id);
    }

    /**
     * Create a new model instance.
     *
     * @param  array<string, mixed>  $data
     * @return TModel
     */
    public function create(array $data): Model
    {
        /** @var TModel */
        return $this->query()->create($data);
    }

    /**
     * Update the given model instance.
     *
     * @param  TModel  $model
     * @param  array<string, mixed>  $data
     * @return TModel
     */
    public function update(Model $model, array $data): Model
    {
        $model->update($data);

        return $model;
    }

    /**
     * Delete the given model instance.
     *
     * @param  TModel  $model
     */
    public function delete(Model $model): bool
    {
        return (bool) $model->delete();
    }
}
