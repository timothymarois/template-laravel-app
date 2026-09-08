# Testing Best Practices

## Choose the Repository's Database Reset Trait

`RefreshDatabase` and `LazilyRefreshDatabase` have different lifecycle and performance tradeoffs.
Preserve the repository's existing trait unless a measured suite problem justifies changing it and
the full database test matrix proves isolation and parallel behavior.

## Match the Assertion to the Behavior

Indirect for a model-existence question: `$this->assertDatabaseHas('users', ['id' => $user->id]);`

Direct: `$this->assertModelExists($user);`

More expressive, type-safe, and fails with clearer messages.

Use model assertions when the question is whether a model exists. Use database assertions when the
exact stored row, table, soft-delete state, or cast-independent value is the behavior under test.

## Use Factory States and Sequences

Named states make tests self-documenting. Sequences eliminate repetitive setup.

Incorrect: `User::factory()->create(['email_verified_at' => null]);`

Correct: `User::factory()->unverified()->create();`

## Use `Exceptions::fake()` to Assert Exception Reporting

Instead of `withoutExceptionHandling()`, use `Exceptions::fake()` to assert the correct exception was reported while the request completes normally.

```php
Exceptions::fake();

$this->get('/orders/invalid')->assertUnprocessable();

Exceptions::assertReported(InvalidOrderException::class);
```

## Call `Event::fake()` After Factory Setup

Model factories rely on model events (e.g., `creating` to generate UUIDs). Calling `Event::fake()` before factory calls silences those events, producing broken models.

Incorrect: `Event::fake(); $user = User::factory()->create();`

Correct: `$user = User::factory()->create(); Event::fake();`

## Use `recycle()` to Share Relationship Instances Across Factories

Without `recycle()`, nested factories create separate instances of the same conceptual entity.

```php
Ticket::factory()
    ->recycle(Airline::factory()->create())
    ->create();
```

Fake only the external boundary under test, then assert the exact dispatch, recipient, request, or
failure. Query-count assertions should protect a known N+1 boundary, not freeze an unrelated total.
Use the project's browser workflow for visual behavior; HTML string assertions are not layout proof.
