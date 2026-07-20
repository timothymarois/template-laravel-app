# Guide: Ziggy routes in the frontend

How Laravel route names reach Vue, and the gotchas that waste an agent's time. Usage is one line; the rest is
where things go wrong.

## Using a route

```js
route('admin.users.index')            // "/admin/users"  (relative by default)
route('admin.users.show', user.id)    // "/admin/users/42"
$inertia.visit(route('posts.create')) // navigate
```

`route()` in `<script setup>`, `$route()` in templates. Paths are **relative** (no host) so the same bundle
works on any domain. `route()` works in **both** the browser and SSR — the config is bundled into both, so
you can call it at setup scope on the server (unlike `window`/`document`, which you must not).

## Where route names come from — and the one rule

**Route names come from `routes/*.php`. Nothing else.** To add or rename a route for the frontend, edit the
route file — never `resources/js/ziggy.js`.

- **`resources/js/ziggy.js` is generated and git-ignored. Never hand-edit it.** `php artisan ziggy:generate`
  rewrites it from `routes/*.php`, and it runs automatically on `composer install`, `pnpm dev`, `pnpm build`,
  and `pnpm build-ssr`. Any edit you make is overwritten on the next build and is invisible in git.
- Visibility (which routes are exposed to JS) is controlled in `config/ziggy.php`, not by editing the file.

## "`route()` says my route doesn't exist"

You added a route to `routes/*.php`, but `route('my.new.route')` throws or returns nothing. The bundled route
list was generated **before** you added it. Fix:

```bash
php artisan ziggy:generate    # rebuild resources/js/ziggy.js from the current routes
```

…then restart `pnpm dev` (it only generates at startup). A production `pnpm build` always regenerates first,
so this only bites in a running dev session.

## Two sources, and why

- **The bundle** (`resources/js/ziggy.js` → `globalThis.Ziggy`, set in `resources/js/setup.js`) feeds the
  app's `route()` in the browser and on the server. This is what your components use.
- **The `@routes` Blade directive** (`resources/views/app.blade.php`) injects the request-time route list for
  Ziggy's own inline helper. It also lets you **scope** what a page exposes — `@routes('site')` publishes only
  the `site` route group, so public pages don't leak admin route names. Scope it per layout if that matters.

Both are regenerated from the same `routes/*.php`, so they agree at build/deploy time.

## Pitfalls

- **Don't commit or hand-edit `resources/js/ziggy.js`** — it's generated and git-ignored.
- **Don't guard `route()` as client-only** — it resolves on SSR too. (Do still keep `window`/`location`/
  `document` out of setup scope.)
- **A new route "missing" in JS** is a stale bundle, not a bug — regenerate / restart dev.
- **Don't hardcode a URL** to dodge a stale route — fix the generation, keep the name.
