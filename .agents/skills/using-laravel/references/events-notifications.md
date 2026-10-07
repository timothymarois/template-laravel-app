# Events & Notifications Best Practices

## Rely on Event Discovery

Laravel auto-discovers listeners by reading `handle(EventType $event)` type-hints. No manual registration needed in `AppServiceProvider`.

## Run `event:cache` in Production Deploy

Event discovery scans the filesystem per-request in dev. Cache it in production: `php artisan optimize` or `php artisan event:cache`.

## Use `ShouldDispatchAfterCommit` Inside Transactions

Without it, a queued listener may process before the DB transaction commits, reading data that doesn't exist yet.

```php
class OrderShipped implements ShouldDispatchAfterCommit {}
```

## Queue Slow Notifications When Delivery Semantics Fit

Notifications that call external email, SMS, or chat APIs can block the response. Queue them when
the response does not need delivery to succeed and retries are safe for the selected channels.

```php
class InvoicePaid extends Notification implements ShouldQueue
{
    use Queueable;
}
```

Do not queue every notification by reflex. An in-memory or database-only channel may belong to the
immediate operation. Choose queueing from channel latency, retry safety, and user-visible success
semantics.

## Use `afterCommit()` on Notifications in Transactions

Same race condition as events — call `afterCommit()` to delay dispatch until the transaction commits.

```php
$user->notify((new InvoicePaid($invoice))->afterCommit());
```

## Route Notification Channels to Dedicated Queues

Mail and database notifications have different priorities. Use `viaQueues()` to route them to separate queues.

## Use On-Demand Notifications for Non-User Recipients

Avoid creating dummy models to send notifications to arbitrary addresses.

```php
Notification::route('mail', 'admin@example.com')->notify(new SystemAlert());
```

## Implement `HasLocalePreference` on Notifiable Models

Laravel automatically uses the user's preferred locale for all notifications and mailables — no per-call `locale()` needed.

Queued listeners and notifications may run more than once. Keep their side effects idempotent. Route
channels to separate queues only when their capacity or latency requirements differ; every extra
queue needs workers and monitoring.
