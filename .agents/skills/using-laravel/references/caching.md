# Caching Best Practices

## Use `Cache::remember()` Instead of Manual Get/Put

Cleaner cache-aside pattern that removes boilerplate. use `Cache::lock()` for race conditions.

Incorrect:
```php
$val = Cache::get('stats');
if (! $val) {
    $val = $this->computeStats();
    Cache::put('stats', $val, 60);
}
```

Correct:
```php
$val = Cache::remember('stats', 60, fn () => $this->computeStats());
```

## Use `Cache::flexible()` for Stale-While-Revalidate

On high-traffic keys, one user always gets a slow response when the cache expires. `flexible()` serves slightly stale data while refreshing in the background.

Incorrect: `Cache::remember('stats', 300, fn () => $this->computeStats());`

Correct: `Cache::flexible('stats', [300, 600], fn () => $this->computeStats());` — fresh for 5 min, stale-but-served up to 10 min, refreshes via deferred function.

## Use `Cache::memo()` to Avoid Redundant Hits Within a Request

If the same cache key is read multiple times per request (e.g., a service called from multiple places), `memo()` stores the resolved value in memory.

`Cache::memo()->get('settings');` — 5 calls = 1 Redis round-trip instead of 5.

## Use Cache Tags to Invalidate Related Groups

Without tags, invalidating a group of entries requires tracking every key. Tags let you flush atomically. Not supported by the `file`, `dynamodb`, `database` or `storage` drivers.

```php
Cache::tags(['user-1'])->flush();
```

## Use `Cache::add()` for Atomic Conditional Writes

`add()` only writes if the key does not exist — atomic, no race condition between checking and writing.

Incorrect: `if (! Cache::has('lock')) { Cache::put('lock', true, 10); }`

Correct: `Cache::add('lock', true, 10);`

## Use `once()` for Per-Request Memoization

`once()` memoizes a function's return value for the lifetime of the object (or request for closures). Unlike `Cache::memo()`, it doesn't hit the cache store at all — pure in-memory.

```php
public function roles(): Collection
{
    return once(fn () => $this->loadRoles());
}
```

Multiple calls return the cached result without re-executing. Use `once()` for expensive computations called multiple times per request. Use `Cache::memo()` when you also want cross-request caching.

## Configure Failover Cache Stores in Production

If Redis goes down, the app falls back to a secondary store automatically.

```php
'failover' => ['driver' => 'failover', 'stores' => ['redis', 'database']],
```

A failover store changes behavior when the primary is unavailable. Adopt it only after deciding
whether cache-backed locks, rate limits, and correctness may safely degrade or become inconsistent.
It is not automatic production hardening.

## Define Invalidation Before Adding a Cache

Incorrect — the key can outlive the data contract indefinitely:

```php
$stats = Cache::rememberForever("team:{$team->id}:stats", fn () => $this->compute($team));
```

Correct — state acceptable staleness and one owning rebuild/invalidation path:

```php
$stats = Cache::remember(
    "team:{$team->id}:stats",
    now()->addMinutes(10),
    fn () => $this->compute($team),
);
```

Use `Cache::lock()` when concurrent misses could stampede an expensive rebuild. Use tags only on a
supported store and when group invalidation earns the portability cost. Verify `flexible()`,
`memo()`, and failover stores against the installed Laravel version.
