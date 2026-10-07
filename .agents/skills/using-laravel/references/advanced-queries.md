# Advanced Query Patterns

## Use `addSelect()` Subqueries for Single Values from Has-Many

Instead of eager-loading an entire has-many relationship for a single value (like the latest timestamp), use a correlated subquery via `addSelect()`. This pulls the value directly in the main SQL query — zero extra queries.

```php
public function scopeWithLastLoginAt($query): void
{
    $query->addSelect([
        'last_login_at' => Login::select('created_at')
            ->whereColumn('user_id', 'users.id')
            ->latest()
            ->take(1),
    ])->withCasts(['last_login_at' => 'datetime']);
}
```

## Create Dynamic Relationships via Subquery FK

Extend the `addSelect()` pattern to fetch a foreign key via subquery, then define a `belongsTo` relationship on that virtual attribute. This provides a fully-hydrated related model without loading the entire collection.

```php
public function lastLogin(): BelongsTo
{
    return $this->belongsTo(Login::class);
}

public function scopeWithLastLogin($query): void
{
    $query->addSelect([
        'last_login_id' => Login::select('id')
            ->whereColumn('user_id', 'users.id')
            ->latest()
            ->take(1),
    ])->with('lastLogin');
}
```

## Use Conditional Aggregates Instead of Multiple Count Queries

Replace N separate `count()` queries with a single query using `CASE WHEN` inside `selectRaw()`. Use `toBase()` to skip model hydration when you only need scalar values.

```php
$statuses = Feature::toBase()
    ->selectRaw("count(case when status = 'Requested' then 1 end) as requested")
    ->selectRaw("count(case when status = 'Planned' then 1 end) as planned")
    ->selectRaw("count(case when status = 'Completed' then 1 end) as completed")
    ->first();
```

## Use `setRelation()` to Prevent Circular N+1

When a parent model is eager-loaded with its children, and the view also needs `$child->parent`, use `setRelation()` to inject the already-loaded parent rather than letting Eloquent fire N additional queries.

```php
$feature->load('comments.user');
$feature->comments->each->setRelation('feature', $feature);
```

## Consider `whereIn` + Subquery Alongside `whereHas`

Depending on the database, statistics, and indexes, a `whereIn()` subquery may produce a better plan
than `whereHas()`. It is an alternative to measure, not a universal replacement.

Current `whereHas()` shape:

```php
$query->whereHas('company', fn ($q) => $q->where('name', 'like', $term));
```

Alternative `whereIn()` subquery to measure:

```php
$query->whereIn('company_id', Company::where('name', 'like', $term)->select('id'));
```

## Sometimes Two Simple Queries Beat One Complex Query

Running a small, targeted secondary query and passing its results via `whereIn` is often faster than a single complex correlated subquery or join. The additional round-trip is worthwhile when the secondary query is highly selective and uses its own index.

## Evaluate Compound Indexes for Multi-Column Ordering

When ordering by multiple columns, evaluate a compound index whose order matches the query's actual
equality, range, and sort shape. Confirm the representative plan; one index order does not serve
every filter and sort combination.

```php
// Migration
$table->index(['last_name', 'first_name']);

// Query — column order must match the index
User::query()->orderBy('last_name')->orderBy('first_name')->paginate();
```

## Use Correlated Subqueries for Has-Many Ordering

When sorting by a value from a has-many relationship, avoid joins (they duplicate rows). Use a correlated subquery inside `orderBy()` instead, paired with an `addSelect` scope for eager loading.

```php
public function scopeOrderByLastLogin($query): void
{
    $query->orderByDesc(Login::select('created_at')
        ->whereColumn('user_id', 'users.id')
        ->latest()
        ->take(1)
    );
}
```

## Measure Competing Query Shapes

Do not treat `whereIn`, `whereHas`, a join, a correlated subquery, or two simpler queries as a
universal winner. Data distribution and indexes determine the plan.

Incorrect — changing the query only because one form is assumed faster:

```php
// No representative plan or timing supports this rewrite.
$users = User::whereIn('id', Order::select('user_id'))->get();
```

Correct — inspect the target query with representative bindings and compare alternatives:

```php
$query = User::whereHas('orders', fn ($query) => $query->where('status', 'paid'));

dump($query->toRawSql()); // Run EXPLAIN with production-like statistics.
```

Keep the clearest adequate query when measured plans are equivalent. Complexity and extra round
trips are costs too.
