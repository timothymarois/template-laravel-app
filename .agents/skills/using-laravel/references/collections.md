# Collection Best Practices

## Use Higher-Order Messages for Simple Operations

Incorrect:
```php
$users->each(function (User $user) {
    $user->markAsVip();
});
```

Correct: `$users->each->markAsVip();`

Works with `each`, `map`, `sum`, `filter`, `reject`, `contains`, etc.

## Choose `cursor()` vs. `lazy()` Correctly

- `cursor()` — one model in memory, but cannot eager-load relationships (N+1 risk).
- `lazy()` — chunked pagination returning a flat LazyCollection, supports eager loading.

Incorrect: `User::with('roles')->cursor()` — eager loading silently ignored.

Correct: `User::with('roles')->lazy()` for relationship access; `User::cursor()` for attribute-only work.

## Use `lazyById()` When Updating Records While Iterating

`lazy()` uses offset pagination — updating records during iteration can skip or double-process. `lazyById()` uses `id > last_id`, safe against mutation.

## Use `toQuery()` for Bulk Operations on Collections

Avoids manual `whereIn` construction.

Incorrect: `User::whereIn('id', $users->pluck('id'))->update([...]);`

Correct: `$users->toQuery()->update([...]);`

## Use `#[CollectedBy]` for Custom Collection Classes

More declarative than overriding `newCollection()`.

```php
#[CollectedBy(UserCollection::class)]
class User extends Model {}
```

Use a custom collection only when reusable domain collection behavior earns the extra type, and
verify `CollectedBy` exists in the installed Laravel version.

## Remember That `toQuery()` Is a Mass Operation

Incorrect when model events own required behavior:

```php
$users->toQuery()->update(['status' => 'inactive']);
```

Correct — use `toQuery()` only after confirming one model type, one connection, and intentionally
eventless behavior:

```php
$users = User::whereKey($ids)->get();
$updated = $users->toQuery()->update(['status' => 'inactive']);
AuditUsersDeactivated::dispatch($users->modelKeys());
```

Hydrate and save each model when per-model events are the required contract.
