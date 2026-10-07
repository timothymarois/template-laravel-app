# Inertia source basis

Use this mapping to verify a lesson before changing it. Documentation establishes current contracts;
maintainer examples and community reports show the failures those contracts prevent. Last checked
7 August 2026.

## Props, security, and the protocol

- [Responses](https://inertiajs.com/docs/v3/the-basics/responses) establishes minimum page data,
  browser-visible props, explicit `only(...)` shaping, `withViewData()`, and history-state limits.
  It supports the good/bad prop pair in `SKILL.md` and the routed default in `core.md`.
- [The protocol](https://inertiajs.com/docs/v3/core-concepts/the-protocol) establishes page objects,
  Inertia request/response headers, partial data, redirects, and asset versions.
- [Authorization](https://inertiajs.com/docs/v3/security/authorization) and
  [Laravel authorization with Inertia](https://laravel.com/docs/13.x/authorization#authorization-and-inertia)
  establish server-side enforcement and permission props for UI rendering.
- [Ping CRM](https://github.com/inertiajs/pingcrm) is a maintained reference application for
  inspecting concrete response shapes. It is an example, not proof that every local convention is a
  universal best practice.

## Visits, forms, and validation

- [Forms](https://inertiajs.com/docs/v3/the-basics/forms),
  [validation](https://inertiajs.com/docs/v3/the-basics/validation), and
  [redirects](https://inertiajs.com/docs/v3/the-basics/redirects) establish redirect-based form
  success and validation, automatic errors, checkbox values, remembered-password exclusion, manual
  router submissions, and the distinction between visits and non-Inertia submissions.
- [HTTP requests](https://inertiajs.com/docs/v3/the-basics/http-requests) explicitly permits
  `useHttp`, XHR, or `fetch` for standalone requests. This corrects the earlier unsupported blanket
  ban on Axios/fetch.
- [File uploads](https://inertiajs.com/docs/v3/the-basics/file-uploads) establishes automatic
  `FormData` conversion and Laravel method spoofing for multipart `PUT`/`PATCH` updates.
- [Partial reloads](https://inertiajs.com/docs/v3/data-props/partial-reloads) establishes Laravel's
  `errors` as an `always` prop and `preserveErrors`. It also corrects the earlier false claim that an
  Inertia validation flow receives a `422` response.
- [Layouts](https://inertiajs.com/docs/v3/the-basics/layouts) establishes that a page-wrapped layout
  is destroyed and recreated on visits while a persistent layout stays alive. [Manual visits](https://inertiajs.com/docs/v3/the-basics/manual-visits)
  establishes when page state is recreated or preserved, and [issue #1211](https://github.com/inertiajs/inertia/issues/1211)
  reproduces remount-driven DOM state loss. An anonymized first-hand Vue/Inertia reproduction in 2026
  traced repeated third-party initialization and focus loss to the same layout lifetime, supporting
  the replacement in `core.md`.

## Data-loading traps

- [Partial reloads](https://inertiajs.com/docs/v3/data-props/partial-reloads) provides the eager,
  closure, optional, and always evaluation matrix and the same-component constraint. It supports the
  good/bad query pair in `data-loading.md`.
- [Shared data](https://inertiajs.com/docs/v3/data-props/shared-data) says shared data is included in
  every response, shows lazy shaped auth data, recommends sparing use, and distinguishes flash data.
- [Once props](https://inertiajs.com/docs/v3/data-props/once-props) establishes remembrance,
  refreshing, expiry, and the explicit-null pattern that prevents stale authenticated-user data.
- [Deferred props](https://inertiajs.com/docs/v3/data-props/deferred-props) establishes request
  grouping, parallel groups, rescue behavior, and reload state.
- [Polling](https://inertiajs.com/docs/v3/data-props/polling),
  [prefetching](https://inertiajs.com/docs/v3/data-props/prefetching), and
  [infinite scroll](https://inertiajs.com/docs/v3/data-props/infinite-scroll) establish background
  throttling, request options, cache invalidation, merged-prop reset, distinct page names, and
  `manualAfter`.
- Jump24's practitioner report,
  [Once props: stop sending the same data over and over](https://jump24.co.uk/journal/inertiajs-once-props-stop-sending-the-same-data-over-and-over-again),
  documents repeated shared payloads as the problem once props solve.
- The Laracasts community discussion
  [Inertia shared data best practice?](https://laracasts.com/discuss/channels/inertia/inertia-shared-data-best-practice)
  narrows the lesson: unnecessary shared data is the trap; small genuinely global data is valid.

## History, assets, SSR, and tests

- [History encryption](https://inertiajs.com/docs/v3/security/history-encryption) establishes the
  back-button risk, key rotation through `clearHistory()`, and the secure-context requirement.
- [Asset versioning](https://inertiajs.com/docs/v3/advanced/asset-versioning) establishes Vite's
  automatic version, full-page refresh behavior, and the v3.6.0 background-request exception.
- [SSR](https://inertiajs.com/docs/v3/advanced/server-side-rendering) establishes browser-global
  failures, production supervision and restart, client-render fallback, and test-only
  `throw_on_error`.
- Maintainer discussion
  [`document is not defined` when starting SSR](https://github.com/inertiajs/inertia/discussions/1849)
  records the browser-global symptom in a real integration and resolution by fixing the incompatible
  dependency/version rather than hiding the error.
- [Testing](https://inertiajs.com/docs/v3/advanced/testing) establishes prop-shape, missing-field,
  partial-reload, deferred-prop, and flash assertions.

## Migration and versions

- The [v3 upgrade guide](https://inertiajs.com/docs/v3/getting-started/upgrade-guide) is the source of
  the runtime floors, ESM/ES2022 boundary, removed dependencies, API renames, configuration changes,
  JSON initial-page data, and React layout caveat in `migration.md`.
- [npm](https://registry.npmjs.org/@inertiajs/vue3) and
  [Packagist](https://repo.packagist.org/p2/inertiajs/inertia-laravel.json) establish that the client
  adapter and Laravel adapter publish independently. Query them at review time; do not copy a dated
  "latest" number into guidance.
- [Laravel News on Inertia 3.0](https://laravel-news.com/inertia-3-0-0) is practitioner release
  coverage for `useHttp`, optimistic updates, layout props, and the Axios removal. The official
  upgrade guide remains authoritative for compatibility and breaking changes.

Omitted on purpose: generic framework introductions, uncited listicles, v2 tutorials presented as
current, and community claims that could not be tied to a reproduced failure or a documented
replacement.

## Attribution

This package adapts `skills/inertia-patterns/` from the Rundesk skills catalog at
<https://github.com/rundesk-ai/rundesk-skills>, commit
`680e3d720547dbb563e6e15808e15c8f5bdd4083`, published by Rundesk AI under the MIT License. The
adaptation renames the package to `using-inertia`, rewrites the routing description and the
composition boundary for this catalog, and adds the maintainer validation record required here. The
technical guidance, examples, and source mapping are carried forward.
