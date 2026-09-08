# HTTP Client Best Practices

## Always Set Explicit Timeouts

The default timeout is 30 seconds — too long for most API calls. Always set explicit `timeout` and `connectTimeout` to fail fast.

Incorrect:
```php
$response = Http::get('https://api.example.com/users');
```

Correct:
```php
$response = Http::timeout(5)
    ->connectTimeout(3)
    ->get('https://api.example.com/users');
```

For service-specific clients, define timeouts in a macro:

```php
Http::macro('github', function () {
    return Http::baseUrl('https://api.github.com')
        ->timeout(10)
        ->connectTimeout(3)
        ->withToken(config('services.github.token'));
});

$response = Http::github()->get('/repos/laravel/framework');
```

## Use Retry with Backoff for External APIs

External APIs have transient failures. Use `retry()` with increasing delays.

Incorrect:
```php
$response = Http::post('https://api.example.com/v1/charges', $data);

if ($response->failed()) {
    throw new PaymentFailedException('Charge failed');
}
```

Correct for an externally visible write only when one persisted idempotency key identifies every
attempt:
```php
$response = Http::withHeaders(['Idempotency-Key' => $attempt->idempotency_key])
    ->retry([100, 500, 1000])
    ->timeout(10)
    ->post('https://api.example.com/v1/charges', $data);
```

Only retry on specific errors:

```php
$response = Http::retry(3, 100, function (Throwable $exception, PendingRequest $request) {
    return $exception instanceof ConnectionException
        || ($exception instanceof RequestException && $exception->response->serverError());
})->post('https://api.example.com/data');
```

## Handle Errors Explicitly

The HTTP Client does not throw on 4xx/5xx by default. Always check status or use `throw()`.

Incorrect:
```php
$response = Http::get('https://api.example.com/users/1');
$user = $response->json(); // Could be an error body
```

Correct:
```php
$response = Http::timeout(5)
    ->get('https://api.example.com/users/1')
    ->throw();

$user = $response->json();
```

For graceful degradation:

```php
$response = Http::get('https://api.example.com/users/1');

if ($response->successful()) {
    return $response->json();
}

if ($response->notFound()) {
    return null;
}

$response->throw();
```

## Use Request Pooling for Concurrent Requests

When making multiple independent API calls, use `Http::pool()` instead of sequential calls.

Incorrect:
```php
$users = Http::get('https://api.example.com/users')->json();
$posts = Http::get('https://api.example.com/posts')->json();
$comments = Http::get('https://api.example.com/comments')->json();
```

Correct:
```php
use Illuminate\Http\Client\Pool;

$responses = Http::pool(fn (Pool $pool) => [
    $pool->as('users')->get('https://api.example.com/users'),
    $pool->as('posts')->get('https://api.example.com/posts'),
    $pool->as('comments')->get('https://api.example.com/comments'),
]);

$users = $responses['users']->json();
$posts = $responses['posts']->json();
```

Pooling helps only when the calls are independent. Preserve request identity and inspect every
response before decoding it as success.

## Fake HTTP Calls in Tests

Never make real HTTP requests in tests. Use `Http::fake()` and `preventStrayRequests()`.

Incorrect:
```php
it('syncs user from API', function () {
    $service = new UserSyncService;
    $service->sync(1); // Hits the real API
});
```

Correct:
```php
it('syncs user from API', function () {
    Http::preventStrayRequests();

    Http::fake([
        'api.example.com/users/1' => Http::response([
            'name' => 'John Doe',
            'email' => 'john@example.com',
        ]),
    ]);

    $service = new UserSyncService;
    $service->sync(1);

    Http::assertSent(function (Request $request) {
        return $request->url() === 'https://api.example.com/users/1';
    });
});
```

Test failure scenarios too:

```php
Http::fake([
    'api.example.com/*' => Http::failedConnection(),
]);
```

Call `Http::preventStrayRequests()` so an unrecognized URL cannot silently reach the network during
a test.

```php
Http::preventStrayRequests();
Http::fake([
    'inventory.example/*' => Http::response(['available' => true]),
]);
```

## Reconcile Unknown Remote Outcomes

A timeout after an externally visible write does not prove the provider rejected it. The remote
operation may have completed after the caller stopped waiting.

Incorrect — a blind retry can create a second charge:

```php
Http::retry(3, 500)->post($paymentsUrl, $payload);
```

Correct — persist one attempt identity, reuse it when the provider supports idempotency, and
reconcile provider status:

```php
$response = Http::withHeaders([
    'Idempotency-Key' => "order:{$order->id}:charge",
])->retry([200, 1_000], when: fn (Throwable $error) => $error instanceof ConnectionException)
  ->timeout(8)
  ->post($paymentsUrl, $payload)
  ->throw();
```

Retry selected transient connection or server failures, not every 4xx response. Pool only independent
requests, preserve response identity, and inspect every result.
