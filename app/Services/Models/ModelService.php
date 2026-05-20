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
        // Explicit `page` overrides the request-resolver default. Needed for
        // non-HTTP callers (commands, jobs, MCP tools) that have no `?page=`
        // query string.
        $page = isset($options['page']) ? max(1, (int) $options['page']) : null;

        /** @var LengthAwarePaginator<int, TModel> */
        return $this->buildQuery($options)
            ->when($options['sortField'] ?? false, function ($q) use ($options) {
                $direction = ($options['sortOrder'] ?? 1) === 1 ? 'asc' : 'desc';

                return $q->orderBy($options['sortField'], $direction);
            })
            // Stable tiebreaker. Without this, rows whose sortField values
            // tie come back in whatever order the database planner picks,
            // which can flip across deploys when index strategy changes.
            // Anchoring on `id` makes pagination deterministic.
            ->orderBy('id', 'asc')
            ->paginate($perPage, ['*'], 'page', $page)
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

    /**
     * Apply a case-insensitive LIKE search across the given columns when
     * `$options['search']` is a non-empty string. The first column is matched
     * with `where`, subsequent columns with `orWhere`, all grouped so the
     * search scope doesn't leak into other filters.
     *
     * @param  Builder<TModel>  $query
     * @param  array<string, mixed>  $options
     * @param  list<string>  $columns
     * @return Builder<TModel>
     */
    protected function applySearch(Builder $query, array $options, array $columns): Builder
    {
        /** @var string|null $term */
        $term = $options['search'] ?? null;

        if (! is_string($term) || $term === '' || $columns === []) {
            return $query;
        }

        $pattern = '%'.$term.'%';

        return $query->where(function (Builder $q) use ($columns, $pattern): void {
            foreach ($columns as $i => $column) {
                $method = $i === 0 ? 'where' : 'orWhere';
                $q->{$method}($column, 'like', $pattern);
            }
        });
    }

    /**
     * Apply eager-load relations from `$options['with']` when present.
     *
     * @param  Builder<TModel>  $query
     * @param  array<string, mixed>  $options
     * @return Builder<TModel>
     */
    protected function applyWith(Builder $query, array $options): Builder
    {
        /** @var array<int, string>|string|null $relations */
        $relations = $options['with'] ?? null;

        if ($relations === null || $relations === [] || $relations === '') {
            return $query;
        }

        return $query->with($relations);
    }
}
