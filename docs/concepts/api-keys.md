# API keys

Machine callers authenticate with an API key. Keys are Laravel Sanctum personal access tokens — no
custom table, no second auth system — issued and revoked from `/admin/api-keys`.

## What a key is

A plaintext key looks like `12|apik_<random><crc>`: the token id, a pipe, then the configured prefix
and the secret. Only a SHA-256 hash of the secret half is stored, so **a key cannot be recovered after
it is issued** — it is shown once, at creation, and never again.

The `apik_` prefix (`SANCTUM_TOKEN_PREFIX`) exists so secret scanners, GitHub's included, can recognise
a leaked key on sight. Change it to something unique to your product; changing it does not invalidate
keys already issued.

| Piece | Owns |
|---|---|
| `ApiAbility` | The closed set of abilities a key may carry |
| `ApiKeyService` | Issue, list, revoke — and the three invariants below |
| `EnsureApiKey` (`api.key`) | Proves a real key was presented and its owner is active |
| `ApiKeyPolicy` | Who may manage keys, and whose keys they may revoke |

## The three invariants

**Abilities come from the enum, never from request input.** `StoreApiKeyRequest` validates against
`ApiAbility` and `ApiKeyService::issue()` validates again — the service is reachable from a command or
a job that never passes through the request. A caller who could send an arbitrary abilities list could
widen its own scope.

**A key expires unless somebody deliberately says otherwise.** `issue()` takes 1–365 days and defaults
to 90. `ApiKeyService::NEVER_EXPIRES` (0) issues a key with no expiry — it must be passed by name, and
the admin screen only reaches it through an explicit "Never expires" checkbox. Omitting the lifetime
gives the default, never a permanent key. Sanctum's guard rejects an expired token by itself, which is
why no read path checks the date.

A non-expiring key is the right answer for a caller that genuinely cannot rotate, and the wrong one
everywhere else: it outlives the person who created it, and revocation becomes the only way to end it.

**The plaintext exists once, in transit.** It is returned in an `IssuedApiKey` and rendered directly by
the request that created it. It is never logged, never flashed to the session, and never persisted.

## Why issuance renders instead of redirecting

The obvious shape — flash the key, redirect, read it back — writes it into the session store, which in
production is a database row. The one value that must exist only in transit would then be at rest.
Rendering from the request that created it keeps the plaintext in exactly one response body.

That leaves the browser: Inertia keeps a page's props in `history.state`, where the back button would
find it. `ApiKeyController::store()` wraps the render in `encryptHistory()` and resets the flag
immediately afterwards — the response factory is resolved once per worker, so a sticky flag would
spread to every later render. The page also calls `router.clearHistory()` when the operator dismisses
the dialog.

## The route chain, and why each link is load-bearing

```
throttle:api  →  auth:sanctum  →  api.key  →  abilities:<ability>
```

`api.key` is not redundant with `auth:sanctum`. **`config('sanctum.guard')` includes `web`**, so
`auth:sanctum` also accepts a logged-in browser session, which Sanctum represents as a
`TransientToken` — and `TransientToken::can()` returns `true` for *every* ability. Without `api.key`,
an `abilities:` check passes for any signed-in user carrying no key at all. Requiring a real
`PersonalAccessToken` owned by the authenticated user is what makes the ability check mean anything.

`api.key` also re-checks `is_active`. `EnsureUserIsActive` is appended to the **web** group only, so a
key belonging to a deactivated user would otherwise keep authenticating — deactivating an account has
to disable its keys in the same act.

## How it fails

| Symptom | Cause |
|---|---|
| A signed-in user reaches an `abilities:`-gated route with no key | `api.key` is missing from that route's middleware. It is not optional; `auth:sanctum` alone accepts a session. |
| A deactivated user's integration keeps working | Same — `EnsureUserIsActive` never runs on `api` routes. |
| A key stops working after ~90 days | Working as designed — that is the default lifetime. Issue a replacement, or use "Never expires" if the caller cannot rotate. |
| A key meant to be permanent expired anyway | The lifetime reached the service as `null` rather than `0`. Anything that coerces falsy values (`?:`) turns NEVER_EXPIRES back into the default. |
| The plaintext appears in `storage/logs` | Something logged the `IssuedApiKey` or the request. Neither may be logged. |
| A leaked key is still valid after "deleting" it | Revocation must be a hard delete. A soft-deleted key that still resolves authenticates as a downgraded user rather than as nobody. |
| Adding an ability to the enum changes nothing | An ability is only a scope once a route enforces it with `abilities:`. Add the case and the route in the same change. |
| `abilities:` routes 500 or never match | The `abilities`/`ability` aliases are registered in `bootstrap/app.php`. Sanctum ships the middleware but does not register them in Laravel 11+. |

## Issuing from the console

`php artisan api-key:create` covers the cases with no browser — seeding an environment,
provisioning CI, scripting a deploy:

```sh
php artisan api-key:create --user=ops@example.com --name="CI pipeline" \
    --ability=api:read --ability=api:write --days=30
php artisan api-key:create --user=ops@example.com --name=Integration --never-expires
```

Like the screen, it prints the plaintext once. It refuses an unknown owner, a deactivated owner (the
key would be refused at request time anyway) and an ability outside the enum, listing the valid ones.

## Adding an ability

1. Add the case to `App\Enums\ApiAbility` with a `label()`.
2. Put `abilities:<value>` on the routes it governs — in the same change.
3. Cover the refusal: a key without the ability must be rejected.

Existing keys are unaffected; abilities are stored per key at issue time.
