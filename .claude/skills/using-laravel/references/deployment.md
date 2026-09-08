# Performance & Deployment Best Practices

## Measure Before Changing Infrastructure

Locate the bottleneck before recommending Octane, replicas, or larger machines.

```text
1. Count queries and find lazy loading.
2. Inspect slow query plans with EXPLAIN.
3. Find unbounded reads and oversized responses.
4. Identify slow or failure-prone inline work.
5. Measure serialization, cache, and external-call time.
```

Correct the measured boundary first. Infrastructure can add capacity, but it does not repair an
N+1 query or an unbounded collection.

## Build and Restart Release State Deliberately

Use the application's documented deployment sequence. A conventional release rebuilds framework
caches after the new code is active and restarts every long-running process that holds PHP or SSR
code.

```bash
composer install --no-dev --optimize-autoloader
php artisan migrate --force
php artisan optimize
php artisan queue:restart
```

The commands and order are examples, not a universal deployment script. Verify zero-downtime
switching, migration locks, maintenance-mode behavior, Inertia SSR, Horizon, Octane, and process
supervision against the application being deployed.

## Treat Octane as Persistent State

Incorrect — mutable static data survives the request that created it:

```php
final class RequestTracker
{
    private static array $seen = [];

    public function record(string $requestId): void
    {
        self::$seen[] = $requestId;
    }
}
```

Correct — keep request data request-scoped:

```php
final class RequestTracker
{
    public function __construct(private array $seen = []) {}

    public function record(string $requestId): void
    {
        $this->seen[] = $requestId;
    }
}
```

Do not inject the request or container into a singleton in a way that captures the first request.
Resolve request-scoped dependencies at call time or inject a resolver. Test consecutive requests;
the first request cannot expose leaked state.

## Operate Long-Running Workers Deliberately

- Restart queue, Octane, and SSR workers on deploy so they load the new release.
- Keep Redis `block_for` finite when workers must react promptly to `SIGTERM`.
- Monitor queue depth, failed jobs, scheduler execution, and cache health.
- Review large-table migrations for locking and online-DDL support before the deploy window.
- Confirm whether workers should pause during maintenance mode.

Do not ban the database queue or require Horizon by default. Choose the queue backend and process
manager from measured throughput, locking, durability, and operational requirements.

## Prove the Transition

Verify the active release, effective configuration/routes/events, worker restart, representative
request, queue consumption, scheduler signal, and migration state. A green build does not prove that
long-running production processes loaded it.
