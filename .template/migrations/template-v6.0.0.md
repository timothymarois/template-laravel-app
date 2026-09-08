# Migrating a fork to template v6.0.0

v6.0.0 **removes built-in multi-tenancy**, **completes the authentication surface**, **authorizes the
admin area**, **ships an API-key system**, and fixes confirmed defects in the **health checks**, the
**UI kit** and the **document head**. Major: it removes a shipped capability, renames a public
enum method, and closes a privilege-escalation hole that changes who can reach `/admin`.

> ⚠️ **Two changes will break a fork that does nothing.** `/admin` now requires `role = admin` — an
> operator whose row says `user` loses access until promoted. And `UserRole::canManageAllTenants()` is
> gone. Read Parts C and D before deploying.

> **Skip note.** If you are on **v5.0.0–v5.3.0** and heading here, do **not** work through the tenancy
> setup in those migrations — v6.0.0 deletes everything they install. Apply their non-tenancy parts and
> come straight here. A fork that wants tenancy follows `docs/guides/adding-tenancy.md` afterwards.

## Prerequisites

- `jq -r .version template-manifest.json` → `5.7.0`. On `5.6.0` or below, apply the intervening
  migrations first (honouring the skip note above).
- Baseline `pnpm check` is green.
- A clone of this template at v6.0.0 to copy from. Referred to below as `<t>`.
- **Know whether your fork enabled tenancy**: `grep TENANCY_ENABLED .env`. If it is `true`, read Part B
  before anything else.

---

## Part A — Remove built-in tenancy (forks that never enabled it)

This is most forks. If `TENANCY_ENABLED=false` you have been carrying dormant code; deleting it changes
no behaviour.

**Order matters.** `HandleInertiaRequests` calls the package's global `tenant()` helper on every web
request. Remove the dependency first and every request fatals with `Call to undefined function
tenant()`. Do step 1 before step 4.

### 1. Un-wire the middleware first

```diff
 public function share(Request $request): array
 {
     return array_merge(parent::share($request), [
         'user' => $request->user(),
-        ...$this->tenancyShared(),
     ]);
 }
-
-protected function tenancyShared(): array
-{
-    $tenant = tenant();
-    ...
-}
```

### 2. Un-wire the rest

- `bootstrap/providers.php` — drop the `TenancyServiceProvider` import and array entry.
- `app/Providers/AppServiceProvider.php` — drop the two `App\Tenancy\*` imports and the whole
  `register()` body (the `ExistingDataMigrator` binding); leave `register(): void {}`.
- `app/Models/User.php` — drop `use App\Models\Concerns\CentralConnection;`, remove `CentralConnection,`
  from the `use` list, delete the `tenants()` relation, then delete the now-unused `BelongsToMany`
  import (Pint and Larastan both flag it).
- `routes/web.php` — drop the `InviteController` import and the `if (config('tenancy.enabled'))` block.
- `config/database.php` — delete the `pgsql_central` and `mysql_central` connections.
- `phpunit.xml` — delete the `Central` and `Tenant` `<testsuite>` blocks and the forced
  `TENANCY_ENABLED` env.
- `.env.example` and `.env` — delete the `TENANCY_*` and `DB_CENTRAL_*` block from both. `.env` is not
  tracked, so nothing does this for you.
- `.gitignore` — drop `/database/tenant*`.
- `package.json` — delete `check:tenancy` and `check:all`, then fix every citation:
  `grep -rn "check:tenancy\|check:all" --exclude-dir=node_modules --exclude-dir=vendor .`

### 3. Delete the files

```bash
git rm -r app/Tenancy app/Services/Tenancy app/Http/Controllers/Tenancy \
  app/Http/Middleware/Tenancy app/Console/Commands/Tenancy app/Notifications/Tenancy \
  database/migrations/central database/migrations/tenant \
  tests/Central tests/Tenant tests/Feature/Tenancy tests/Unit/Tenancy
git rm app/Providers/TenancyServiceProvider.php app/Models/Tenant.php app/Models/TenantInvite.php \
  app/Models/Domain.php app/Models/Concerns/CentralConnection.php app/Enums/TenantRole.php \
  app/helpers.php routes/tenant.php config/tenancy.php phpunit.tenancy.xml \
  .github/workflows/tenancy-enabled.yml tests/CentralBaseTestCase.php tests/TenantBaseTestCase.php \
  tests/Unit/Enums/TenantRoleTest.php tests/Unit/Models/TenantInviteTest.php \
  tests/Unit/Notifications/TenantInvitationNotificationTest.php \
  docs/guides/tenancy-using.md docs/guides/tenancy-migrating.md
```

**If your fork added its own helpers to `app/helpers.php`**, keep the file with only those and skip the
`composer.json` edit in step 4. Otherwise the file goes, and so must its autoload entry.

### 4. Drop the dependency

```bash
composer remove stancl/tenancy
```

`stancl/jobpipeline`, `stancl/virtualcolumn` and `facade/ignition-contracts` have no other requirer and
leave with it. Then, in `composer.json`:

```diff
     "autoload": {
         "psr-4": { ... }
-        },
-        "files": [
-            "app/helpers.php"
-        ]
+        }
     },
```

Leaving `autoload.files` pointing at a deleted file fatals `composer dump-autoload`.

### 5. Clear the stale caches

```bash
rm -f bootstrap/cache/packages.php bootstrap/cache/services.php bootstrap/cache/config.php
php artisan package:discover
```

Skip this and Larastan (and a fresh deploy) will fail with `Class "…ServiceProvider" not found` from a
cached manifest that still lists a removed package.

---

## Part B — Forks that enabled tenancy

**Do not run Part A.** The template no longer maintains multi-tenancy. Choose one:

1. **Stay on v5.7.0.** Supported, and the right answer if tenancy is core to your product and you are
   not ready to own it.
2. **Adopt the code.** It is yours now: keep every file Part A deletes, keep `stancl/tenancy` in
   `composer.json`, keep `check:tenancy` and the CI job, and stop expecting template upgrades to touch
   any of it. `docs/guides/adding-tenancy.md` in v6.0.0 describes the shape it should have — use it to
   check your wiring, not to rebuild it.

Then apply Parts C–H, which are independent of tenancy.

---

## Part C — The `UserRole` rename (every fork)

`canManageAllTenants()` is now `canManageAllUsers()`. The old name described a concept that no longer
exists.

```bash
grep -rn "canManageAllTenants" --exclude-dir=vendor --exclude-dir=node_modules --exclude-dir=.git .
```

Rename every hit, including tests. There is no deprecation shim: a silent fallback on an authorization
predicate is worse than a fatal.

---

## Part D — Authorize the admin area (every fork) ⚠️

**This is the change most likely to lock you out.** Before v6.0.0 the `admin` route group carried
`auth:sanctum` and nothing else — no policy, no role check — so **any authenticated user could list,
create, edit and delete every user**. All three known forks had patched this independently.

### 1. Check who will lose access, first

```bash
php artisan tinker --execute="echo App\Models\User::where('role','admin')->count().' admins / '.App\Models\User::count().' users'.PHP_EOL;"
```

If that count is `0`, promote somebody **before** deploying:

```php
App\Models\User::where('email', 'you@example.com')->update(['role' => 'admin']);
```

### 2. Copy the pieces

```bash
mkdir -p app/Policies
cp <t>/app/Policies/UserPolicy.php app/Policies/
cp <t>/app/Http/Middleware/EnsureUserIsAdmin.php app/Http/Middleware/
```

### 3. Wire them

- `app/Http/Controllers/Controller.php` — add `use Illuminate\Foundation\Auth\Access\AuthorizesRequests;`
  and `use AuthorizesRequests;` in the class body. Laravel no longer applies it by default, so
  `$this->authorize()` has nothing to call without it.
- `bootstrap/app.php` — register the alias:
  ```php
  $middleware->alias(['admin' => EnsureUserIsAdmin::class]);
  ```
- `routes/web.php` — `Route::middleware('admin')->prefix('admin')->name('admin.')->group(...)`.
- `Admin/UserController` — `$this->authorize(...)` on `index`, `prepareIndexFilters`, `simpleTable`,
  `show`, `destroy`.
- `StoreUserRequest` / `UpdateUserRequest` — add `authorize()` returning the policy check.

**If your fork already wrote its own admin middleware**, keep yours and skip the middleware half — but
still take `UserPolicy` and the Form Request `authorize()` methods. A route-group guard does not
authorize a specific resource.

### 4. Expect your existing tests to fail

Any test that acts as a plain `User::factory()->create()` against an admin route now gets 403. That
failure **is** the vulnerability. Change those to `User::factory()->admin()->create()` and add refusal
cases for a non-admin and a guest.

---

## Part E — Password reset and the flash share (every fork)

The template had no password reset at all. The table and broker config already existed, so nothing is
migrated — this is purely additive.

```bash
cp <t>/app/Http/Controllers/Auth/PasswordResetController.php app/Http/Controllers/Auth/
cp <t>/app/Http/Requests/Auth/ForgotPasswordRequest.php app/Http/Requests/Auth/
cp <t>/app/Http/Requests/Auth/ResetPasswordRequest.php app/Http/Requests/Auth/
cp <t>/resources/js/pages/ForgotPassword.vue <t>/resources/js/pages/ResetPassword.vue resources/js/pages/
```

Take the guest-group route block from `<t>/routes/web.php` verbatim. **Keep the route names
`password.request`, `password.email`, `password.reset`, `password.store`** — the framework's
`ResetPassword` notification builds its link from `route('password.reset', ...)`, so renaming them means
registering a `ResetPassword::createUrlUsing()` callback to undo it, and drops the routes out of
`GenerateSitemap`'s `password.*` exclusion.

Add the `flash` share to `HandleInertiaRequests::share()`:

```php
'flash' => [
    'status' => $request->session()->get('status'),
    'error' => $request->session()->get('error'),
],
```

Nothing shared it before, so every `->with('status')` / `->with('error')` in your fork has been written
to the session and silently dropped — including `EnsureUserIsActive`'s deactivation message. Adding this
may make messages appear that you had assumed were already showing. Render them as `Login.vue` does.

Then run `php artisan ziggy:generate` (or restart `pnpm dev`), or `route('password.email')` throws in
the browser — `resources/js/ziggy.js` is generated and git-ignored.

**Mail actually has to work.** `.env.example` ships `MAIL_MAILER=log`. A fork that deploys with `log`
has a password-reset flow that silently does nothing. Set a real provider in production.

---

## Part F — Auth defect fixes (every fork)

Three fixes, each small, each closing something real:

1. `RegisterController::store()` — add `$request->session()->regenerate();` after `Auth::login($user)`.
   Login already did this; registration did not, leaving session fixation on the one route that hands a
   new user a session.
2. `LoginRequest::authenticate()` — after a successful `Auth::attempt`, refuse an inactive user
   explicitly (see `<t>`). `EnsureUserIsActive` only ejects on the *next* request, leaving one fully
   authenticated request in between.
3. `EnsureUserIsActive::handle()` — change `Auth::logout()` to `Auth::guard('web')->logout()`. ⚠️ On any
   route behind `auth:sanctum` — which is every `/admin/*` route — `Authenticate` has already called
   `shouldUse('sanctum')`, so the default guard is Sanctum's `RequestGuard`, which has no `logout()`.
   A deactivated user hitting the admin area got a **500**, not a redirect. Every case in the template's
   own `EnsureUserIsActiveTest` used `/`, a session-only route, so the broken path was never exercised.
   Copy the two new cases from `<t>` with it: one on `admin.users.index`, one asserting the JSON/XHR
   branch answers 403 (that branch had no coverage at all).
4. `LoginRequest::rules()` — change `'password' => 'required|string|min:8'` to `'required|string'`.
   Length policy belongs on the routes that set a password; on login it locks out pre-policy passwords
   and advertises the minimum to a prober.

Apply `throttle:auth` to `POST auth/register` and both password POSTs. The limiter was defined in
`AppServiceProvider` and applied to nothing. **Do not put it on login** — named limiters key on the
limiter name + IP, not the route, so all `throttle:auth` routes share one bucket; adding login lets
failed logins lock a user out of password reset.

---

## Part G — API keys (every fork), and converting a home-grown one

New capability. Copy:

```bash
mkdir -p app/Http/Requests/ApiKey app/Services/DataTransferObjects resources/js/pages/admin/api-keys
cp <t>/app/Enums/ApiAbility.php app/Enums/
cp <t>/app/Services/ApiKeyService.php app/Services/
cp <t>/app/Services/DataTransferObjects/IssuedApiKey.php app/Services/DataTransferObjects/
cp <t>/app/Http/Middleware/EnsureApiKey.php app/Http/Middleware/
cp <t>/app/Http/Controllers/Admin/ApiKeyController.php app/Http/Controllers/Admin/
cp <t>/app/Http/Requests/ApiKey/StoreApiKeyRequest.php app/Http/Requests/ApiKey/
cp <t>/app/Policies/ApiKeyPolicy.php app/Policies/
cp <t>/resources/js/pages/admin/api-keys/Index.vue resources/js/pages/admin/api-keys/
cp <t>/docs/concepts/api-keys.md docs/concepts/
```

Wire in `bootstrap/app.php` — **all four aliases**:

```php
$middleware->alias([
    'admin' => EnsureUserIsAdmin::class,
    'api.key' => EnsureApiKey::class,
    'abilities' => \Laravel\Sanctum\Http\Middleware\CheckAbilities::class,
    'ability' => \Laravel\Sanctum\Http\Middleware\CheckForAnyAbility::class,
]);
```

Sanctum ships the last two but does not register them in Laravel 11+; without the alias an `abilities:`
route does not resolve. Bind the policy in `AppServiceProvider::boot()` —
`Gate::policy(PersonalAccessToken::class, ApiKeyPolicy::class)` — because the model is not in
`App\Models` and convention discovery will not find it. Add the admin routes and set
`SANCTUM_TOKEN_PREFIX` (it shipped empty, which disables secret scanning).

**`api.key` is not optional.** `config('sanctum.guard')` includes `web`, so `auth:sanctum` also accepts
a logged-in browser session, which Sanctum models as a `TransientToken` whose ability check returns true
for *everything*. Without `api.key`, an `abilities:` gate passes for any signed-in user holding no key.

### Converting a fork's own API-key system

If your fork built its own (a bespoke `api_keys` / `source_credentials` table, or a static shared-secret
header), migrate onto this one. **This is a persisted-state change — get sign-off before running it.**

| Your column | Sanctum equivalent |
|---|---|
| owner id / source id | `tokenable_type` + `tokenable_id` (polymorphic — the owner need not be a `User`) |
| `name` | `name` |
| your hash of the whole plaintext | `token` — SHA-256 of the part **after** the `id\|` prefix |
| `token_prefix` for display | no equivalent; derive at issue time or add a nullable column |
| `last_used_at` | `last_used_at`, maintained by Sanctum |
| — | `abilities` — map your scopes onto `ApiAbility` cases |
| — | `expires_at` |
| soft deletes | drop them; revocation is a hard delete |

Steps:

1. `use HasApiTokens` on whatever model owns a key.
2. Add the `ApiAbility` cases your scopes map to, and put `abilities:` on the routes that enforce them.
3. Issue replacements with `ApiKeyService::issue()` and distribute them.
4. Cut over, then drop the old table.

**What cannot be migrated:** existing keys. You stored a hash (or a plaintext env secret), and Sanctum
verifies a different hash of a different string — old keys cannot be rehashed into the new scheme.
**Every caller must be reissued.** There is no dual-read window unless you keep both middlewares live
during cutover, which is the only way to avoid downtime for third-party integrations.

If your old scheme was a static shared secret in `config`/`.env`, **rotate it as part of this work** —
it has been in your git history for as long as it has existed.

---

## Part H — Docs and gate (every fork)

- Delete the two tenancy guides and their `docs/guides/README.md` rows; add the `adding-tenancy.md` row
  if you took the guide.
- `docs/CODEMAP.md` — re-count every heading. Counts are the contract; a stale one means the section is.
- `docs/BRIEF.md`, `docs/concepts/platform.md`, `docs/guides/writing-tests.md`, `README.md` — drop the
  tenancy claims.
- `AGENTS.md` **and** `CLAUDE.md` — byte-identical, enforced by a test. Edit both in the same commit or
  the suite fails. v6.0.0 also restructures "Before you work" to five points, hardens the skill-loading
  rule, and adds a "Delegation and review" section; take those if your fork tracks the template's rules.
- **Copy `docs/guides/troubleshooting.md` from `<t>` and add its `docs/guides/README.md` row.** ⚠️ Take it
  even if you skip every other doc in this part. It carries the traps this stack has already sprung —
  several of which each fork rediscovered independently — and it opens by telling whoever reads it that a
  defect in template-managed code (`ui/`, `composables/`, `utils/`, the gates, `scripts/`, `docker/config/`,
  the docs) gets a PR against the template, not just a local patch. That instruction is the point: three
  forks paying separately for the same bug is what this release is correcting. Append your own fork's traps
  under it; keep product-specific ones out of any PR you send upstream.
- Copy `docs/concepts/ui-kit.md` and its `docs/concepts/README.md` row if your fork carries the shared kit —
  it is the index that stops a fourth near-duplicate component being written.


---

## Part I — Health-check defects (every fork) ⚠️

Two of these are live problems in every fork, not cleanups.

1. **`/health` never enforced `HEALTH_SECRET_TOKEN`.** The route carried no middleware, and the package
   only auto-wires its own Oh Dear route (disabled here) — so setting the env var did nothing at all
   while the docs said it protected the endpoint. Attach it:

   ```diff
   +use Spatie\Health\Http\Middleware\RequiresSecretToken;
   -Route::get('health', HealthCheckJsonResultsController::class)->name('health');
   +Route::get('health', HealthCheckJsonResultsController::class)
   +    ->middleware(RequiresSecretToken::class)
   +    ->name('health');
   ```

   It is a no-op until the env var is set, so this is safe to apply immediately.

2. **`always_send_fresh_results` was left at the package default `true`.** The `/health` controller reads
   it whether or not the Oh Dear endpoint is enabled, so **every request ran every check inline** — a DB
   round-trip, a Redis lookup, a `df` subprocess and a TCP probe — fired the `CheckEnded` events that
   drive the recovery debounce, and rewrote the shared result cache. On an unauthenticated, unthrottled
   URL that is a denial-of-service lever. Set it to `false` in `config/health.php`. `?fresh` still forces
   a live run.

3. **`HorizonCheck` gated on `class_exists()`**, which is always true because `laravel/horizon` is a hard
   composer requirement. A fork that removed the horizon process from `supervisord.conf` — the documented
   way to drop it — had the check fail forever. Gate on the queue driver instead:
   `->if(fn () => config('queue.default') === 'redis')`.

Copy `tests/Feature/HealthEndpointTest.php` from `<t>`; nothing covered this endpoint before, which is
how all three survived.

---

## Part J — UI kit fixes (every fork with the shared kit)

These were all found by diffing this template against forks that had patched them locally. Take them
from `<t>` file by file; each is small.

| File | What was wrong |
|---|---|
| `resources/js/utils/vue/inertia/isPageActive.ts` | Returned `false` under SSR and depended on `document.baseURI`, so every nav item rendered inactive server-side then flipped on hydration. **Behaviour note:** a caller passing an absolute URL previously matched on its pathname and now will not. |
| `resources/js/composables/inertia/useDataTableOptions.js` | PHP serializes an empty `filters` as `[]`; letting that land on `form.filters` means later mutations become non-numeric array props, which `JSON.stringify` drops — the request silently loses every filter. The coercion must sit **after** the `...options` spread. Its `sortField` default also changes `'name'` → `'id'`; a caller passing `sortField` explicitly is unaffected. |
| `resources/js/components/ui/sheet/SheetForm.vue` | Laid out wider than the viewport on mobile with Save/Cancel off-screen (`!max-w-none` is `!important` and beat the inline `maxWidth`); rendered no `SheetDescription`, violating reka-ui's dialog contract on every sheet; hardcoded "Save"/"Cancel". New props: `description`, `submitLabel`, `cancelLabel`, `submitDisabled`, and a `footer-actions` slot — all defaulted so rendering is unchanged. |
| `resources/js/components/ui/accordion/Accordion.vue` | Ignored a passed `class`. **Behaviour note:** `class` becomes a declared prop, so it now merges via `cn()` instead of appending as a fallthrough attr — which is what every other component in the kit does. |
| `resources/js/components/app/page/SideNav.vue` | Destructured props, so a computed `items` never re-rendered. |
| `resources/js/components/app/layout/AppLayout.vue` | Read `page.props.user` by value, so the avatar did not update on a partial reload. |
| `resources/js/components/ui/index.ts` | Omitted 13 shipped components with nothing saying why. Nine are now exported; the four that pull optional deps or clash by name are excluded **with a comment naming the reason**. |
| `resources/js/components/app/layout/AppShell.vue` | `defineAsyncComponent()` on the Toaster could not split, because the barrel exports it statically — Vite warned on every build. |

---

## Part J2 — Gates that were passing without checking (every fork) ⚠️

Two gates in every fork name work they never did. Both fixes are one line; the second surfaces real
errors that then have to be fixed, so budget for it.

1. **`typecheck` never read a single component.** `tsc --noEmit` cannot parse `.vue` at all, and it only
   exited 0 because `resources/js/env.d.ts` declared every SFC as `Record<string, never>` — a shim that
   also suppressed every real error inside a component. Copy `<t>/resources/js/env.d.ts` (no `*.vue`
   shim; it declares Ziggy's `$route` on `ComponentCustomProperties` instead), set
   `"typecheck": "vue-tsc --noEmit"`, and add `"noImplicitAny": false` to `tsconfig.json` — `ui/` is
   `lang="ts"` while `app/` and `site/` are plain JS, so a typed component importing an untyped SFC is
   the architecture, not a defect. Expect errors: this template had **103**. Prove the gate afterwards by
   putting `const x: number = 'a'` in a `.vue` and watching it fail.
2. **`lint` skipped every `.ts` file.** The glob was `resources/js/**/*.{js,vue}` while the ESLint config
   already had a `**/*.{ts,tsx}` block — 156 files went unchecked with ESLint exiting 0. Add `ts` to the
   glob in both `lint` and `lint:fix`, run `pnpm lint:fix`, then copy the `vue/one-component-per-file`
   override for `resources/js/tests/**` from `<t>/eslint.config.js` (inline stubs in specs are not SFC
   authoring).

**Fixes the type gate then surfaces**, all in `<t>` — take them if you have the shared kit:

| File | What was wrong |
|---|---|
| `components/ui/calendar/Calendar.vue`, `range-calendar/RangeCalendar.vue` | `:placeholder` was bound **before** `v-bind="forwarded"`, which still carried `placeholder` — so the external prop overwrote the internal one and quick navigation could not move the grid. Add `"placeholder"` to `reactiveOmit`. |
| `components/ui/editor/Editor.vue` | Called tiptap v2's `setContent(content, false, parseOptions)`. v3 takes `(content, options)`, so `emitUpdate: false` **and** `preserveWhitespace` were both dropped and every programmatic set re-emitted an update. |
| `components/ui/tags-input/TagsInputItem.vue` | Imported `TagsInputVariant` from `TagsInput.vue`, which does not export it. It lives in `./types`. |
| `components/ui/button/Button.vue` | `class?: string` rejected the object and array forms callers pass. Widen to `HTMLAttributes['class']` — this alone cleared 17 errors. |
| `components/ui/data-table/TableActions.vue` | Read `.tooltip` off an untyped `Array` prop, and `menuItem` was optional but dereferenced. |
| Date components | `DateValue`/`DateRange` are unions of **classes**; `ref()` applies `UnwrapRef`, which distributes over the union and strips class identity. Use `shallowRef`. |
| 5 files using `text-md` | Not a Tailwind utility — it emitted no CSS rule at all. Replace with `text-base` (identical rendering; the elements were inheriting 1rem anyway). |

**Then make the traps unrepeatable** — three preventions, all in `<t>`:

- `resources/js/tests/setup.js` + `setupFiles` in `vite.config.js`: `enableAutoUnmount(afterEach)`. Without
  it a component bound to `window`/`document` keeps answering the **next** case's events, so an assertion
  reports the leftover component's behaviour.
- `resources/js/tests/conventions/tailwindClasses.test.js`: fails the build on names that read as real
  Tailwind and emit nothing.
- `scripts/preflight-php` on `check:php` and `build`: turns `env: php: No such file or directory` into the
  `export PATH=...` line you actually need. It probes Herd, Lerd, Homebrew and the system paths and only
  names one where a PHP actually exists on that machine — it never edits PATH and hardcodes no location.

---

## Part K — The document head (every fork) ⚠️

Verified by running the built SSR bundle and reading the head it returns.

1. **`canonical`, `og:url` and `og:image` were built from `window.location.origin`**, which is undefined
   under SSR — the exact render a crawler or social scraper reads. `og:url` shipped **empty**, a relative
   `og:image` shipped unresolvable, and there was **no `<link rel="canonical">` anywhere**. Share the base
   URL from the server and build from it:

   ```php
   'appUrl' => rtrim((string) config('app.url'), '/'),
   ```

   **`APP_URL` must now be correct in production** — every absolute URL, and `sitemap:generate`'s
   `<loc>`, is built from it.

2. **Two `<title>` tags.** The Blade root emitted one and `@inertiaHead` emitted another; Inertia does not
   de-duplicate and parsers take the first, so **every page was titled `APP_NAME`**. Delete the Blade
   `<title>`. If your fork does **not** run SSR, note that the server HTML then carries no title at all
   (Inertia sets it on the client) — that is the trade the template makes because SSR is on by default.

3. **The head block was copy-pasted into both layouts** and had drifted. It is now one
   `components/app/SeoHead.vue`, with defaults from a new `config/seo.php`.

4. **`SEO_INDEXABLE`** gates an `X-Robots-Tag: noindex, nofollow` header from `SecurityHeaders`. Set it
   `false` on staging. A `robots.txt` `Disallow` is **not** a substitute — it stops crawling, not
   indexing, so a linked page still appears.

5. **The sitemap listed `/health`, `/release` and `/_inertia/devtools/entries`.** `/health` answers 503
   whenever a check fails, so that was a 5xx URL in the sitemap. Add those prefixes to
   `GenerateSitemap::$excludePrefixes`. Remember `public/sitemap.xml` is git-ignored and the command is
   not scheduled — a fresh deploy serves 404 until you schedule it or add it to post-deploy.

6. `resources/views/app.blade.php` referenced `/favicon.svg`, which does not exist — a 404 on every page
   load.
7. **Three more files change with this part**, easy to miss: `resources/js/app.js` and
   `resources/js/ssr.js` both change the Inertia `title` callback from a bare passthrough to
   `` `${title} — ${appName}` `` (with `appName` read from `import.meta.env.VITE_APP_NAME`), and
   `public/robots.txt` drops a stale `Disallow: /log-viewer` and rewrites the sitemap comment.

Copy `tests/Feature/SeoTest.php` from `<t>`.

---

## Part L — Standardized roles (every fork)

The role model is unchanged in behaviour but now has one obvious API, because every
fork had invented its own (`$user->is_admin`, `$user->isAdmin()`, an inline string
compare).

- `UserRole::default()` — the role a new user gets. The users migration and the
  factory reference it instead of hardcoding a case.
- `UserRole::label()` — display name, for a UI that lets an operator pick a role.
- `UserRole::canAccessAdmin()` — "may reach the admin surface", distinct from
  `canManageAllUsers()`. `EnsureUserIsAdmin` asks this one.
- `User::isAdmin()` — delegates to the enum. This is the accessor your fork
  probably already has; point yours at the enum and delete the string compare.

**The rule the template now states explicitly: ask a capability, not a role.**
`$user->role === UserRole::SuperAdmin` scattered across call sites means the day
you add a third role you have to find every one of them. Nothing in the template
compares cases outside the enum.

If your fork added roles, add a `label()` arm and a decision for each capability —
the new tests iterate `UserRole::cases()`, so a missing arm fails rather than
surfacing at runtime.

---

## Part M — Route and CI corrections (every fork)

- **`Route::resource('users', ...)` registered `create` and `edit`**, which
  `Admin/UserController` does not implement — those URLs returned a 500, not a 404.
  Add `->except(['create', 'edit'])`. (This app uses modal forms; a fork that built
  real create/edit screens keeps them and skips this.)
- **CI ran Node 20** while `package.json` engines requires `>=22.12` and `.nvmrc`
  says 22, and **never ran `tsc`** — so a type error could reach main behind a green
  badge. The JS workflow is now three parallel jobs (quality / build / release
  scripts); only the build half needs PHP, because lint, types and Vitest do not
  read the generated `ziggy.js`. Every workflow gained a `concurrency` group so a
  superseded push stops burning a runner.
- **The Docker workflow no longer triggers on markdown** under `docker/`, and no longer builds the
  application at all. ⚠️ **`Dockerfile` is managed core and changes here:** the `runtime` stage is split
  into `runtime-config` (base + Node + every configuration COPY) and `runtime` (`runtime-config` + the
  app copy). Same directives, reordered — configuration now sits *before* the app copy, so a source
  change stops invalidating it, and the config checks build `--target runtime-config`, skipping
  composer, pnpm and both Vite builds. The job ran 400-635s on every run, including on `main`; it also
  now shares one `type=gha` cache scope instead of a per-branch one. If your fork edited the
  `Dockerfile`, re-apply your change against the new stage boundaries.

---

---

## Part N — Dependencies (every fork)

PHP is fully current on this template, majors included: **Laravel 13.31**, plus **Pest 5** and
**PHPUnit 13** (upgrade together — Pest 5 requires PHPUnit 13) and **spatie/laravel-sitemap 8**
(check `GenerateSitemap` still compiles; the route-exclusion API is unchanged).

`stylelint`'s newer `at-rule-prelude-no-invalid` flags every Tailwind `@apply`, since it validates the
prelude as CSS values. Add the ignore in `stylelint.config.js`:

```js
'at-rule-prelude-no-invalid': [true, {
    ignoreAtRules: ['apply', 'variant', 'custom-variant', 'theme', 'source']
}],
```

**JavaScript majors are deliberately NOT in this release** — TypeScript 7, Vite 8, Vitest 5, ESLint 10,
Stylelint 17, `@tanstack/vue-table` 9, `lucide-vue-next` 1.0, `unplugin-auto-import` 21 and
`laravel-vite-plugin` 3 all remain outstanding. `@tanstack/vue-table` v9 is a rewrite and backs the
data-table kit; TypeScript 7 is the native port. Upgrade them one at a time, proving `pnpm check` green
after each, rather than in a single `pnpm up --latest`.


---

## Part O — Admin UI, roles on the user form, and keyboard behaviour (every fork) ⚠️

### 1. `role` is now required when creating or updating a user ⚠️

`StoreUserRequest` and `UpdateUserRequest` both gained
`'role' => ['required', Rule::enum(UserRole::class)]`. **Any fork code that posts to
`admin.users.store` or `admin.users.update` without a `role` now fails validation with a
422.** Grep for callers — your own tests are the usual casualty:

```bash
grep -rn "admin.users.store\|admin.users.update\|/admin/users" --include=*.php --include=*.vue \
  --exclude-dir=vendor --exclude-dir=node_modules .
```

If your fork should not let an operator set a role from that form, drop the rule and the field
rather than sending a default — a silently-defaulted role on an authorization-bearing column is
worse than a validation error.

`Admin/UserController@index` now also passes a `roles` prop, built from `UserRole::cases()`, which
is what the modal's picker reads.

### 2. Console command and seeder

- **`php artisan api-key:create`** (`app/Console/Commands/CreateApiKey.php`) — issues a key where
  there is no browser: seeding, CI, a deploy script. Prints the plaintext once, and refuses an
  unknown or deactivated owner and an ability outside the enum.
- **`php artisan user:create --admin`** (`app/Console/Commands/CreateUser.php`) — the supported way
  to make an operator. Prompts for anything not passed; generates and prints the password once when
  `--password` is omitted, so a real one never lands in shell history.
- **`DatabaseSeeder` now seeds an operator** alongside the plain user. Before this, a fresh
  `migrate --seed` on v6.0.0 produced an install whose admin surface nobody could reach, because
  Part D gates it on the role. Take this even if you keep your own seeder.

### 3. Admin forms are centered modals

`EditUserModal` moves from `SheetForm` to `Dialog`, and the new API-key form uses `Dialog` too.
Actions read `Save changes` / `Create user` rather than a generic "Save". `SheetForm` stays in the
kit, improved, for wider or tabbed forms.

**Put `FormErrors` inside the form, after the fields — never in the `#footer` slot.** `DialogFooter`
is a horizontal flex row, so an error block placed there is squeezed beside the action instead of
reading full width.

### 4. Keyboard behaviour

- **Enter submits, Escape cancels**, in every form and dialog.
- The four auth pages keep their submit control in the Card footer, **outside** the `<form>`, so each
  needs `@submit.prevent` **and** a hidden `<button type="submit">` inside the form. Browsers only
  perform implicit submission when the form contains a submit control; without one, Enter did
  nothing on a form with more than one field.
- `DialogConfirmation` confirms on Enter and closes on Escape, and **confirming now closes the
  dialog** — clicking the action already closed it while the keyboard path did not, leaving one
  gesture with two outcomes. Its Enter listener binds to `document` in the capture phase while open:
  reka renders through a portal whose wrapper root emits no DOM node, so a listener in the template
  never received the event and Enter activated the focused Cancel button instead.
- The guard lives in `resources/js/components/ui/dialog/dialogUtils.ts` (`shouldConfirmOnEnter`) —
  it refuses while the dialog is busy and ignores Enter raised from an input, textarea or select.

Take `resources/js/tests/components/ui/dialog/dialogUtils.test.ts` and
`resources/js/tests/utils/isPageActive.test.ts` with these.

### 5. Smaller items in this release

| File | Change |
|---|---|
| `config/sanctum.php` | `token_prefix` defaults to `apik_` (was empty, which disables secret scanning) |
| `config/ziggy.php` | drops the `_debugbar.*` exclusion; the package is not installed |
| `app/Console/Commands/GenerateSitemap.php` | excludes `health`, `release`, `_inertia`, `_debugbar`, `storage` |
| `app/Http/Middleware/SecurityHeaders.php` | sends `X-Robots-Tag: noindex` unless `config('seo.indexable')` |
| `resources/js/pages/errors/*.vue`, `Register.vue` | `robots="noindex"`, and Enter-to-submit on the auth pages |
| `routes/api.php` | the API-key chain (Part G) |
| `docs/concepts/{seo,health-checks,api-keys}.md` | new/updated pages — add the index rows |
| `.claude/skills/designing-ui-ux/SKILL.md` | gains the notification and keyboard rules; take it if your fork vendors the skills |
| `storage/debugbar/`, `.env.example` | orphaned directory removed; `DB_DATABASE` no longer defaults to the colliding name `template` |

---

## Verify

```sh
# 1. No tenancy reference survives. Every remaining hit should be "maintenance".
# Every surviving hit should be the substring "main-tenan-ce", or a deliberate
# pointer to adding-tenancy.md — in AGENTS.md, CLAUDE.md, docs/BRIEF.md and
# docs/guides/README.md (its index row).
grep -rniE "tenan|stancl|TENANCY_ENABLED|DB_CENTRAL" --exclude-dir=vendor \
  --exclude-dir=node_modules --exclude-dir=.git --exclude-dir=.template \
  --exclude-dir=.claude --exclude=adding-tenancy.md . | grep -vi maintenance

# 2. The autoloader and package manifest agree with the tree.
composer dump-autoload            # must not fatal
php artisan package:discover

# 3. The runtime has no tenancy surface.
php artisan route:list | grep -ci "tenant\|invite"     # expect 0
php artisan tinker --execute="var_dump(config('tenancy'));"   # expect NULL

# 4. The retired gate is gone from scripts and docs. adding-tenancy.md is excluded
#    on purpose: it tells a fork how to add those scripts back if it adopts tenancy.
grep -rn "check:tenancy\|check:all" --exclude-dir=vendor --exclude-dir=node_modules \
  --exclude-dir=.git --exclude=adding-tenancy.md .

# 5. Password reset resolves, and the notification link will build.
php artisan route:list --name=password                 # expect 4 routes
php artisan ziggy:generate

# 6. At least one operator exists, or Part D locks everyone out.
php artisan tinker --execute="echo App\Models\User::where('role','admin')->count().PHP_EOL;"  # expect >= 1
#    Then prove the gate itself — this is the check that matters, and it is an
#    HTTP one. Log in as a NON-admin and request /admin: a 403 is the fix working,
#    a 200 means Part D is not wired and the hole is still open.

# 7. API keys resolve end to end.
php artisan route:list --name=api-keys                 # expect 3 routes

# 7b. The user form's new required field, and the operator path.
php artisan user:create --admin --name=Probe --email=probe@example.test
grep -rn "admin.users.store\|admin.users.update" --include=*.php --exclude-dir=vendor tests/
#     every caller must now send a role, or it 422s

# 8. Health, head and sitemap.
php artisan tinker --execute="var_dump(config('health.oh_dear_endpoint.always_send_fresh_results'));"  # false
grep -c '<title' resources/views/app.blade.php                          # expect 0
php artisan sitemap:generate && grep -cE '/health|/release|_inertia' public/sitemap.xml   # expect 0

# 9. The whole gate.
pnpm check
```

Then run the suites that prove the security properties, not just that things boot:

```sh
php -d memory_limit=512M ./vendor/bin/pest tests/Feature/ApiKeyTest.php
php -d memory_limit=512M ./vendor/bin/pest tests/Feature/PasswordResetTest.php
php -d memory_limit=512M ./vendor/bin/pest tests/Feature/UserControllerTest.php
```

Finally, log in as a **non-admin** and request `/admin`. A 403 is the fix working. A 200 means Part D is
not wired, and the hole is still open.

## Finally

Bump the manifest:

```diff
-    "version": "5.7.0",
+    "version": "6.0.0",
```

Then update the workspace tracker row for this fork. If the fork kept tenancy (Part B), record that it
now owns that code — the template will not upgrade it again.
