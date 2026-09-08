---
name: using-inertia
description: Use when building, reviewing, debugging, or upgrading an Inertia.js application, including page responses and props, visits and forms, partial or deferred data, shared state, authorization exposure, history, assets, SSR, and adapter or major-version compatibility. It supplies provider-neutral defaults and source-backed failure patterns for server and client adapters. Do not use for a conventional API SPA, Blade-only pages, Livewire work, or for Laravel, Vue, or React behavior that does not cross the Inertia boundary.
---

# Use Inertia

Treat Inertia as a server-driven page protocol:

- The server still owns routes, validation, authorization, and redirects.
- Page props are a public wire format; send only the fields the page needs.
- An Inertia mutation normally redirects to the next page response. Standalone HTTP calls are a
  separate workflow.

This package owns the Inertia protocol only. Compose it with the server-framework and
client-framework guidance actually in use — `using-laravel` with `using-vuejs` in this repository —
when that guidance is available; none of it is required for this package to be useful, and this
package never owns Laravel or Vue behavior of its own.

## Work in this order

1. Record both the client package and server adapter versions. Their versions are independent, and
   v2 examples contain names removed in v3.
2. Inspect the actual page payload and decide which data is required now, later, or only on demand.
3. Shape public props explicitly and enforce authorization on the server.
4. Use Inertia visits for page-changing requests; use `useHttp` or plain HTTP only when no page visit
   should occur.
5. Test redirects, validation, partial reloads, excluded fields, and SSR output where enabled.

## Prefer failure-preventing replacements

```php
// Good: a stable, reviewable public shape.
'user' => $user->only('id', 'name')

// Bad: every serialized model field reaches the browser, including fields added later.
'user' => $user
```

- Replace eager expensive props with closures before relying on partial reloads.
- Replace broadly shared page data with page props; reserve shared data for small, genuinely global
  values.
- Replace a missing conditional once prop with explicit `null`, or stale remembered data survives.
- Replace JSON success responses to Inertia visits with redirects.
- Replace UI-only permission checks with matching server authorization.
- Replace SSR assumptions with a test configuration that throws on SSR failure.

These lessons and examples are mapped to primary and practitioner evidence in
[`references/sources.md`](references/sources.md).

## Read only the needed depth

- Read [`references/core.md`](references/core.md) for props, forms, validation, authorization,
  history, assets, SSR, and tests.
- Read [`references/data-loading.md`](references/data-loading.md) for slow pages, partial reloads,
  shared or once props, deferred data, polling, prefetching, or infinite scroll.
- Read [`references/migration.md`](references/migration.md) before a v2/v3 upgrade or when an example
  names a missing API.
- Read [`references/anti-patterns.md`](references/anti-patterns.md) when diagnosing symptoms or
  reviewing an existing application.
