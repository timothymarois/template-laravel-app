# Migrating a fork to template v6.0.0

v6.0.0 **removes built-in multi-tenancy**, **completes the authentication surface**, **authorizes the
admin area**, and **ships an API-key system**. Major: it removes a shipped capability, renames a public
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
3. `LoginRequest::rules()` — change `'password' => 'required|string|min:8'` to `'required|string'`.
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

---

## Verify

```sh
# 1. No tenancy reference survives. Every remaining hit should be "maintenance".
# Every surviving hit should be the substring "main-tenan-ce", or a deliberate
# pointer to adding-tenancy.md in AGENTS.md / CLAUDE.md / BRIEF.md.
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

# 6. Admin is actually gated. This MUST be 403, not 200.
php artisan tinker --execute="echo App\Models\User::where('role','admin')->count().PHP_EOL;"  # expect >= 1

# 7. API keys resolve end to end.
php artisan route:list --name=api-keys                 # expect 3 routes

# 8. The whole gate.
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
