# Inertia DataTable Options

`InertiaDataTableOptions` merges default datatable settings with request input
and optional session data. Filter values are type-cast with the `Caster`
helper so services can safely consume them.

## Returned Options

`resolveIndexOptions` produces an array with:

- `search` – free-text filter
- `filters` – typed filter values
- `viewFields` – visible columns
- `perPage` – results per page
- `sortField` – column used for ordering
- `sortOrder` – `1` asc, `-1` desc

## Controller Example

```php
use Atlas\Laravel\Http\Concerns\InertiaDataTableOptions;
use Illuminate\Http\Request;
use Inertia\Inertia;

class UsersController
{
    use InertiaDataTableOptions;

    protected array $filterCasts = ['user_id' => 'int'];

    protected array $indexDefaults = [
        'perPage' => 50,
        'sortField' => 'name',
        'sortOrder' => 1,
    ];

    public function index(Request $request)
    {
        $options = $this->resolveIndexOptions($request, true, 'users.index');

        return Inertia::render('Users/Index', [
            'users' => $this->userService->listPaginated($options['perPage'], $options),
            'options' => $options,
        ]);
    }
}
```

`$filterCasts` defines expected types for filters. `$indexDefaults` sets fallback
pagination and sorting. `resolveIndexOptions` combines these with request data
and optional session values.

## Frontend

Pass `$options` to the view. The [`useDataTableOptions` composable](../ui/composables.md#usedatatableoptions) keeps query parameters in sync:

```vue
<script setup>
import { useDataTableOptions } from '@atlas/ui';
import { usePage } from '@inertiajs/vue3';

const { props } = usePage();
const table = useDataTableOptions('users.index', props.options);
</script>
```

## Custom Filters

`useDataTableOptions` exposes a reactive `filters` object. Update it to trigger a
new request automatically:

```vue
<template>
    <select v-model="table.form.filters.status">
        <option value="">Any status</option>
        <option value="active">Active</option>
        <option value="inactive">Inactive</option>
    </select>
</template>

<script setup>
import { useDataTableOptions } from '@atlas/ui';
import { usePage } from '@inertiajs/vue3';

const { props } = usePage();
const table = useDataTableOptions('users.index', props.options);

table.form.filters.role = 'admin';
</script>
```

Any filter change calls `fetchData` and clears row selection. Invoke
`table.fetchData()` to refresh on demand.

