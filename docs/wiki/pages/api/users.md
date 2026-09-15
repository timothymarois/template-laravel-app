+++
title = "GET /api/users"
subtitle = "a page of accounts, for an administrator's key"
status = "approved"
goals = false
intent = """
GET /api/users exists so that an integration reads the account list with paging, search and sorting, and
so that a key held by a plain user is refused even when it carries the read ability.
"""

[[infobox]]
group = "Identity"
rows = [
  { label = "Method", value = "GET", cite = "route" },
  { label = "Path", value = "/api/users", cite = "route" },
  { label = "Ability", value = "api:read", cite = "route" },
]

[[infobox]]
group = "Limits"
rows = [
  { label = "Page size", value = "15", note = "at most 100", cite = "params" },
  { label = "Sort fields", value = "id, name, email, created_at", cite = "params" },
]

[[infobox]]
group = "Rules"
rows = [
  { label = "Owner", value = "administrator only", cite = "owner" },
  { label = "Fields", value = "id, name, email, role, is_active, created_at", cite = "fields" },
]
+++

`GET /api/users` returns one page of accounts, and only to a key whose owner is an administrator: the
ability says what the key is allowed to do, and the owner's role says whether the list is open to it.[^owner] The
checks every request passes are described on [API](../api.md).

## Request

The request carries the key as a bearer token, and every parameter is optional and travels in the query string.[^route][^params]
```http
GET /api/users?perPage=2&sortField=name
Authorization: Bearer KEY
Accept: application/json
```

## Parameters

| Name | In | Type | Required | Meaning |
|---|---|---|---|---|
| `search` | query | string, at most 255 characters | no | matches a name or an email address[^search] |
| `page` | query | integer, 1 or more | no | the page to return; absent means the first[^params] |
| `perPage` | query | integer, 1 to 100 | no | accounts per page; absent means 15[^params] |
| `sortField` | query | one of `id`, `name`, `email`, `created_at` | no | the field to sort by; absent means `id`[^params] |
| `sortOrder` | query | `1` or `-1` | no | ascending or descending; absent means ascending[^params] |

## Responses

| Status | When | Body |
|---|---|---|
| `200` | the key and its owner passed every check[^fields] | `data`, one object per account with `id`, `name`, `email`, `role`, `is_active` and `created_at`, and `meta` with `current_page`, `per_page`, `total` and `last_page` |

## Errors

| Status | Condition | Message |
|---|---|---|
| `401` | no key, an unknown, expired or foreign key[^refusals] | `Unauthenticated.` |
| `403` | the key's owner is not an administrator[^owner] | `This action is unauthorized.` |
| `403` | a deactivated owner, or a key without `api:read`[^refusals] | `Your account has been deactivated.` or `Invalid ability provided.` |
| `422` | `perPage` above 100[^params] | `The per page field must not be greater than 100.` |
| `422` | `sortField` outside the four[^params] | `The selected sort field is invalid.` |
| `429` | the 61st request in a minute[^refusals] | `Too Many Attempts.` |

## Example

The response to the request above on a local site with four accounts, and the answer to
`perPage=500`, both copied from a run.[^fields]
```json
{"data":[{"id":4,"name":"Plain Person","email":"plain@example.com","role":"user","is_active":false,"created_at":"2026-09-08T18:21:50+00:00"},{"id":1,"name":"Test Admin","email":"admin@example.com","role":"admin","is_active":true,"created_at":"2026-09-08T18:21:40+00:00"}],"meta":{"current_page":1,"per_page":2,"total":4,"last_page":2}}
```
```json
{"message":"The per page field must not be greater than 100.","errors":{"perPage":["The per page field must not be greater than 100."]}}
```

[^route]: `routes/api.php` — `GET /users` under `throttle:api`, `auth:sanctum`, `api.key` and
    `abilities:api:read`.
[^owner]: `app/Http/Requests/Api/ListUsersRequest.php` — `authorize()` asks `viewAny` on users;
    `app/Policies/UserPolicy.php` — `viewAny()` needs `canManageAllUsers()`;
    `app/Http/Controllers/Api/UserController.php` — the comment on `index()`;
    `vendor/laravel/framework/src/Illuminate/Auth/Access/AuthorizationException.php` — the message.
[^params]: `app/Http/Requests/Api/ListUsersRequest.php` — `rules()`: `search`, `page`, `perPage` from 1
    to 100, `sortField` allow-listed, `sortOrder` 1 or -1; `app/Http/Controllers/Api/UserController.php`
    — `index()` defaults `perPage` to 15; `app/Http/Concerns/InertiaDataTableOptions.php` —
    `resolveIndexOptions()` defaults `sortField` to `id` and `sortOrder` to 1.
[^search]: `app/Services/Models/UserService.php` — `buildQuery()` applies the search to `name` and
    `email`.
[^fields]: `app/Http/Controllers/Api/UserController.php` — `index()` names the six fields of each row and
    the four `meta` values, and its comment says why the model is not returned.
[^refusals]: `app/Http/Middleware/EnsureApiKey.php` — `handle()`;
    `app/Providers/AppServiceProvider.php` — `configureRateLimiting()`;
    `vendor/laravel/sanctum/src/Exceptions/MissingAbilityException.php` — the ability message.
