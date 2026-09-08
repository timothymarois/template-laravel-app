# Inertia data loading

## Choose prop evaluation deliberately

| Server form | Standard visit | Partial reload | Evaluation |
|---|---|---|---|
| `User::all()` | included | if requested | always |
| `fn () => User::all()` | included | if requested | only when needed |
| `Inertia::optional(fn () => ...)` | excluded | if requested | only when needed |
| `Inertia::defer(fn () => ...)` | follow-up request | if requested | only when needed |
| `Inertia::always(...)` | included | included | always |
| `Inertia::once(fn () => ...)` | first resolution, then remembered | when requested | when not remembered |

The costly trap is confusing reduced response data with reduced server work:

```php
// Good: an `only: ['users']` reload can skip the companies query.
'companies' => fn () => Company::orderBy('name')->get()

// Bad: the query runs even when `companies` is omitted from the partial response.
'companies' => Company::orderBy('name')->get()
```

Use closures for expensive props that partial reloads may skip. Use `optional` only for data that the
initial page can omit, `defer` for data that may arrive after render, and `once` only for data that is
safe to remember until its expiry or explicit refresh. In v2, `optional` was named `lazy`.

## Keep shared data small

Shared data is included with every response. Use it for small values needed across many pages, such
as a shaped authenticated-user summary—not as a convenient home for menus, full models, reports, or
page-specific collections.

```php
// Good: small, lazy, and explicitly public.
'auth.user' => fn () => $request->user()?->only('id', 'name')

// Bad: every serializable user field is resolved and sent on every navigation.
'auth.user' => $request->user()
```

Move page-specific data back to the page response. For stable global data, use a once prop only when
you also define how it becomes fresh after a mutation.

## Overwrite conditional once props

A remembered once prop can outlive the condition that produced it. Return `null` explicitly when the
condition becomes false:

```php
'auth' => $request->user()
    ? Inertia::once(fn () => $request->user()->only('id', 'name'))
    : null
```

Omitting `auth` after logout leaves the remembered user on the client; `null` overwrites it.

## Defer only secondary work

Defer content the page can render without, not the page's main subject. Group related deferred props
when they should share a follow-up request; use separate groups when they should resolve in parallel.
If a non-essential deferred prop may fail independently, use its rescue behavior and render the
failure state. Do not rescue data required for a correct page.

## Make partial reload assumptions explicit

Partial reloads work only when visiting the same page component. The client merges returned props
with the current page, so accepting stale omitted props must be intentional.

```js
router.reload({
  only: ['users'],
  preserveErrors: true,
})
```

Use `preserveErrors` only when existing client errors should survive an empty server error bag. Test
both the requested props and the expensive closures that should not run.

## Bound repeated requests and merged state

| Trap | Preferred replacement | Failure prevented |
|---|---|---|
| Polling when only one prop changes | `usePoll(..., { only: ['notifications'] })` | Recomputing and transferring unrelated props each interval |
| Disabling background throttling by habit | Keep the default; use `keepAlive` only when required | Full-rate polling in background tabs |
| Prefetch cache surviving a mutation | Invalidate the relevant cache tags | Rendering known-stale prefetched data |
| Changing filters on a merged collection | Visit with `reset: ['users']` | New results merging into the previous filter's results |
| Multiple scrollers sharing a query key | Give each paginator a distinct `pageName` | One scroller changing another's page state |
| Endless automatic loading where a boundary is required | Set `manualAfter` or use pagination | Ignoring the product's intended load boundary |

Inertia already throttles polling by 90% in background tabs. Prefetch on `mount`, `hover`, or `click`
according to actual navigation intent and choose a finite cache lifetime; do not add every strategy
by default.
