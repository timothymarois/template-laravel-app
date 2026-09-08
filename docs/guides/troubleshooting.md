# Guide: Get unstuck

**When to use:** Something failed in a way that wasted your time, and you want to know whether it is a known trap before you debug it.
**Prerequisites:** None.

> **Found something the template itself gets wrong? Fix it upstream.**
> If the friction is in template-managed code — the `ui/` kit, `composables/`, `utils/`, the gates,
> `scripts/`, `docker/config/`, or these docs — the fix belongs in
> [`template-laravel-app`](https://github.com/timothymarois/template-laravel-app), not only in this fork.
> Patch it locally to unblock yourself, then open a PR against the template with the same change and a
> regression test, so the next project on this template never pays for it again. A fix that lives only in
> one fork is a fix three other forks will each rediscover — every entry below is evidence of exactly that.
> Product-specific code (your own pages, services, migrations) stays in the fork.

Every entry here was paid for by a real failed run in this repository or a fork of it. Most of them fail
**quietly** — a gate passes, a class does nothing, a test proves nothing — which is why they cost an hour
rather than a minute. Delete an entry once the trap is genuinely gone; a long page means something was
fixed and never pruned.

## The shell and Herd

| Symptom | Cause and way out |
|---|---|
| `php is not on PATH, so this script cannot run` | `php` and `composer` are not on a non-interactive shell's `PATH`; they live in Herd. Run the `export PATH=...` line the message prints — it probes Herd, Lerd, Homebrew and the system paths and names the one that exists here. `scripts/preflight-php` guards `check:php` and `build` so this no longer surfaces as `env: php: No such file or directory`, which read as a partial install. |
| `zsh: no matches found: --include=*.vue`, and the command never ran | zsh expands an unquoted glob **before** the command sees it, and aborts the whole line when it matches nothing in the working directory — even when the glob was meant as a literal flag value. `2>/dev/null` does not hide it. Quote it: `--include="*.vue"`. |
| A `herd` command dies with `Undefined array key "USER"` | Valet resolves the account from `USER`, which is unset in a bare exec shell. `export USER="$(id -un)"` first. |
| `herd link` leaves the app emitting `http://` URLs on an HTTPS page | `herd link` rewrites `APP_URL` in `.env` to the `http://` form even when you secure the site next. Restore it after `herd secure`, then `php artisan config:clear`, or canonical tags and `og:image` ship as plaintext. |

## Gates that pass without checking

| Symptom | Cause and way out |
|---|---|
| A type error inside a `.vue` file reaches production | `tsc` cannot read `.vue` at all. The gate is `vue-tsc --noEmit`; if `env.d.ts` ever regains a `declare module '*.vue'` shim, it overrides every component's real props and suppresses the errors again. |
| A `.ts` file is never linted | `pnpm lint` globs `resources/js/**/*.{js,ts,vue}`. Drop `ts` from that list and 156 files stop being checked while ESLint still exits 0. |
| A Vitest file passes locally and fails CI with `Failed to resolve import` | It reached something only Composer provides — `@/ziggy` (generated, git-ignored) or `'ziggy'` (a Vite alias into `vendor/`). The unit-test job is node-only by design, so neither exists there. Reproduce it with `mv vendor /tmp/v && pnpm test; mv /tmp/v vendor`. |
| A Tailwind class does nothing and no tool complains | Tailwind emits a class only if its literal text appears in a scanned source file, and emits nothing for a name that is not a utility — `text-md` shipped in 5 places and produced no rule. `resources/js/tests/conventions/tailwindClasses.test.js` now fails the build on the names that read as real and are not; add to that list when you find another. A class built by interpolation is still never generated. |
| A middleware test is green and the middleware is broken | `tests/Feature/EnsureUserIsActiveTest.php` used only `/`, a session-only route, so the `auth:sanctum` path was never exercised — and a deactivated user was getting a 500 there. Cover each **branch and route group**, not each method. |

## Laravel behaviour

| Symptom | Cause and way out |
|---|---|
| A deactivated user gets a 500 instead of a redirect | Behind `auth:sanctum`, `Authenticate` has already called `shouldUse('sanctum')`, so a bare `Auth::logout()` reaches Sanctum's `RequestGuard`, which has no `logout()`. Log out a **named** guard: `Auth::guard('web')->logout()`. |
| `auth:sanctum` accepts a plain browser session | `config('sanctum.guard')` includes `web` by design. `auth:sanctum` alone does not mean "an API key was presented" — see [api-keys.md](../concepts/api-keys.md). |
| An `abilities:` route never matches, or 500s | Sanctum ships the middleware but does not register the aliases in Laravel 11+. They are aliased in `bootstrap/app.php`. |
| Middleware runs in an order you did not declare | Laravel sorts by its own priority list, in which `Authenticate` precedes appended group middleware. Declared order is not execution order. |
| `assertSessionMissing('k')` passes for a key flashed as `null` | It is `Session::has()` underneath, which reports false for a null value. Assert the thing the skip was worth having instead. |

## Frontend

| Symptom | Cause and way out |
|---|---|
| A prop you bind is silently ignored | `v-bind="forwarded"` **after** an explicit `:prop` overwrites it. Omit that key from the forwarded set — this is what stopped `Calendar`'s quick navigation from moving the grid. |
| A `DateValue`/`DateRange` ref stops satisfying its own type | Both are unions of classes. `ref()` applies `UnwrapRef`, which distributes over the union and strips class identity. Use `shallowRef`. |
| A Vitest case reports behaviour from the previous case | `mount()` does not unmount when a case ends, so a component bound to `window`/`document` answers the next case's events. `resources/js/tests/setup.js` calls `enableAutoUnmount(afterEach)`, which closes this globally — do not remove it. |
| `$route` is undefined in a template under type-check | Ziggy's helper is declared on `ComponentCustomProperties` in `resources/js/env.d.ts`. |
| SSR fails inside `<script setup>` | Inertia SSR runs it in Node. Do not touch `window`, `location`, `document`, `localStorage` or `navigator` at setup scope. |
| A page prop and a shared prop share a name | Inertia merges shared props with page props and **the page wins**, so a composable reading the shared value gets different data on that one route, with no error. |

## Verify

```bash
export PATH="$HOME/Library/Application Support/Herd/bin:$PATH"
pnpm check                                    # zero errors AND zero warnings
pnpm exec vue-tsc --noEmit                    # 0 — tsc cannot replace this
grep -o "\.text-base{[^}]*}" public/build/assets/*.css   # a class exists only if it emits a rule
```
