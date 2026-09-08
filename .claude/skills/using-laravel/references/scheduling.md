# Task Scheduling Best Practices

## Use `withoutOverlapping()` on Variable-Duration Tasks

Without it, a long-running task spawns a second instance on the next tick, causing double-processing or resource exhaustion.

## Use `onOneServer()` on Multi-Server Deployments

Without it, every server runs the same task simultaneously. Requires a shared cache driver (Redis, database, Memcached).

## Use `runInBackground()` for Concurrent Long Tasks

By default, tasks at the same tick run sequentially. A slow first task delays all subsequent ones. `runInBackground()` runs them as separate processes.

Use it only for supported scheduled commands whose parallel execution is safe and whose output and
failure remain observable.

## Use `environments()` to Restrict Tasks

Prevent accidental execution of production-only tasks (billing, reporting) on staging.

```php
Schedule::command('billing:charge')->monthly()->environments(['production']);
```

## Bound Long-Running Processing With Supported APIs

A task running every 15 minutes that processes an unbounded cursor can overlap with the next run.
Laravel does not document a built-in `takeUntilTimeout()` scheduler method. If the application uses
that name, first prove it owns a macro or package implementation.

Use the installed version's supported APIs, a bounded batch, and an explicit checkpoint:

```php
$deadline = now()->addMinutes(14);

Order::whereNull('processed_at')->chunkById(500, function ($orders) use ($deadline) {
    foreach ($orders as $order) {
        if (now()->greaterThanOrEqualTo($deadline)) {
            return false;
        }

        ProcessOrder::dispatch($order->id);
    }
});
```

## Use Schedule Groups for Shared Configuration

Avoid repeating `->onOneServer()->timezone('UTC')` across many tasks.

```php
Schedule::daily()
    ->onOneServer()
    ->timezone('UTC')
    ->group(function () {
        Schedule::command('emails:send --force');
        Schedule::command('emails:prune');
    });
```

## Lock the Runtime That Can Actually Overlap

If a scheduled command only dispatches a queued job, its scheduler lock ends after dispatch and
does not protect the job's runtime.

Incorrect:

```php
Schedule::job(new ImportCatalog)->everyFiveMinutes()->withoutOverlapping();
```

Correct — keep the scheduler guard when useful and protect the job execution too:

```php
Schedule::job(new ImportCatalog)->everyFiveMinutes()->withoutOverlapping();

final class ImportCatalog implements ShouldQueue
{
    public function middleware(): array
    {
        return [(new WithoutOverlapping('catalog-import'))->expireAfter(900)];
    }
}
```

`onOneServer()` and overlap protection require a shared supported cache across scheduler hosts.
Choose lock expiry from observed runtime and clear stale locks deliberately after crashes.
