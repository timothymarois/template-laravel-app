+++
title = "GET /api/user"
subtitle = "the caller's own account"
status = "approved"
goals = false
intent = """
GET /api/user exists so that a caller confirms which account a key belongs to, and proves a key works,
with one request.
"""

[[infobox]]
group = "Identity"
rows = [
  { label = "Method", value = "GET", cite = "route" },
  { label = "Path", value = "/api/user", cite = "route" },
  { label = "Ability", value = "api:read", cite = "route" },
]

[[infobox]]
group = "Rules"
rows = [
  { label = "Fields", value = "id, name, email, role", cite = "route" },
]
+++

`GET /api/user` returns the account that owns the key presented.[^route] The checks every request passes,
and their refusals, are described on [API](../api.md).

## Request

The request carries the key as a bearer token and nothing else.[^route]
```http
GET /api/user
Authorization: Bearer KEY
Accept: application/json
```

## Responses

| Status | When | Body |
|---|---|---|
| `200` | the key passed every check[^route] | the owner's `id`, `name`, `email` and `role` |
| `401` | no key, an unknown, expired or foreign key[^refusals] | `{"message":"Unauthenticated."}` |
| `403` | a deactivated owner, or a key without `api:read`[^refusals] | `{"message":"Your account has been deactivated."}` or `{"message":"Invalid ability provided."}` |
| `429` | the 61st request in a minute[^refusals] | `{"message":"Too Many Attempts."}` |

## Example

The response to a key issued to the seeded administrator, and the answer to the same request without a
key, both copied from a run against a local site.[^route]
```json
{"id":1,"name":"Test Admin","email":"admin@example.com","role":"admin"}
```
```json
{"message":"Unauthenticated."}
```

[^route]: `routes/api.php` — `GET /user` under `throttle:api`, `auth:sanctum`, `api.key` and
    `abilities:api:read`, returning the user's `id`, `name`, `email` and `role`.
[^refusals]: `app/Http/Middleware/EnsureApiKey.php` — `handle()`;
    `app/Providers/AppServiceProvider.php` — `configureRateLimiting()`;
    `vendor/laravel/sanctum/src/Exceptions/MissingAbilityException.php` — the ability message.
