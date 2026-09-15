# Codemap — template-laravel-app

> The one document written for builders. Inventory first, then the traps only a builder meets, under the
> layer each concerns. Conventions live in [`AGENTS.md`](../AGENTS.md); what the software does for a
> person lives in the wiki under [`wiki/`](wiki/). Counts are artifacts, not lines — when one stops
> matching the tree, that section is stale.

Laravel 13 (PHP 8.4) + Vue 3.5 + Inertia + Tailwind 4 + shadcn-vue + Vite 7 + TypeScript (strict).

**Key integrations:** Laravel Reverb + Echo (WebSockets), Horizon (Redis queues), Sentry (errors), spatie/laravel-health + Discord notifications, Cashier (billing), Socialite (OAuth), Sanctum (auth/tokens), flysystem S3, archtechx/enums, Spatie Sitemap, TipTap (editor), Unovis (charts), Maska (masks), vue-sonner (toasts), vue-draggable-plus, lucide + tabler icons, Ziggy (routes).

## Models (app/Models/ — 1)

- **User** — HasApiTokens, Notifiable. Fields: name, email, password, timezone, is_active, `role` (UserRole cast), last_seen_at/ip/user_agent. `isOnline()`, `isAdmin()`.

## Enums (app/Enums/ — 2)

- **UserRole** — `default()`, `label()`, `canManageAllUsers()`, `canAccessAdmin()`, `canImpersonate()`. **ApiAbility** — the closed set of API-key abilities.

## Services (app/Services/ — 4)

- **Models/ModelService** — abstract base; **Models/UserService** — user CRUD.
- **ApiKeyService** — issue/list/revoke API keys on Sanctum PATs; **DataTransferObjects/IssuedApiKey** — a key at the one moment its plaintext exists.

## Controllers (app/Http/Controllers/ — 11)

- **PageController** — public home; **ReleaseController** — deployed version as JSON; **Auth/SessionController** (login/logout), **Auth/RegisterController**, **Auth/PasswordResetController** (forgot/reset, non-enumerating).
- **Admin/DashboardController**, **Admin/UserController** (CRUD + `simpleTable`, `prepareIndexFilters`), **Admin/ApiKeyController** (issue/revoke), **Admin/ComponentController** (showcase); **Api/UserController** (the collection endpoint a key is tested against).
- Base **Controller** (carries `AuthorizesRequests`; without it `$this->authorize()` has nothing to call).

## HTTP Concerns (app/Http/Concerns/ — 1)

- **InertiaDataTableOptions** — data-table state (search, filters, pagination, sorting, session persistence).

## Form Requests (app/Http/Requests/ — 8)

- Auth: `LoginRequest`, `RegisterRequest`, `ForgotPasswordRequest`, `ResetPasswordRequest`; User: `StoreUserRequest`, `UpdateUserRequest`; ApiKey: `StoreApiKeyRequest`; Api: `ListUsersRequest`.

## Middleware (app/Http/Middleware/ — 6)

- **HandleInertiaRequests** (shares an allow-listed `user`, `appUrl`, `seo`, `flash.{status,error}`), **TrackLastSeen**, **EnsureUserIsActive**, **EnsureUserIsAdmin** (aliased `admin`, gates the whole admin prefix), **EnsureApiKey** (aliased `api.key`, requires a real PAT and an active owner), **SecurityHeaders**.

Traps:

| Symptom | Cause and way out |
|---|---|
| A deactivated user gets a 500 instead of a redirect | Behind `auth:sanctum`, `Authenticate` has already called `shouldUse('sanctum')`, so a bare `Auth::logout()` reaches Sanctum's `RequestGuard`, which has no `logout()`. Log out a **named** guard: `Auth::guard('web')->logout()`. |
| `auth:sanctum` accepts a plain browser session | `config('sanctum.guard')` includes `web` by design, and a session is a `TransientToken` whose `can()` is true for every ability. `auth:sanctum` alone never means "an API key was presented" — `api.key` is what makes an `abilities:` check mean anything. |
| An `abilities:` route never matches, or 500s | Sanctum ships the middleware but does not register the aliases in Laravel 11+. They are aliased in `bootstrap/app.php`. |
| Middleware runs in an order you did not declare | Laravel sorts by its own priority list, in which `Authenticate` precedes appended group middleware. Declared order is not execution order. |
| A page prop and a shared prop share a name | Inertia merges shared props with page props and **the page wins**, so a composable reading the shared value gets different data on that one route, with no error. |

## Policies (app/Policies/ — 2)

- **UserPolicy** — viewAny/view/create/update/delete against `UserRole::canManageAllUsers()`; delete also refuses self-deletion.
- **ApiKeyPolicy** — viewAny/create/delete for API keys; delete also requires the key belong to the actor. Bound explicitly in `AppServiceProvider` (the model is in the Sanctum namespace, so convention discovery misses it).

## Listeners (app/Listeners/ — 1)

- **LogBackgroundFailures** — one structured line per failed job (`event=job.failed`) and failed scheduled task (`event=schedule.failed`), alongside the trace Laravel already reports.

## Health (app/Health/ — 5)

- **Checks/ReverbCheck**; **DiscordHealthChannel**, **DiscordWebhook**; **Listeners/NotifyOnHealthRecovery**, **Listeners/NotifyOnMaintenanceMode**.

## Providers (app/Providers/ — 2)

- **AppServiceProvider** (password rules, rate limiters, health checks, policies, failure listeners), **HorizonServiceProvider**.

## Commands (app/Console/Commands/ — 5)

- **StartFresh** (`start:fresh`), **GenerateSitemap** (`sitemap:generate`), **EnsureStorage** (`app:ensure-storage`), **CreateUser** (`user:create --admin`), **CreateApiKey** (`api-key:create`).

## Support (app/Support/ — 2)

- **Caster** (filter type casting), **PhoneNumber** (formatting/validation).

## Routes (routes/ — 4)

`web.php`, `components.php`, `api.php`, `channels.php`. The schedule lives in `bootstrap/app.php` → `withSchedule()`; there is no `routes/console.php`.

- **Public:** `GET /` (home), `GET /up` (container gate), `GET /health` (spatie health JSON, `RequiresSecretToken`), `GET /release` (deployed version).
- **Guest:** `GET /register|/login`, `POST /auth/register|/auth/login`; password reset — `GET /forgot-password` (`password.request`), `POST /auth/forgot-password` (`password.email`), `GET /reset-password/{token}` (`password.reset`), `POST /auth/reset-password` (`password.store`). The three unauthenticated POSTs share one `throttle:auth` bucket (5/min/IP) — named limiters key on the limiter name + IP, not the route, which is why login is **not** in it: failed logins would lock a user out of password reset.
- **Auth (`auth:sanctum`):** `POST /logout`; admin group (additionally `admin` middleware) — `GET /admin/` (dashboard), `/admin/users/*` (resource without `create`/`edit`, + `users/table`, `users/filters`), `/admin/api-keys` (index/store/destroy), `/admin/components/*` (showcase).
- **API:** `GET /api/user`, `GET /api/users` — `throttle:api` → `auth:sanctum` → `api.key` → `abilities:api:read`. **Broadcast:** `App.Models.User.{id}`.

## Pages (resources/js/pages/ — 60 .vue)

- **Public:** `Index.vue`, `Login.vue`, `Register.vue`, `ForgotPassword.vue`, `ResetPassword.vue`. **Errors:** `errors/{404,500,503}.vue`.
- **Admin:** `admin/Index.vue`, `admin/users/{Index,Show}.vue`, `admin/api-keys/Index.vue`.
- **Component showcase** (`admin/components/`): `forms/` (Input, InputMasks, Textarea, Select, Checkbox, Combobox, Switch, Slider, Fields, Editor, Upload, PinInput, input/Tags, calendar/{DateInput,DateRangeInput}), `actions/` (Button, Command, Dialog, Menu, Sheet), `display/` (Alert, Card, Badge, Avatar, Tooltip, Popover, Loading, Tabs, Accordion, Toast, Carousel, Resizable, CodeBlock, ViewToggle), `data/` (Table, Actions, Pagination), `charts/` (Bar, Line, Area, Pie).

## UI Components (resources/js/components/ui/ — 49)

Data: accordion, alert, avatar, badge, card, carousel, chart, code-block, data-table, pagination, progress, skeleton, table, tooltip, view-toggle | Forms: checkbox, combobox, date-picker, input (NumberInput, MaskInput), label, pin-input, radio-group, range-calendar, select, select-popover, slider, switch, tags-input, textarea | Overlays: alert-dialog, dialog, popover, sheet, dropdown-menu, context-menu | Nav: tabs, sidebar | Layout: collapsible, resizable, scroll-frame, separator, command | Editor: editor (TipTap) | Upload: upload | Feedback: sonner, spinner, form-errors | Actions: button | Other: calendar.

Three tiers, dependencies inward only: `ui/` (kit, `lang="ts"`, barrel imports, knows nothing of Inertia/auth/routes) → `app/` (authenticated surfaces, direct file imports) → `site/` (public pages). A shadcn/reka primitive is suffixed `*Base`; the kit's wrapper adds the project's props and states. `app/` and `site/` are plain JS on purpose, which is why `noImplicitAny` is off.

Traps:

| Symptom | Cause and way out |
|---|---|
| A page renders nothing, no build error | A name imported from the barrel that it does not export is `undefined`. Check `components/ui/index.ts`; four components are excluded from it on purpose, each with a comment naming the reason. |
| A prop you bind is silently ignored | `v-bind="forwarded"` **after** an explicit `:prop` overwrites it. Omit that key from the forwarded set — this is what stopped `Calendar`'s quick navigation from moving the grid. |
| Passing `:class="{ active: x }"` is a type error | That component still declares `class?: string`. Widen it to `HTMLAttributes['class']`. |
| A `DateValue`/`DateRange` ref stops satisfying its own type | Both are unions of classes. `ref()` applies `UnwrapRef`, which distributes over the union and strips class identity. Use `shallowRef`. |
| A Tailwind class does nothing and no tool complains | Tailwind emits a class only if its literal text appears in a scanned source file, and emits nothing for a name that is not a utility — `text-md` shipped in 5 places and produced no rule. `resources/js/tests/conventions/tailwindClasses.test.js` fails the build on the names that read as real and are not; add to that list when you find another. A class built by interpolation is still never generated. |
| SSR fails inside `<script setup>` | Inertia SSR runs it in Node. Do not touch `window`, `location`, `document`, `localStorage` or `navigator` at setup scope. `route()` is safe there — its config is bundled into both builds. |
| A dialog's Enter listener never fires | reka renders through a portal whose wrapper root emits no DOM node, so a listener in the template never receives the event. `DialogConfirmation` binds to `document` in the capture phase while open; `shouldConfirmOnEnter` in `ui/dialog/dialogUtils.ts` is the shared guard. |

## App Components (resources/js/components/app/ — 16 .vue, by subfolder)

- **SeoHead** — the single owner of the document head (canonical, OG, Twitter, robots).
- **layout/** — AppLayout, AppShell, AppTopbar.
- **modals/** — DeleteUserModal, EditUserModal.
- **navigation/** — ModeToggle, ProfileMenu, Sidebar, Topbar.
- **page/** — Content, Footer, Header, PageSidebar, SideContent, SideNav.

## Site Components (resources/js/components/site/ — 1)

- **layout/SiteLayout** — public layout with SEO meta.

## Composables (resources/js/composables/)

- **ui/**: useModal, useModelValue, useScroll, useSelectableOptions. **inertia/**: useDataTableOptions.
- **useFormSubmit**; **useEcho.js**: getEcho, useChannel, usePrivateChannel, usePresenceChannel, useListen.

`useDataTableOptions`: PHP serializes an empty `filters` as `[]`; letting that land on `form.filters` means later mutations become non-numeric array props, which `JSON.stringify` drops — the request silently loses every filter. The coercion to `{}` sits **after** the `...options` spread on purpose.

## Utils (resources/js/utils/)

- **format/** — formatBytes, formatCurrency, formatDate, formatDatetime, formatDateValue, formatNumber, formatPercentage, formatSlug, formatUSPhoneNumber, formatValidURL, formatYmdDate, parseUtcDate, timezone (`isDateOnly`, `resolveTimeZone`).
- **validate/** — isEmpty, isNumeric, isValidEmail, isValidURL. **file/** — getFileExtension, normalizeFiles, validateFileSize, validateFileType.
- **array/** getRandomItem | **browser/** isClient | **vue/** expandTransition, hasSlotContent, inertia/isPageActive | **math/** roundTo | **cn.ts**.

## Frontend Entry Points & Plugins (resources/js/ — 6)

- `app.js`, `bootstrap.js`, `setup.js`, `ssr.js`, `ziggy.js` (generated), `env.d.ts` (declares Ziggy's `$route` on `ComponentCustomProperties`; no `*.vue` shim — one would override every component's real props and suppress every error).
- **plugins/inertia/** — `ziggy.js` exposes the helper three ways (`globalThis.route()`, `inject('route')`, `$route()` in templates) and flips Ziggy's `absolute` default to `false`, which keeps the build-time host out of every href.

### Ziggy — how route names reach Vue

- **Route names come from `routes/*.php`. Nothing else.** `resources/js/ziggy.js` is generated by `php artisan ziggy:generate` (run on `composer install`, `pnpm dev`, `pnpm build`, `pnpm build-ssr`) and git-ignored; never hand-edit or commit it. Visibility is controlled in `config/ziggy.php`.
- `import { route } from 'ziggy'` — **not** `'ziggy-js'`. `ziggy` is a Vite alias to `vendor/tightenco/ziggy/src/js`, delivered by Composer, so a checkout without `composer install` cannot build.
- The `@routes` Blade directive in `app.blade.php` injects the request-time route list for Ziggy's inline helper and can scope what a page exposes (`@routes('site')`); the bundle (`globalThis.Ziggy`, set in `setup.js`) feeds the app's `route()` in the browser and on the server. Both come from the same route files.

| Symptom | Cause and way out |
|---|---|
| `Ziggy error: route 'x' is not in the route list` | The bundle was generated before the route was added. `php artisan ziggy:generate`, then restart `pnpm dev` (it generates at startup only). A production build always regenerates first. |
| `from 'ziggy-js'` — `Failed to resolve import` | The specifier is `'ziggy'`, and it needs `vendor/` present. |
| A Vitest file passes locally and fails CI with `Failed to resolve import` | It reached something only Composer provides — `@/ziggy` (generated, git-ignored) or `'ziggy'` (a Vite alias into `vendor/`). The `Lint, types, tests` job is node-only by design. `vite.config.js` aliases `ziggy` to `resources/js/tests/stubs/ziggy.js` for `test` only. Reproduce CI before pushing: `mv vendor /tmp/v && pnpm test; mv /tmp/v vendor`. |
| `$route` is undefined in a template under type-check | Ziggy's helper is declared on `ComponentCustomProperties` in `resources/js/env.d.ts`. |
| A server-rendered page carries empty hrefs | `route()` resolves under SSR; check the SSR bundle was rebuilt. Prove it: `node bootstrap/ssr/ssr.js &` then POST `{"component":"Index","props":{"errors":{},"auth":{"user":null}},"url":"/","version":"1","clearHistory":false,"encryptHistory":false}` to `http://127.0.0.1:13714/render` and grep the hrefs. |

## Migrations (database/migrations/ — 4)

- users (+ `password_reset_tokens`, `sessions`), cache, jobs, personal_access_tokens.

## Scripts (scripts/ — 6)

Stack-neutral bash + `python3`, no dependencies. `production` is the deployed branch; `main` stays at
version `0.0.0`. Identity and target come from `template-manifest.json` -> `deploy`. The procedure a
person follows: [`wiki/pages/releases.md`](wiki/pages/releases.md).

| Script | What it does |
|--------|--------------|
| prepare-production-release | Bumps both manifests on a `release/vX.Y.Z` branch cut from `origin/main` |
| production-release-version | Reads the stored version, refusing `0.0.0` or manifests that disagree |
| publish-production-release | Verifies the deploy is live via `/release`, `/up`, `/health` (with stored `checkResults` — an empty 200 is *not yet checked*, never *healthy*), then tags and publishes |
| assert-neutral-main-version | Guards the invariant that `main` never carries a release version |
| preflight-php | Turns `env: php: No such file or directory` into the `export PATH=...` line you need; never edits PATH |
| dev-wiki.sh | Runs the pinned wiki-builder release through `uvx`, passing `--root`; the one line to bump when the tool moves |

## Config (config/ — 22)

Notable: `release.php` (reads `version` from `composer.json`, served by `/release`), `seo.php` (head defaults + the `indexable` switch), `health.php`, `horizon.php`, `reverb.php`, `broadcasting.php`, `sentry.php`, `solo.php`.

## Testing

- **Backend (Pest — 26 test files: Feature 18, Unit 8):** Feature — AgentInstructions, ApiKey, ApiUserList, AuthenticationFlow, BackgroundFailureLogging, CreateApiKeyCommand, CreateUserCommand, EnsureStorage, EnsureUserIsActive, Example, GenerateSitemap, GoogleAnalytics, HealthEndpoint, PasswordReset, ReleaseVersion, Seo, TrackLastSeen, UserController. Unit — Example, PhoneNumber, Services/UserService, Enums/UserRole, Health/{DiscordHealthChannel, NotifyOnHealthRecovery, NotifyOnMaintenanceMode, ReverbCheck}.
- **Frontend (Vitest + Vue Test Utils, happy-dom — resources/js/tests/, 52 test files):** UI component helpers, composables, utils, and conventions.
- **Shell contracts (tests/scripts/ — 8 + stubs/):** the release scripts with `gh` and `curl` stubbed, `preflight-php`, and the deploy image's own configuration (`php-ini`, `nginx-config`, `post-deployment`). `tests/docker/` builds and probes the real image.

### Conventions

| Change | Required test |
|---|---|
| New endpoint | Feature test for HTTP behaviour — cover each **branch and route group**, not each method (`EnsureUserIsActiveTest` was green while the `auth:sanctum` path 500'd, because every case used `/`) |
| New Service or Action | Unit test for the business logic |
| New component | Component test for rendering + interaction; anything binding `window`/`document` relies on `enableAutoUnmount(afterEach)` in `resources/js/tests/setup.js` — do not remove it |
| New composable | Unit test for reactive behaviour |
| Bug fix | Regression test proved by reverting the fix and watching only that case fail |

Factories, never hand-written inserts; `User::factory()->admin()->create()` for an operator. Run the changed
case, then `pnpm check:php` / `pnpm test`, then `pnpm check`.

| Symptom | Fix |
|---|---|
| A test passes whether or not the fix is present | It asserts the implementation, not the behaviour. Revert the fix and watch it fail before you trust it. |
| Tests pass alone but fail in the suite | Shared or order-dependent state. Use factories and per-test setup; never rely on a prior test's rows. |
| A Vitest case reports behaviour from the previous case | `mount()` does not unmount when a case ends; `setup.js` closes this globally. |
| `assertSessionMissing('k')` passes for a key flashed as `null` | It is `Session::has()` underneath, which reports false for a null value. Assert the thing the skip was worth having instead. |
| A flaky test gets re-run until green | That is a defect in the test or the code. Fix the cause; a retry hides it. |

## Code Quality

- **PHP:** Pint (PSR-12), Larastan (level 5). **JS:** ESLint (strict TS + Vue 3), Stylelint. **Docs:** `wiki check` (every sentence cited, headings named, budgets held).
- **Master:** `pnpm check` (php + js + release + deploy + wiki, then build). `check:wiki` needs `uv`.
- Rate limits (`AppServiceProvider`): `api` (60/min per user or IP), `auth` (5/min per IP), `uploads` (10/min).

Traps in the gates:

| Symptom | Cause and way out |
|---|---|
| `php is not on PATH, so this script cannot run` | `php` and `composer` live in Herd, off a non-interactive shell's `PATH`. Run the `export PATH=...` line the message prints. `scripts/preflight-php` guards `check:php` and `build`. |
| `zsh: no matches found: --include=*.vue`, and the command never ran | zsh expands an unquoted glob **before** the command sees it and aborts the whole line when it matches nothing — even for a literal flag value. `2>/dev/null` does not hide it. Quote it: `--include="*.vue"`. |
| A `herd` command dies with `Undefined array key "USER"` | Valet resolves the account from `USER`, unset in a bare exec shell. `export USER="$(id -un)"` first. |
| `herd link` leaves the app emitting `http://` URLs on an HTTPS page | `herd link` rewrites `APP_URL` in `.env` to `http://` even when you secure the site next. Restore it after `herd secure`, then `php artisan config:clear`, or canonical tags and `og:image` ship as plaintext. |
| A type error inside a `.vue` file reaches production | `tsc` cannot read `.vue`. The gate is `vue-tsc --noEmit`; if `env.d.ts` regains a `declare module '*.vue'` shim it suppresses the errors again. |
| A `.ts` file is never linted | `pnpm lint` globs `resources/js/**/*.{js,ts,vue}`. Drop `ts` from that list and 156 files stop being checked while ESLint still exits 0. |
| `wiki check` reports every page "has changed since its date was recorded" | Run `./scripts/dev-wiki.sh build` first and commit `docs/wiki/UPDATED.toml`; edits move page dates. |
| `wiki check` refuses a word inside a quoted label | It reads no code, so format interface text as code and it passes. |
| A `goals = false` page passes with uncited sentences | Those pages are exempt from the citation checks. Copy `docs/wiki` to a scratch folder, delete the `goals = false` lines there, raise `[budget] goals`, and `./scripts/dev-wiki.sh check --wiki <copy>`. |

## Documentation

- `docs/wiki/` — the wiki, written to the `writing-wiki-pages` skill and checked by `wiki check`: `wiki.toml` (sections, budgets, the recorded release), `pages/` (one file per page; a file in a folder named after a page nests under it), `UPDATED.toml` (page dates, written by `build`), `worker.js` (the Basic-auth Cloudflare Worker for `wrangler.jsonc`), `site/` (generated, ignored).
- `docs/CODEMAP.md` — this file. Builder inventory and traps; nothing a person needs.
- `AGENTS.md` — the rules, including the worked code galleries. Not documentation; law. `CLAUDE.md` is a byte-identical copy, enforced by `AgentInstructionsTest`.
- `wrangler.jsonc` — the documentation site's Worker; `name` is the one knob a fork sets.
- `.template/` _(hidden)_ — template maintenance only, deletable by a fork: `CHANGELOG.md`, `CHANGELOG-LEGACY.md`, `README.md`, `ADOPT.md`, `migrations/`.
