# Database Performance Best Practices

## Eager Load Relationships the Caller Uses

Lazy loading inside a loop causes N+1 queries. Use `with()` for relationships this caller will
access; do not load unrelated relationships by default.

Incorrect (N+1 — executes 1 + N queries):
```php
$posts = Post::all();
foreach ($posts as $post) {
    echo $post->author->name;
}
```

Correct (2 queries total):
```php
$posts = Post::with('author')->get();
foreach ($posts as $post) {
    echo $post->author->name;
}
```

Constrain eager loads to select only needed columns and include the keys Eloquent needs to match the
relationship:

```php
$users = User::with(['posts' => function ($query) {
    $query->select('id', 'user_id', 'title')
          ->where('published', true)
          ->latest()
          ->limit(10);
}])->get();
```

## Prevent Lazy Loading in Development

Enable this in `AppServiceProvider::boot()` to catch N+1 issues during development.

```php
public function boot(): void
{
    Model::preventLazyLoading(! app()->isProduction());
    Model::preventSilentlyDiscardingAttributes(! app()->isProduction());
}
```

This throws for accidental lazy loading and dropped fillable attributes outside production. A
production application may log violations instead when throwing would turn one missed eager load
into an outage.

## Select Only Needed Columns

Avoid `SELECT *` — especially when tables have large text or JSON columns.

Incorrect:
```php
$posts = Post::with('author')->get();
```

Correct:
```php
$posts = Post::select('id', 'title', 'user_id', 'created_at')
    ->with(['author:id,name,avatar'])
    ->get();
```

When selecting columns on eager-loaded relationships, always include the foreign key column or the relationship won't match.

## Chunk Large Datasets

Never load thousands of records at once. Use chunking for batch processing.

Incorrect:
```php
$users = User::all();
foreach ($users as $user) {
    $user->notify(new WeeklyDigest);
}
```

Correct:
```php
User::where('subscribed', true)->chunk(200, function ($users) {
    foreach ($users as $user) {
        $user->notify(new WeeklyDigest);
    }
});
```

Use `chunkById()` when modifying records during iteration — standard `chunk()` uses OFFSET which shifts when rows change:

```php
User::where('active', false)->chunkById(200, function ($users) {
    $users->each->delete();
});
```

## Add Indexes From Representative Query Plans

Columns used in `WHERE`, `ORDER BY`, `JOIN`, and `GROUP BY` clauses are index candidates, not an
automatic checklist. Use `EXPLAIN`, representative data, write cost, and composite-index order to
choose the index.

Incorrect:
```php
Schema::create('orders', function (Blueprint $table) {
    $table->id();
    $table->foreignId('user_id')->constrained();
    $table->string('status');
    $table->timestamps();
});
```

Candidate after the query plan proves these access paths:
```php
Schema::create('orders', function (Blueprint $table) {
    $table->id();
    $table->foreignId('user_id')->index()->constrained();
    $table->string('status')->index();
    $table->timestamps();
    $table->index(['status', 'created_at']);
});
```

Add composite indexes for common query patterns (e.g., `WHERE status = ? ORDER BY created_at`).

## Use `withCount()` for Counting Relations

Never load entire collections just to count them.

Incorrect:
```php
$posts = Post::all();
foreach ($posts as $post) {
    echo $post->comments->count();
}
```

Correct:
```php
$posts = Post::withCount('comments')->get();
foreach ($posts as $post) {
    echo $post->comments_count;
}
```

Conditional counting:

```php
$posts = Post::withCount([
    'comments',
    'comments as approved_comments_count' => function ($query) {
        $query->where('approved', true);
    },
])->get();
```

## Use `cursor()` for Memory-Efficient Iteration

For read-only iteration over large result sets, `cursor()` loads one record at a time via a PHP generator.

Incorrect:
```php
$users = User::where('active', true)->get();
```

Correct:
```php
foreach (User::where('active', true)->cursor() as $user) {
    ProcessUser::dispatch($user->id);
}
```

Use `cursor()` for read-only iteration. Use `chunk()` / `chunkById()` when modifying records.

`cursor()` cannot eager load relationships and PDO may still buffer raw results. Use `lazy()` when
relations are needed or when cursor buffering becomes the limiting factor.

## No Queries in Blade Templates

Never execute queries in Blade templates. Pass data from controllers.

Incorrect:
```blade
@foreach (User::all() as $user)
    {{ $user->profile->name }}
@endforeach
```

Correct:
```php
// Controller
$users = User::with('profile')->get();
return view('users.index', compact('users'));
```

```blade
@foreach ($users as $user)
    {{ $user->profile->name }}
@endforeach
```

## Preserve Keys When Selecting Columns

Incorrect — Eloquent cannot match the selected relation back to its parent:

```php
$posts = Post::with('author:name')->get(); // Missing the related key.
```

Correct — include the related key and the foreign key needed for matching:

```php
$posts = Post::select(['id', 'author_id', 'title'])
    ->with('author:id,name')
    ->get();
```

## Choose the Growing-Read Primitive by Behavior

Use `lazy()` when relations must be eager loaded, `cursor()` for one-model-at-a-time iteration with
no eager loading, and `chunkById()` / `lazyById()` when processing changes the filtered data.

Incorrect — offset pages can skip rows after the update moves them out of the filter:

```php
User::whereNull('processed_at')->chunk(500, function ($users) {
    $users->each->update(['processed_at' => now()]);
});
```

Correct:

```php
User::whereNull('processed_at')->chunkById(500, function ($users) {
    $users->each->update(['processed_at' => now()]);
});
```
