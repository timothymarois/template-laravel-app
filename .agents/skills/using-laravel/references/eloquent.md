# Eloquent Best Practices

## Use Correct Relationship Types

Use `hasMany`, `belongsTo`, `morphMany`, etc. with proper return type hints.

```php
public function comments(): HasMany
{
    return $this->hasMany(Comment::class);
}

public function author(): BelongsTo
{
    return $this->belongsTo(User::class, 'user_id');
}
```

## Use Local Scopes for Reusable Queries

Extract reusable query constraints into local scopes to avoid duplication.

Incorrect:
```php
$active = User::where('verified', true)->whereNotNull('activated_at')->get();
$articles = Article::whereHas('user', function ($q) {
    $q->where('verified', true)->whereNotNull('activated_at');
})->get();
```

Correct:
```php
#[Scope]
protected function active(Builder $query): Builder
{
    return $query->where('verified', true)->whereNotNull('activated_at');
}

// Usage
$active = User::active()->get();
$articles = Article::whereHas('user', fn ($q) => $q->active())->get();
```

## Apply Global Scopes Sparingly

Global scopes silently modify every query on the model, making debugging difficult. Prefer local scopes and reserve global scopes for truly universal constraints like soft deletes or multi-tenancy.

Incorrect (global scope for a conditional filter):
```php
class PublishedScope implements Scope
{
    public function apply(Builder $builder, Model $model): void
    {
        $builder->where('published', true);
    }
}
// Now admin panels, reports, and background jobs all silently skip drafts
```

Correct (local scope you opt into):
```php
#[Scope]
protected function published(Builder $query): Builder
{
    return $query->where('published', true);
}

Post::published()->paginate(); // Explicit
Post::paginate(); // Admin sees all
```

## Define Attribute Casts

Use the `casts()` method (or `$casts` property following project convention) for automatic type conversion.

```php
protected function casts(): array
{
    return [
        'is_active' => 'boolean',
        'metadata' => 'array',
        'total' => 'decimal:2',
    ];
}
```

## Cast Date Columns Properly

Always cast date columns. Use Carbon instances in templates instead of formatting strings manually.

Incorrect:
```blade
{{ Carbon::createFromFormat('Y-d-m H-i', $order->ordered_at)->toDateString() }}
```

Correct:
```php
protected function casts(): array
{
    return [
        'ordered_at' => 'datetime',
    ];
}
```

```blade
{{ $order->ordered_at->toDateString() }}
{{ $order->ordered_at->format('m-d') }}
```

## Use `whereBelongsTo()` for Relationship Queries

Cleaner than manually specifying foreign keys.

Incorrect:
```php
Post::where('user_id', $user->id)->get();
```

Correct:
```php
Post::whereBelongsTo($user)->get();
Post::whereBelongsTo($user, 'author')->get();
```

## Keep Table References Traceable

Prefer Eloquent relationships and queries when they express the operation. In reusable application
query code, model-derived table names can reduce rename drift; ordinary literal table names are not
themselves a correctness defect.

Literal table references:
```php
DB::table('users')->where('active', true)->get();

$query->join('companies', 'companies.id', '=', 'users.company_id');

DB::select('SELECT * FROM orders WHERE status = ?', ['pending']);
```

Model-derived or Eloquent alternatives:
```php
DB::table((new User)->getTable())->where('active', true)->get();

// Even better — use Eloquent or the query builder instead of raw SQL
User::where('active', true)->get();
Order::where('status', 'pending')->get();
```

When `DB::table()` or raw joins are necessary, follow the repository's convention and keep the
reference easy to find. Do not instantiate models only to satisfy a universal style rule.

**Exception — migrations:** In migrations, hardcoded table names via `DB::table('settings')` are acceptable and preferred. Models change over time but migrations are frozen snapshots — referencing a model that is later renamed or deleted would break the migration.

## Reserve Global Scopes for Universal Constraints

Incorrect — every admin, report, and queued query silently loses drafts:

```php
protected static function booted(): void
{
    static::addGlobalScope('published', fn ($query) => $query->whereNotNull('published_at'));
}
```

Correct — use a named local scope when callers legitimately need both views:

```php
#[Scope]
protected function published(Builder $query): void
{
    $query->whereNotNull('published_at');
}

$publicPosts = Post::published()->get();
$allPosts = Post::query()->get();
```

## Treat Mass Operations as Eventless

Eloquent mass updates and deletes do not hydrate models, so corresponding model events do not run.

Incorrect when an observer owns required search-index behavior:

```php
Post::where('expired', true)->update(['status' => 'archived']);
```

Correct — make the bulk side effect explicit and keep the batch bounded:

```php
Post::where('expired', true)->chunkById(500, function ($posts) {
    $ids = $posts->modelKeys();
    Post::whereKey($ids)->update(['status' => 'archived']);
    SearchIndex::rebuildPosts($ids);
});
```

If correctness requires per-model events, deliberately hydrate and save each model and accept the
extra work.
