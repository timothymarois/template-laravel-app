+++
title = "Issuing"
subtitle = "the API keys screen and the api-key:create command"
status = "approved"
goals = false
intent = """
Issuing exists so that an operator gets a working key in seconds, sees its plaintext exactly once, and can
revoke it as fast. The plaintext should never be written anywhere but the response that created it.
"""

[[infobox]]
group = "Identity"
rows = [
  { label = "Screen", value = "/admin/api-keys", cite = "both" },
  { label = "Command", value = "api-key:create", cite = "both" },
]

[[infobox]]
group = "Form"
rows = [
  { label = "Fields", value = "Name, Abilities, Expires after (days), Never expires", cite = "form" },
]

[[infobox]]
group = "Rules"
rows = [
  { label = "Plaintext", value = "one response, never the session", cite = "render" },
  { label = "Other operators' keys", value = "not revocable", cite = "policy" },
]
+++

An operator issues keys to themselves on `/admin/api-keys`, or from the console with
[api-key:create](../commands/api-key-create.md); both show the key once.[^both] What a key is and how long
it lives is described on [API keys](../api-keys.md).

## Screen

The screen lists the operator's own keys, newest first, with their abilities, last use, expiry and creation
date, and reads `No API keys yet` when there are none.[^list] `Create key` opens a form with `Name`,
`Abilities` as one checkbox per ability, `Expires after (days)` and a `Never expires` checkbox that
disables the days field.[^form] Leaving the days empty gives the default lifetime.[^default] A name is
required, at least one ability must be ticked, and the days must be a whole number from 0 to
365.[^rules]

## Plaintext

The new key appears in a dialog headed `Copy your key now`, with
`This is the only time the key is shown. Only its hash is stored.` and a `Copy` button.[^dialog] The
server renders that page from the request that created the key instead of redirecting, so the plaintext
is never written to the session store, and the browser's history entry for it is encrypted; `Done` clears
that entry and reloads the list.[^render]

## Revocation

`Revoke` on a row asks `Revoke “name”? Any caller using it stops working immediately, and this cannot be
undone.` and, confirmed, deletes the key and reports `API key “name” revoked`.[^revoke] An operator
revokes only their own keys: a guessed id belonging to another operator is answered `403`, and only an
administrator sees the screen at all.[^policy]

[^both]: `routes/web.php` — the three `api-keys` routes under `admin`;
    `app/Console/Commands/CreateApiKey.php` — `$signature` `api-key:create`, and `handle()` printing the
    key once; `app/Http/Controllers/Admin/ApiKeyController.php` — `store()`.
[^list]: `app/Http/Controllers/Admin/ApiKeyController.php` — `page()` passes each key's `name`,
    `abilities`, `last_used_at`, `expires_at` and `created_at`; `app/Services/ApiKeyService.php` —
    `listFor()` orders newest first; `resources/js/pages/admin/api-keys/Index.vue` — the columns and
    `emptyLabel`.
[^form]: `resources/js/pages/admin/api-keys/Index.vue` — the `Create key` button, the dialog
    `Create API key`, its fields and the `Never expires` checkbox that disables `lifetime_days`.
[^default]: `resources/js/pages/admin/api-keys/Index.vue` — `submit()` sends `0` for never and `null` for
    an empty field; `app/Services/ApiKeyService.php` — `issue()` reads `null` as
    `DEFAULT_LIFETIME_DAYS`.
[^rules]: `app/Http/Requests/ApiKey/StoreApiKeyRequest.php` — `rules()`: `name` required,
    `abilities` at least one enum case, `lifetime_days` an integer from 0 to 365.
[^dialog]: `resources/js/pages/admin/api-keys/Index.vue` — the dialog `Copy your key now`, its alert
    text and the `Copy` and `Done` buttons.
[^render]: `app/Http/Controllers/Admin/ApiKeyController.php` — `store()` renders the page inside
    `encryptHistory()`, and its comment says why it does not redirect;
    `resources/js/pages/admin/api-keys/Index.vue` — `dismissIssued()` calls `router.clearHistory()` and
    reloads.
[^revoke]: `resources/js/pages/admin/api-keys/Index.vue` — the `Revoke` button, the confirmation
    message and the toasts; `app/Http/Controllers/Admin/ApiKeyController.php` — `destroy()` revokes and
    flashes `API key revoked.`.
[^policy]: `app/Policies/ApiKeyPolicy.php` — `viewAny()` and `create()` need `canManageAllUsers()`, and
    `delete()` also needs the key to belong to the actor.
