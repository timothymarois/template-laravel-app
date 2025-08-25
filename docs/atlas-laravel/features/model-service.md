# Model Service

`ModelService` is a lightweight base class for Eloquent models. It wraps common
CRUD operations and integrates with the same option array used by the Inertia
data table helpers.

## Usage

```php
use Atlas\Laravel\Services\ModelService;
use App\Models\User;

class UserService extends ModelService
{
    protected string $model = User::class;
}

$service = app(UserService::class);

$user = $service->create(['name' => 'Terry']);
$service->update($user, ['name' => 'Taylor']);

$service->listPaginated(15, [
    'search' => 'tay',
    'sortField' => 'name',
    'sortOrder' => -1,
]);

$service->delete($user);
```

## API

- `query()` – new query builder.
- `buildQuery(array $options = [])` – extendable base query.
- `list(array $columns = ['*'], array $options = [])` – all models.
- `listPaginated(int $perPage = 15, array $options = [])` – paginated list.
- `find(mixed $id)` – fetch by primary key.
- `create(array $data)` – persist a model.
- `update(Model $model, array $data)` – update a model.
- `delete(Model $model)` – remove a model.

## Configuration

Assign the model class via the `$model` property or in a constructor.

```php
class UserService extends ModelService
{
    protected string $model = User::class;
}
```

### Contextual Setup

```php
use Illuminate\Database\Eloquent\Builder;

class TeamUserService extends ModelService
{
    public function __construct(protected int $teamId)
    {
        $this->model = User::class;
    }

    public function buildQuery(array $options = []): Builder
    {
        return parent::buildQuery($options)
            ->where('team_id', $this->teamId);
    }
}
```

### Defaults

```php
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class SortedUserService extends ModelService
{
    protected string $model = User::class;

    protected array $defaults = [
        'sortField' => 'name',
        'sortOrder' => 1,
    ];

    public function listPaginated(int $perPage = 15, array $options = []): LengthAwarePaginator
    {
        return parent::listPaginated($perPage, array_merge($this->defaults, $options));
    }
}
```

## Custom Queries

Override `buildQuery` to add filters or searches:

```php
use Illuminate\Database\Eloquent\Builder;

class UserService extends ModelService
{
    protected string $model = User::class;

    public function buildQuery(array $options = []): Builder
    {
        return parent::buildQuery($options)
            ->when($options['search'] ?? false, fn ($q, $search) => $q
                ->where('name', 'like', "%{$search}%")
                ->orWhere('email', 'like', "%{$search}%"))
            ->when($options['filters']['user_id'] ?? null, fn ($q, $userId) =>
                $q->where('id', $userId));
    }
}
```

