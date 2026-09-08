# Queue & Job Best Practices

## Set `retry_after` Greater Than `timeout`

If `retry_after` is shorter than the job's `timeout`, the queue worker re-dispatches the job while it's still running, causing duplicate execution.

Incorrect (`retry_after` ≤ `timeout`):
```php
class ProcessReport implements ShouldQueue
{
    public $timeout = 120;
}

// config/queue.php — retry_after: 90 ← job retried while still running!
```

Correct (`retry_after` > `timeout`):
```php
class ProcessReport implements ShouldQueue
{
    public $timeout = 120;
}

// config/queue.php — retry_after: 180 ← safely longer than any job timeout
```

## Use Exponential Backoff

Use progressively longer delays between retries to avoid hammering failing services.

Incorrect (fixed retry interval):
```php
class SyncWithStripe implements ShouldQueue
{
    public $tries = 3;
    // Default: retries immediately, overwhelming the API
}
```

Correct (exponential backoff):
```php
class SyncWithStripe implements ShouldQueue
{
    public $tries = 3;
    public $backoff = [1, 5, 10];
}
```

## Implement `ShouldBeUnique`

Prevent duplicate job processing.

```php
class GenerateInvoice implements ShouldQueue, ShouldBeUnique
{
    public function uniqueId(): string
    {
        return $this->order->id;
    }

    public $uniqueFor = 3600;
}
```

## Implement `failed()` When Terminal Failure Needs Domain Handling

Handle errors explicitly — don't rely on silent failure.

```php
public function failed(?Throwable $exception): void
{
    $this->podcast->update(['status' => 'failed']);
    report($exception);
    Log::error('Processing failed', ['podcast_id' => $this->podcast->id]);
}
```

Implement `failed()` for required state repair or a domain signal. Global failed-job monitoring is
still required; adding the same boilerplate logger to every job can duplicate noise.

## Rate Limit External API Calls in Jobs

Use `RateLimited` middleware to throttle jobs calling third-party APIs.

```php
public function middleware(): array
{
    return [new RateLimited('external-api')];
}
```

## Batch Related Jobs

Use `Bus::batch()` when related jobs need shared progress, cancellation, or completion callbacks.
Batches do not roll back successful jobs when another job fails.

```php
Bus::batch([
    new ImportCsvChunk($chunk1),
    new ImportCsvChunk($chunk2),
])
->then(fn (Batch $batch) => Notification::send($user, new ImportComplete))
->catch(fn (Batch $batch, Throwable $e) => Log::error('Batch failed'))
->dispatch();
```

## Reconcile `retryUntil()` With Attempt Limits

Current Laravel 13 gives `retryUntil()` precedence when both it and an attempt limit are defined. Do
not add `$tries = 0` merely because a job has a retry deadline. On older applications, verify the
installed job/worker precedence and test the terminal boundary before adopting this legacy form.

```php
// Legacy only when the installed version and worker configuration require unlimited attempts:
public $tries = 0;

public function retryUntil(): \DateTimeInterface
{
    return now()->addHours(4);
}
```

## Use `ShouldBeUniqueUntilProcessing` for Early Lock Release

`ShouldBeUnique` holds the lock until the job completes. `ShouldBeUniqueUntilProcessing` releases it when processing starts, allowing new instances to queue.

```php
class UpdateSearchIndex implements ShouldQueue, ShouldBeUniqueUntilProcessing
{
    // Lock releases when processing begins, not when it finishes
}
```

## Use Horizon for Complex Queue Scenarios

Use Laravel Horizon when you need monitoring, auto-scaling, failure tracking, or multiple queues with different priorities.

```php
// config/horizon.php
'environments' => [
    'production' => [
        'supervisor-1' => [
            'connection' => 'redis',
            'queue' => ['high', 'default', 'low'],
            'balance' => 'auto',
            'minProcesses' => 1,
            'maxProcesses' => 10,
            'tries' => 3,
        ],
    ],
],
```

Horizon requires Redis queues and operated supervisors. It is not a default for applications whose
queue backend or operational needs do not match it.

## Dispatch Transaction-Dependent Jobs After Commit

Incorrect — a fast worker can query the order before it commits:

```php
DB::transaction(function () use ($data) {
    $order = Order::create($data);
    ProcessOrder::dispatch($order);
});
```

Correct:

```php
DB::transaction(function () use ($data) {
    $order = Order::create($data);
    ProcessOrder::dispatch($order)->afterCommit();
});
```

Use connection-wide `after_commit` when all queued jobs, listeners, mail, notifications, and
broadcasts on that connection should observe committed state.

## Keep Serialized Models Small and Version-Aware

`SerializesModels` stores identifiers and re-fetches current database state. Loaded relationships
increase payloads and may reload without their original constraints.

```php
final class PublishPodcast implements ShouldQueue
{
    public function __construct(
        #[WithoutRelations] public Podcast $podcast,
    ) {}
}
```

Use `$model->withoutRelations()` on versions without the attribute. Laravel 13 restores eager-loaded
relations for models in serialized collections; earlier versions did not. Test the installed
version's deserialized payload instead of repeating one rule across majors.

## Make the Operation Idempotent

`ShouldBeUnique` limits duplicate dispatch and `WithoutOverlapping` limits concurrent execution.
Neither makes a retried side effect exactly once.

```php
if ($this->order->charged_at !== null) {
    return;
}

$payment = $gateway->charge(
    order: $this->order,
    idempotencyKey: "order:{$this->order->id}:charge",
);
```

Unique constraints do not apply inside batches. Lock-based controls require a shared lock-capable
cache and the same logical key everywhere that must not overlap.

## Operate Workers Deliberately

- Restart workers on deploy so they load new code.
- Set blocking HTTP/socket client timeouts; a job timeout may not interrupt blocking I/O.
- Keep Redis `block_for` finite when workers must handle `SIGTERM` promptly.
- Separate queues only when latency or capacity requirements differ.
- Monitor queue depth and failed jobs; a retry policy without monitoring delays discovery.

## Keep Chains and Batches Honest

- `$this->delete()` inside a chained job does not stop later jobs; only failure stops the chain.
- Do not use `$this` in chain or batch callbacks; Laravel serializes those callbacks for later.
- Batched jobs run within transactions, so avoid statements that trigger implicit commits.

```php
Bus::chain([
    new ValidateImport($importId),
    new PersistImport($importId),
])->catch(function (Throwable $error) use ($importId) {
    Import::whereKey($importId)->update(['status' => 'failed']);
})->dispatch();
```

Design compensation explicitly when earlier jobs can succeed before a later job fails.
