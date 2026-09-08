# Laravel source map

Use this file to audit a lesson, not as additional workflow. Framework docs establish behavior;
maintainer and practitioner sources establish conventions Laravel intentionally leaves open.

Verified 22 August 2026 against Laravel 13 documentation and the linked source pages. Versioned
advice is marked in the owning reference instead of maintaining a patch/support ledger here.

## Core routing and skeleton

- [`SKILL.md`: inspect the installed version](https://laravel.com/docs/13.x/releases) — framework
  release notes establish feature and PHP-version boundaries; the installed Composer package remains
  the source for the application under review.
- [`SKILL.md`: current streamlined skeleton](https://laravel.com/docs/13.x/structure),
  [middleware registration](https://laravel.com/docs/13.x/middleware#registering-middleware), and
  [Laravel 11's upgrade caveat](https://laravel.com/docs/11.x/upgrade#application-structure) — new
  applications use `bootstrap/app.php`, while upgraded applications do not have to adopt the new
  structure.
- [Spatie's Laravel/PHP guidelines](https://github.com/spatie/guidelines.spatie.be/blob/master/content/code-style/laravel-php.md#about-laravel)
  — practitioner default: follow Laravel's documented path unless the deviation has a reason.

## Where logic belongs

- [Laravel News' worked controller refactor](https://laravel-news.com/controller-refactor) — source
  for the minimized fat-controller pair, extraction options, observer visibility tradeoff, and the
  caveat that project structure is a choice.
- [Brent Roose, Queueable actions in Laravel](https://stitcher.io/blog/laravel-queueable-actions) —
  source for actions as reusable business operations and jobs as asynchronous transport.
- [Spatie's controller guidelines](https://github.com/spatie/guidelines.spatie.be/blob/master/content/code-style/laravel-php.md#controllers)
  — keep controllers to resource actions and extract a new controller when responsibilities diverge.
- [Laravel directory structure](https://laravel.com/docs/13.x/structure) — Laravel imposes few class
  location restrictions. Therefore this catalog presents actions/services as conditional
  practitioner judgment, never framework law.
- [Eloquent events](https://laravel.com/docs/13.x/eloquent#events) — mass update/delete event gaps
  establish why observers cannot guarantee behavior for every write path.

## Eloquent and database lessons

- [Strictness and lazy-loading violations](https://laravel.com/docs/13.x/eloquent-relationships#preventing-lazy-loading)
  and [discarded-attribute protection](https://laravel.com/docs/13.x/eloquent#mass-assignment-exceptions)
  — source for the non-production strictness example.
- [Eager loading](https://laravel.com/docs/13.x/eloquent-relationships#eager-loading) — N+1 example,
  eager-load key requirements, aggregates, and model-level `$with` behavior.
- [Local scopes](https://laravel.com/docs/13.x/eloquent#local-scopes),
  [global scopes](https://laravel.com/docs/13.x/eloquent#global-scopes), and
  [attribute casting](https://laravel.com/docs/13.x/eloquent-mutators#attribute-casting) — framework
  contracts for reusable query constraints, implicit query constraints, and model type conversion.
- [Querying belongs-to relationships](https://laravel.com/docs/13.x/eloquent-relationships#querying-belongs-to-relationships)
  — `whereBelongsTo()` expresses a relationship-backed constraint without repeating its foreign key.
- [Advanced subqueries](https://laravel.com/docs/13.x/eloquent#advanced-subqueries),
  [one-of-many relationships](https://laravel.com/docs/13.x/eloquent-relationships#has-one-of-many),
  and [relationship aggregates](https://laravel.com/docs/13.x/eloquent-relationships#aggregating-related-models)
  — supported ways to ask SQL for one related scalar or aggregate without hydrating a collection.
- [Dynamic relationships](https://reinink.ca/articles/dynamic-relationships-in-laravel-using-subqueries)
  — Jonathan Reinink's worked `addSelect()` / relationship pattern and query-count motivation. The
  owning reference still requires a measured query plan instead of declaring every subquery faster.
- [Framework PR #49695](https://github.com/laravel/framework/pull/49695) and
  [Laravel News' maintainer-sourced announcement](https://laravel-news.com/eager-load-limit) — native
  per-parent eager-load limits arrived in Laravel 11 from `eloquent-eager-limit`.
- [Chunking and lazy collections](https://laravel.com/docs/13.x/eloquent#chunking-results) and
  [cursors](https://laravel.com/docs/13.x/eloquent#cursors) — source for the filter-mutation trap,
  grouped conditions, inability to eager load with `cursor()`, and PDO buffering.
- [Aggregates](https://laravel.com/docs/13.x/queries#aggregates) — source for the
  `get()->count()` / query `count()` pair.
- [Mass assignment](https://laravel.com/docs/13.x/eloquent#mass-assignment) — fillable/guarded write
  boundary and silent-discard behavior.
- [Mass updates](https://laravel.com/docs/13.x/eloquent#mass-updates) and
  [mass deletes](https://laravel.com/docs/13.x/eloquent#deleting-models-using-queries) — model events do
  not run when models are not retrieved. The bulk-update pair is this catalog's minimized application
  of that documented failure.
- [Upserts](https://laravel.com/docs/13.x/eloquent#upserts) — unique-index requirement and MySQL /
  MariaDB `uniqueBy` behavior.
- [Transactions](https://laravel.com/docs/13.x/database#database-transactions) — automatic rollback
  and deadlock retry count. Keeping transactions clear of external calls is the catalog's lock-scope
  conclusion, not a quoted Laravel rule.
- [Migrations: online index creation](https://laravel.com/docs/13.x/migrations#online-index-creation)
  — large index builds can block reads/writes and support is database-specific.
- [Generating migrations](https://laravel.com/docs/13.x/migrations#generating-migrations) and
  [foreign-key constraints](https://laravel.com/docs/13.x/migrations#foreign-key-constraints) —
  Artisan naming/timestamps and the `foreignId()->constrained()` convention.
- [Mastering Laravel: migrations during early development](https://masteringlaravel.io/daily/2024-02-20-how-we-use-migrations-during-early-product-development)
  — Joel Clermont distinguishes disposable pre-launch migrations from the new forward migrations
  used after launch. His [production `down()` rule](https://masteringlaravel.io/daily/2023-11-13-a-good-rule-around-down-migrations)
  documents why an apparently reversible migration can destroy data after users depend on the new
  schema. The owning reference therefore asks for honest reversibility instead of a universal
  `down()` rule.

## HTTP, validation, and authorization lessons

- [Scoped bindings](https://laravel.com/docs/13.x/routing#implicit-model-binding-scoping) constrain
  nested model lookup; [policy authorization](https://laravel.com/docs/13.x/authorization#authorizing-actions-using-policies)
  checks the current user. The good/bad pair combines these separate contracts; scoping is not a user
  authorization decision.
- [Validation](https://laravel.com/docs/13.x/validation#rule-unique) — exact warning against user
  input in `unique()->ignore()`. The same page's `extensions`, `image`, and array validation sections
  establish the extension-only, SVG/XSS, and permitted-key traps.
- [Spatie's validation guidelines](https://github.com/spatie/guidelines.spatie.be/blob/master/content/code-style/laravel-php.md#validation)
  — community source for array rule syntax.
- [Policy filters](https://laravel.com/docs/13.x/authorization#policy-filters) — `before()` is not
  called without a matching ability method; `null` falls through.
- [API resource conditional relationships](https://laravel.com/docs/13.x/eloquent-resources#conditional-relationships)
  and [Laravel Daily's reproduced N+1 case](https://laraveldaily.com/post/laravel-api-resources-relations-when-methods)
  — source for the `whenLoaded()` pair and the requirement to eager load at the query site.
- [CSRF protection](https://laravel.com/docs/13.x/csrf#csrf-excluding-uris) — narrow
  route exclusion rather than application-wide disablement.
- [Route rate limiting](https://laravel.com/docs/13.x/routing#rate-limiting) — named limiters and
  throttle middleware for abuse-sensitive endpoints. The product must still choose the key and
  threshold from its threat and traffic model.
- [Encrypted casts](https://laravel.com/docs/13.x/eloquent-mutators#encrypted-casting) and
  [serialization visibility](https://laravel.com/docs/13.x/eloquent-serialization#hiding-attributes-from-json)
  — encryption requires a text-capable column and hidden attributes control array/JSON output, not
  storage encryption or authorization.
- [Composer audit](https://getcomposer.org/doc/03-cli.md#audit) — the package manager's supported
  advisory and abandoned-package check.
- [Laravel session flash data](https://laravel.com/docs/13.x/session#flash-data) establishes that a
  flashed value is deleted after the subsequent request. An anonymized first-hand Laravel/Inertia
  reproduction in 2026 found a redirect target consuming flash before a later workflow request, a
  failure hidden by a test that stopped after the POST. The documented lifetime establishes the
  mechanism; the reproduction supports the routing rule's complete multi-request test path.

## Queue lessons

- [Jobs and database transactions](https://laravel.com/docs/13.x/queues#jobs-and-database-transactions)
  — before-commit race, connection-wide `after_commit`, per-dispatch `afterCommit()`, and rollback
  discard behavior.
- [Queued relationships](https://laravel.com/docs/13.x/queues#handling-relationships) — relations enlarge
  payloads and reload without prior constraints; `withoutRelations` / `WithoutRelations` are the
  documented replacement. [Class structure](https://laravel.com/docs/13.x/queues#class-structure)
  establishes identifier re-fetch, binary-data encoding, and current-state semantics.
  [Missing models](https://laravel.com/docs/13.x/queues#ignoring-missing-models) establishes Laravel
  13's `DeleteWhenMissingModels` attribute and its silent-discard behavior.
- [Laravel 13 upgrade: collection model serialization](https://laravel.com/docs/13.x/upgrade#collection-model-serialization-restores-eager-loaded-relations)
  establishes that model collections now restore eager-loaded relations. The current 13.x queue
  guide still contains the pre-13 statement that collection relations are not restored; the owning
  reference follows the explicit upgrade contract and requires installed-version verification.
- [Timeouts](https://laravel.com/docs/13.x/queues#timeout) and
  [`retry_after`](https://laravel.com/docs/13.x/queues#job-expirations-and-timeouts) — timeout must be
  shorter or a job may be processed twice; IO clients also need their own timeouts.
- [Unique jobs](https://laravel.com/docs/13.x/queues#unique-jobs),
  [debounced jobs](https://laravel.com/docs/13.x/queues#debounced-jobs), and
  [overlap middleware](https://laravel.com/docs/13.x/queues#preventing-job-overlaps) — shared locks,
  batch exclusion, and debounce/unique incompatibility.
- [Backoff](https://laravel.com/docs/13.x/queues#dealing-with-failed-jobs),
  [job middleware rate limiting](https://laravel.com/docs/13.x/queues#rate-limiting), and
  [Horizon](https://laravel.com/docs/13.x/horizon) — retry schedules, shared quota controls, and the
  Redis-specific queue dashboard/supervisor boundary.
- [Job chains](https://laravel.com/docs/13.x/queues#job-chaining),
  [batches](https://laravel.com/docs/13.x/queues#defining-batchable-jobs), and
  [Redis blocking](https://laravel.com/docs/13.x/queues#blocking) — delete does not stop a chain,
  callback serialization, implicit commits, and `block_for=0` signal handling.
- [Queue workers and deployment](https://laravel.com/docs/13.x/queues#queue-workers-and-deployment) and
  [maintenance mode](https://laravel.com/docs/13.x/configuration#maintenance-mode) — worker restart
  and pause behavior.

## Performance and deployment lessons

- [Configuration caching](https://laravel.com/docs/13.x/configuration#configuration-caching) and
  [debug mode](https://laravel.com/docs/13.x/configuration#debug-mode) — exact basis for the `env()` /
  `config()` pair and production debug warning.
- [Environment detection](https://laravel.com/docs/13.x/configuration#determining-the-current-environment),
  [environment-file encryption](https://laravel.com/docs/13.x/configuration#encrypting-environment-files),
  and [localization](https://laravel.com/docs/13.x/localization) — supported environment boundaries,
  encrypted environment workflows, and language-file ownership of translatable text.
- [Accessing configuration values](https://laravel.com/docs/13.x/configuration#accessing-configuration-values)
  defines dots as path separators, and Laravel's current
  [`Repository::get`](https://github.com/laravel/framework/blob/13.x/src/Illuminate/Config/Repository.php)
  delegates lookup to `Arr::get`. An anonymized first-hand Laravel 13 reproduction in 2026 confirmed
  that a literal dotted key returns `null` through the path parser and that indexing the owning array
  returns the intended value.
- [Deployment optimization](https://laravel.com/docs/13.x/deployment#optimization) — framework cache
  commands. The application must still own ordering and zero-downtime mechanics.
- [Cache atomic locks](https://laravel.com/docs/13.x/cache#atomic-locks) and
  [stale-while-revalidate](https://laravel.com/docs/13.x/cache#swr) — replacements
  for stampedes and refreshes that need bounded staleness.
- [Atomic `add`](https://laravel.com/docs/13.x/cache#store-if-not-present),
  [cache memoization](https://laravel.com/docs/13.x/cache#cache-memoization),
  [cache tags](https://laravel.com/docs/13.x/cache#cache-tags), and
  [cache failover](https://laravel.com/docs/13.x/cache#cache-failover) — create-if-absent semantics,
  request/job-local memoization, store-dependent group invalidation, and fallback-store behavior.
- [Octane dependency injection](https://laravel.com/docs/13.x/octane#dependency-injection-and-octane)
  and [memory leaks](https://laravel.com/docs/13.x/octane#managing-memory-leaks) — captured request /
  container state and growing static arrays persist across requests.

## Outbound HTTP and error lessons

- [HTTP timeouts](https://laravel.com/docs/13.x/http-client#timeout),
  [retries](https://laravel.com/docs/13.x/http-client#retries), and
  [error handling](https://laravel.com/docs/13.x/http-client#error-handling) — response/connect
  timeout defaults, selective retry callbacks, and the fact that 4xx/5xx responses do not throw by
  default.
- [Amazon Builders' Library: retries and idempotency](https://aws.amazon.com/builders-library/making-retries-safe-with-idempotent-APIs/)
  — operational basis for treating timeouts as potentially unknown remote outcomes and making
  externally visible retries safe with a caller-supplied idempotency key and reconciliation.
- [Concurrent requests](https://laravel.com/docs/13.x/http-client#concurrent-requests) and
  [HTTP testing](https://laravel.com/docs/13.x/http-client#testing) — request pools/batches, fakes,
  sent-request assertions, and prevention of stray requests.
- [Laravel error handling](https://laravel.com/docs/13.x/errors) — central and exception-local
  reporting/rendering, per-exception context, `ShouldntReport`, same-instance deduplication,
  throttling, and custom JSON response decisions.
- [OWASP Logging Cheat Sheet](https://cheatsheetseries.owasp.org/cheatsheets/Logging_Cheat_Sheet.html)
  — security guidance for useful event context and exclusion of access tokens, secrets, and
  sensitive personal data from logs.

## Event, mail, and scheduling lessons

- [Event discovery and queued listeners](https://laravel.com/docs/13.x/events),
  [dispatching after database transactions](https://laravel.com/docs/13.x/events#dispatching-events-after-database-transactions),
  and [queued listeners and transactions](https://laravel.com/docs/13.x/events#queued-event-listeners-and-database-transactions)
  — discovery, queue transport, and after-commit interfaces/configuration.
- [Queued notifications](https://laravel.com/docs/13.x/notifications#queueing-notifications),
  [on-demand notifications](https://laravel.com/docs/13.x/notifications#on-demand-notifications), and
  [notification localization](https://laravel.com/docs/13.x/notifications#user-preferred-locales) —
  queue/after-commit behavior, recipients without models, and locale preferences.
- [Queued mail](https://laravel.com/docs/13.x/mail#queueing-mail),
  [queued mail and transactions](https://laravel.com/docs/13.x/mail#queued-mailables-and-database-transactions),
  [Markdown mailables](https://laravel.com/docs/13.x/mail#markdown-mailables), and
  [mail testing](https://laravel.com/docs/13.x/mail#testing-mailable-sending) — queued dispatch,
  after-commit delivery, an optional HTML/plain-text rendering path, and sent-versus-queued assertions.
- [Preventing task overlaps](https://laravel.com/docs/13.x/scheduling#preventing-task-overlaps),
  [single-server tasks](https://laravel.com/docs/13.x/scheduling#running-tasks-on-one-server),
  [background tasks](https://laravel.com/docs/13.x/scheduling#background-tasks), and
  [schedule groups](https://laravel.com/docs/13.x/scheduling#schedule-groups) — lock expiration,
  shared-cache requirements, supported background execution, environment restrictions, and grouped
  configuration. Queue
  [overlap middleware](https://laravel.com/docs/13.x/queues#preventing-job-overlaps) owns the separate
  runtime of a job dispatched by a scheduled command.

## Testing and view lessons

- [Database testing](https://laravel.com/docs/13.x/database-testing) — `RefreshDatabase`, model
  factories, model/database assertions, and query-count assertions.
- [HTTP exception assertions](https://laravel.com/docs/13.x/http-tests#exception-handling) — the
  `Exceptions` fake can assert reporting while allowing the request to complete normally.
- [Factory states and recycling](https://laravel.com/docs/13.x/eloquent-factories) — named state and
  shared-related-model construction.
- [Event fakes](https://laravel.com/docs/13.x/events#testing),
  [mail fakes](https://laravel.com/docs/13.x/mail#testing-mailable-sending), and
  [notification fakes](https://laravel.com/docs/13.x/notifications#testing) — framework test
  doubles and scoped/subset fakes. Creating event-dependent setup before a broad fake is this
  catalog's consequence of the fake replacing dispatch, not a quoted framework rule.
- [Blade escaping](https://laravel.com/docs/13.x/blade#displaying-data),
  [components](https://laravel.com/docs/13.x/blade#components),
  [component attributes](https://laravel.com/docs/13.x/blade#component-attributes), and
  [`@once` / `@pushOnce`](https://laravel.com/docs/13.x/blade#the-once-directive) — escaped output,
  explicit component inputs, mergeable attributes, and one-time stack registration.
- [Accessing parent component data](https://laravel.com/docs/13.x/blade#accessing-parent-data) and
  [Blade fragments](https://laravel.com/docs/13.x/blade#rendering-blade-fragments) — explicit nested
  component context and partial rendering for compatible frontend request flows.
- [View composers](https://laravel.com/docs/13.x/views#view-composers) — callbacks or classes for data
  genuinely shared by a set of views.

## Framework utility lessons

- [Service-container injection](https://laravel.com/docs/13.x/container#automatic-injection) and
  [interface binding](https://laravel.com/docs/13.x/container#binding-interfaces-to-implementations)
  — visible dependencies and contracts at real substitution boundaries.
- [Helpers](https://laravel.com/docs/13.x/helpers), [strings](https://laravel.com/docs/13.x/strings),
  and [URI manipulation](https://laravel.com/docs/13.x/helpers#uri) — installed-version support for
  expressive framework utilities. The owning reference makes these conditional on clarity and
  version rather than mandatory replacements for PHP.
- [Laravel Pint](https://laravel.com/docs/13.x/pint) — the framework formatter, project presets, and
  dirty/diff scoping. The repository's configured formatter and style remain authoritative.
- [Context](https://laravel.com/docs/13.x/context) — request/job propagation, hidden context, and log
  integration.
- [Deferred functions](https://laravel.com/docs/13.x/helpers#deferred-functions) and
  [concurrency](https://laravel.com/docs/13.x/concurrency) — post-response in-process callbacks and
  driver/process-backed parallel execution.
- [Lazy Eloquent iteration](https://laravel.com/docs/13.x/eloquent#cursors) and
  [Eloquent collection `toQuery`](https://laravel.com/docs/13.x/eloquent-collections#method-toquery),
  and [custom collections](https://laravel.com/docs/13.x/eloquent-collections#custom-collections) —
  cursor buffering/eager-load limits, lazy collections, collection-to-query conversion, and the
  `CollectedBy` attribute.

## Deliberate removals

- Inertia guidance lives in `using-inertia`; duplicating it here creates version drift.
- Patch numbers and support countdowns were removed because they age faster than the workflow. Inspect
  the installed package and current release notes instead.
- The former claim that Laravel 13 documentation forbids the database queue in production was
  removed. Current Laravel 13 deployment and queue docs do not make that statement.
- Blanket rules to index every foreign key, always implement `down()`, forbid repositories, or ban
  business logic from every model were weakened or removed because the cited sources do not justify
  those absolutes.
- No blanket rule prefers `whereIn()` over `whereHas()`, a correlated subquery over a join, or one
  compound-index order for every sort. Representative query plans and engine behavior decide.
- No blanket rule requires newest-first ordering, action classes, interfaces for every dependency,
  queued notifications, `failed()` on every job, Horizon, cache failover, or view composers. Each
  adds behavior or operations that must answer a concrete application need.
- Markdown mailables are an available responsive HTML/plain-text path, not a universal requirement;
  preserve the application's established mail rendering and brand system.
- Undocumented scheduler helpers are not taught as framework contracts. Bound long-running work with
  APIs present in the installed version, overlap protection, and an observable checkpoint strategy.
- `LazilyRefreshDatabase` is not presented as a universal replacement for `RefreshDatabase`; the
  current framework documentation teaches `RefreshDatabase`, and the repository's database/test
  lifecycle remains the governing convention.
- Convenience syntax such as request magic properties, global helpers, higher-order collection
  messages, `Str`, or `Arr` is not automatically more readable. The skill preserves the local style
  and adopts a helper only when it clarifies semantics or prevents a concrete encoding/path failure.
- Controller line counts, action classes, constructor-only injection, custom collection classes, and
  model-derived table names are not framework correctness boundaries. Use them when they expose a
  real dependency or reusable domain operation, not to satisfy a universal shape.
- DDL and data movement are not forbidden from one migration by category alone. Decide from the
  engine's transaction/locking behavior, deployment duration, resumability, and rollback path.
- A time-based retry deadline does not imply one universal `$tries` value across Laravel versions and
  worker configuration. Verify the installed job/worker attempt precedence and test the terminal
  boundary.

## Attribution

This package adapts `skills/laravel-patterns/` from the Rundesk skills catalog at
<https://github.com/rundesk-ai/rundesk-skills>, commit
`680e3d720547dbb563e6e15808e15c8f5bdd4083`, published by Rundesk AI under the MIT License.
