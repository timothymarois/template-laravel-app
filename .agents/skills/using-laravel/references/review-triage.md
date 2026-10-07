# Laravel Review Triage

Use this file only when reviewing or debugging across several Laravel concerns. A signal is a prompt
to inspect context, not an automatic finding.

| Signal | Verify | Read |
|---|---|---|
| `env(` outside `config/` | Production uses config caching | [Configuration](config.md) |
| `APP_DEBUG=true` in production | The value reaches the deployed environment | [Configuration](config.md) |
| `all()` or unbounded `get()` | The data can grow and the caller does not need every row | [Database performance](db-performance.md) |
| Relationship access inside a loop or resource | The query did not eager load it | [Database performance](db-performance.md) |
| `get()->count()`, `sum()`, or `isNotEmpty()` | Collection behavior is not otherwise needed | [Advanced queries](advanced-queries.md) |
| `chunk()` while changing its filter column | Updated rows can move between pages | [Collections](collections.md) |
| Bulk update or delete | Correctness depends on model events | [Eloquent](eloquent.md) |
| Global scope for a product-specific view | Every admin, job, and report query should inherit it | [Eloquent](eloquent.md) |
| Relationship collection loaded for one scalar | An aggregate or subquery can answer the question | [Advanced queries](advanced-queries.md) |
| `$guarded = []` plus request-derived writes | A future sensitive column can cross the boundary | [Security](security.md) |
| Business operation in a controller, job, or observer | Reuse, coordinated steps, or side effects justify extraction | [Architecture](architecture.md) |
| Repository wrapping Eloquent | It creates a real persistence boundary | [Architecture](architecture.md) |
| Scoped route binding without authorization | Parent-child scope is mistaken for user access | [Routing](routing.md) |
| `$request->all()` at persistence | Only validated, fillable fields should cross | [Validation](request-validation.md) |
| Request input passed to `unique()->ignore()` | The ignored value is not a trusted model or key | [Validation](request-validation.md) |
| Extension-only upload validation or enabled SVG | MIME/content and sanitization policy are present | [Security](security.md) |
| Job dispatched inside a transaction | Dispatch is configured after commit | [Queues](queue-jobs.md) |
| Queued model with loaded relations | The payload and reloaded relation set are intended | [Queues](queue-jobs.md) |
| Queue timeout at or above `retry_after` | Another worker can start before the first stops | [Queues](queue-jobs.md) |
| Unique job inside a batch | The code assumes uniqueness Laravel does not apply | [Queues](queue-jobs.md) |
| HTTP response decoded without a status decision | A 4xx/5xx body can be treated as success | [HTTP client](http-client.md) |
| Retry around an externally visible write | One stable idempotency key prevents duplicates | [HTTP client](http-client.md) |
| Catch-and-fallback without a report or metric | The dependency can fail silently | [Error handling](error-handling.md) |
| Event, notification, or mail inside a transaction | Delivery waits until state commits | [Events](events-notifications.md) |
| Scheduled task can outlive its interval | Scheduler and job runtime overlaps are controlled | [Scheduling](scheduling.md) |
| Framework fake before event-dependent factories | Setup still receives required events | [Testing](testing.md) |
| Raw Blade echo for user-controlled HTML | A sanitizer owns the XSS boundary | [Blade](blade-views.md) |
| `app()` or `resolve()` deep in domain code | The dependency is visible at the class boundary | [Architecture](architecture.md) |
| `defer()` for required work | Losing the PHP process cannot lose the operation | [Architecture](architecture.md) |
| Mutable static or singleton state under Octane | State survives its originating request | [Deployment](deployment.md) |

Name the observed symptom and mechanism, then give the supported replacement and proof. Separate
correctness and security defects from structure preferences.
