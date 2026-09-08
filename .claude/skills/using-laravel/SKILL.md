---
name: using-laravel
description: Use when building, reviewing, debugging, or refactoring Laravel backend behavior, including controllers and routing, form requests and authorization, Eloquent and migrations, queues, caching, outbound HTTP, events, mail, scheduling, Blade behavior, Laravel-specific testing, and deployment-sensitive behavior. It supplies version-aware, example-driven Laravel rules and production traps. Do not use for a non-Laravel PHP project, for visual-only work with no Laravel behavior, or for database-engine tuning that never touches Laravel code.
---

# Use Laravel

Laravel backend rules, organized as an index of rule files. Each rule file teaches what to do and
why.

Read only the files mapped to the current task. Verify exact API syntax and version-sensitive
behavior against the installed framework or the official documentation for the installed version.
Where a documentation-search tool is available in the environment, use it as an accelerator; never
assume one exists, and never let its absence become a reason to guess.

## Consistency First

Before applying any rule, check what the application already does. Laravel offers multiple valid
approaches, and the best choice is the one the codebase already uses, even if another pattern would
be theoretically better. Inconsistency is worse than a suboptimal pattern.

Check sibling files, related controllers, models, or tests for established patterns. If one exists,
follow it. Don't introduce a second way. These rules are defaults for when no pattern exists yet,
not overrides.

## How to Apply

1. Check the changed files, nearby code, project configuration, and relevant tests for established
   patterns. Deviate only for a correctness or security defect, and call the deviation out.
2. Map every affected concern to the rule index below. Read each mapped rule file before editing.
   Skip unrelated rule files.
3. Make the smallest coherent change. Keep the application's architecture and naming instead of
   introducing a second pattern for the same job.
4. Verify version-sensitive Laravel APIs against the installed framework or the documentation for
   that exact version.
5. Run the narrowest relevant tests first, then the project's formatting and static-analysis checks
   when the change warrants them.
6. Re-read the diff against every mapped rule before finishing.

## Rule Index

Cross-cutting changes often need more than one rule file.

| Concern | Read |
| --- | --- |
| Query count, eager loading, indexes, large datasets | [`references/db-performance.md`](references/db-performance.md) |
| Subqueries, aggregates, complex ordering and query plans | [`references/advanced-queries.md`](references/advanced-queries.md) |
| Models, relationships, scopes, casts | [`references/eloquent.md`](references/eloquent.md) |
| Authentication, authorization, input safety, secrets, uploads | [`references/security.md`](references/security.md) |
| Form Requests and validation rules | [`references/request-validation.md`](references/request-validation.md) |
| Controllers, route binding, resources, middleware | [`references/routing.md`](references/routing.md) |
| Schema changes, columns, foreign keys, indexes | [`references/migrations.md`](references/migrations.md) |
| Jobs, retries, uniqueness, batches, Horizon | [`references/queue-jobs.md`](references/queue-jobs.md) |
| Cache lifetime, invalidation, locks, memoization | [`references/caching.md`](references/caching.md) |
| Outbound requests, retries, timeouts, fakes | [`references/http-client.md`](references/http-client.md) |
| Exceptions, reporting, rendering, log context | [`references/error-handling.md`](references/error-handling.md) |
| Events and notifications | [`references/events-notifications.md`](references/events-notifications.md) |
| Mailables and mail assertions | [`references/mail.md`](references/mail.md) |
| Scheduled tasks and overlap protection | [`references/scheduling.md`](references/scheduling.md) |
| Collections, lazy iteration, bulk operations | [`references/collections.md`](references/collections.md) |
| Blade components, attributes, composers | [`references/blade-views.md`](references/blade-views.md) |
| Environment values and application configuration | [`references/config.md`](references/config.md) |
| Pest/PHPUnit patterns, factories, fakes | [`references/testing.md`](references/testing.md) |
| Naming, helpers, file boundaries, PHP style | [`references/style.md`](references/style.md) |
| Actions, services, dependencies, application structure | [`references/architecture.md`](references/architecture.md) |
| Production caches, Octane, deploy transitions, workers | [`references/deployment.md`](references/deployment.md) |
| Code-review triage across Laravel concerns | [`references/review-triage.md`](references/review-triage.md) |

## Decision Rules

- Prefer framework features and existing application abstractions over new helpers or dependencies.
- Avoid speculative abstractions. Extract code when it creates a clear domain boundary, removes
  meaningful duplication, or makes behavior independently testable.
- Keep database access out of Blade views and prevent hidden N+1 queries across controllers,
  resources, jobs, and serialization.
- Check the installed framework version before using a rule's version-sensitive API. New Laravel
  11+ applications use streamlined configuration, while upgraded applications may retain older
  kernel and exception-handler boundaries.
- Use `using-inertia` alongside this skill for props, shared data, forms, partial reloads, SSR,
  or adapter-version changes. Authorize every request on the server.
- Choosing test layers and auditing coverage is a separate, language-neutral concern; this package
  owns only the Laravel-specific testing integration in its testing rule file.
- Read [`references/sources.md`](references/sources.md) only when auditing or changing a factual
  claim or example.
