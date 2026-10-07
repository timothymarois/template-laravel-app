# Inertia v2 to v3 migration

## Identify both installed halves

The client package and server adapter version independently. Record both before interpreting advice:

```sh
npm ls @inertiajs/vue3 @inertiajs/react @inertiajs/svelte
rg -n -i 'inertia' composer.lock Gemfile.lock mix.lock pyproject.toml requirements.txt 2>/dev/null
```

Use the detected backend's package manager to print the exact adapter version. For Laravel, run
`composer show inertiajs/inertia-laravel`; do not run Composer or assume its version represents a
Rails, Phoenix, Django, or other adapter. Read that adapter's registry and changelog for server-side
changes.

Do not keep "latest" numbers in project guidance; check the package registries and the changelogs for
the versions actually being crossed.

## Fix high-impact v3 breaks

The official v3 guide establishes these floors for the Laravel and client adapters: PHP 8.2+,
Laravel 11+, React 19+, and Svelte 5 with runes. Packages are ESM-only and target ES2022. Treat the
PHP/Laravel floor and server configuration changes below as Laravel-adapter guidance; other server
adapters follow their own release notes.

| v2 | v3 |
|---|---|
| `Inertia::lazy()` | `Inertia::optional()` |
| `invalid` event | `httpException` |
| `exception` event | `networkError` |
| `router.cancel()` | `router.cancelAll()` |
| `inertia` head attribute | `data-inertia` |
| `hideProgress()` / `revealProgress()` | `progress.hide()` / `progress.reveal()` |

Axios, `qs`, and `lodash-es` are no longer bundled. This does not ban them: install direct imports
explicitly. Move Axios interceptors to Inertia's built-in interceptors, use its Axios adapter, or
provide a custom client. Convert CommonJS `require()` imports to `import`.

Republish and diff `config/inertia.php`. In v3, page paths and extensions moved under `pages`; the
`testing` section still exists but retains only `ensure_pages_exist`. The `future` namespace is gone
because its behaviors are enabled. Initial page data now uses a JSON script element. React arrow
function layout components assigned directly to `.layout` must be wrapped in an array.

## Upgrade with evidence

1. Read the client and server-adapter changelogs for every crossed release.
2. Upgrade both dependency sets and republish the adapter configuration; reapply local changes from
   the diff.
3. Clear cached server views.
4. Search for every identifier in the v2 column, `data-page`, `require(`, direct imports of removed
   dependencies, Axios interceptors, and custom `future` or testing configuration.
5. Run the test suite. Exercise a mutation redirect, validation failure, file upload, partial reload,
   and SSR page if enabled.

Translate older advice only after checking the v3 guide. The protocol's prop evaluation and redirect
model remain useful; identifiers and configuration may not.

mapping.
