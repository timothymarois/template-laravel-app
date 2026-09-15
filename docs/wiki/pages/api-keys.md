+++
title = "API keys"
subtitle = "the form of a key, its abilities and expiry, and revocation"
status = "approved"
intent = """
API keys exist so that a machine caller reaches the API with a credential that is scoped, expires and can
be revoked, without a second identity system. A key should be shown once, expire unless an operator
decides otherwise, and stop working the moment its owner is deactivated.
"""

[[infobox]]
group = "Identity"
rows = [
  { label = "Prefix", value = "apik_", cite = "prefix" },
  { label = "Abilities", value = "api:read, api:write", cite = "abilities" },
]

[[infobox]]
group = "Lifetime"
rows = [
  { label = "Default lifetime", value = "90 days", cite = "lifetime" },
  { label = "Longest lifetime", value = "365 days", cite = "lifetime" },
  { label = "No expiry", value = "on request only", cite = "lifetime" },
]

[[infobox]]
group = "Rules"
rows = [
  { label = "Plaintext", value = "shown once, never stored", cite = "hash" },
  { label = "Revocation", value = "immediate and permanent", cite = "revoke" },
  { label = "Deactivated owner", value = "refused", cite = "inactive" },
]
+++

An **API key** is a bearer credential an operator issues to themselves for a machine caller; it carries one
or more abilities and, unless asked otherwise, an expiry date.[^issue] Issuing and revoking
on the admin screen are described on [API keys](admin/api-keys.md), and what a caller does with a key on
[API](api.md).

## Form

A key begins with its number, a pipe and the prefix `apik_`, then the secret.[^prefix] The prefix comes from
`SANCTUM_TOKEN_PREFIX`, so a secret scanner recognises a leaked key on sight.[^prefix] Only a hash of the
secret is stored, so a key cannot be shown again after it is issued.[^hash]

## Abilities

A key carries `api:read`, `api:write` or both, and a key with no ability is refused at issue with
`An API key must carry at least one ability.`[^abilities] `api:read` opens every endpoint the template
ships.[^read] No endpoint asks for `api:write`; it is held for a fork's first write route.[^write]

## Expiry

A key lives 90 days unless the issuer chooses between 1 and 365 days, or asks for no expiry by name; any
other lifetime is refused with `An API key lifetime must be 0 (never) or between 1 and 365 days.`[^lifetime]
An expired key is refused as unauthenticated, and the screen shows its expiry in red.[^expired]

## Revocation

Revoking a key deletes it, so a caller presenting it afterwards is refused as unauthenticated rather than
downgraded.[^revoke] A key whose owner has been deactivated is refused with
`Your account has been deactivated.`[^inactive]

[^issue]: `app/Services/ApiKeyService.php` — `issue()` creates a personal access token with the name,
    abilities and expiry it is given and returns the plaintext once in an `IssuedApiKey`.
[^prefix]: `config/sanctum.php` — `token_prefix` reads `SANCTUM_TOKEN_PREFIX`, `apik_` by default, and
    its comment names secret scanning; `vendor/laravel/sanctum/src/HasApiTokens.php` — `createToken()`
    builds the plaintext as the id, a pipe, the prefix and the secret.
[^hash]: `vendor/laravel/sanctum/src/HasApiTokens.php` — `createToken()` stores `hash('sha256', …)` of
    the secret; `app/Services/ApiKeyService.php` — the class comment: the plaintext is returned once and
    never stored, logged or flashed.
[^abilities]: `app/Enums/ApiAbility.php` — cases `Read = 'api:read'` and `Write = 'api:write'`;
    `app/Services/ApiKeyService.php` — `issue()` refuses an empty list and an ability outside the enum.
[^read]: `routes/api.php` — every route sits under `abilities:api:read`.
[^write]: `app/Enums/ApiAbility.php` — the comment on `Write`: no route enforces it, and it waits for a
    fork's first write route.
[^lifetime]: `app/Services/ApiKeyService.php` — `DEFAULT_LIFETIME_DAYS` 90, `MAX_LIFETIME_DAYS` 365,
    `NEVER_EXPIRES` 0, and `issue()` raising the message for any other value.
[^expired]: `app/Http/Middleware/EnsureApiKey.php` — `handle()` raises an authentication error when
    `expires_at` is past; `resources/js/pages/admin/api-keys/Index.vue` — `isExpired()` colours the
    expiry.
[^revoke]: `app/Services/ApiKeyService.php` — `revoke()` deletes the row, and the class comment says a
    revoked key must authenticate as no account; `app/Http/Middleware/EnsureApiKey.php` — `handle()` raises an
    authentication error when no key matches.
[^inactive]: `app/Http/Middleware/EnsureApiKey.php` — `handle()` aborts with 403 and
    `Your account has been deactivated.` when the owner is not active, and its comment says why.
