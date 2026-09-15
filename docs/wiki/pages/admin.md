+++
title = "Admin area"
subtitle = "the /admin surface: dashboard, users, API keys and the component showcase"
status = "approved"
intent = """
The admin area exists so that an operator manages the application from one place behind one gate, and so
that a screen added there is refused by default until it is authorised. A signed-in account without the
operator role should see none of it.
"""

[family]
headings = ["List", "Adding", "Editing", "Removal"]
labels = ["Address", "Page size", "Sort", "Fields", "Plaintext", "Removal", "Removal refused"]
table = ["Address", "Removal", "Removal refused"]

[[infobox]]
group = "Identity"
rows = [
  { label = "Address", value = "/admin", cite = "gate" },
  { label = "Role", value = "admin", cite = "gate" },
]

[[infobox]]
group = "Contents"
rows = [
  { label = "Screens", value = "Users, API keys, Components", cite = "screens" },
]

[[infobox]]
group = "Rules"
rows = [
  { label = "Other roles", value = "403", cite = "gate" },
]
+++

The **admin area** is everything under `/admin`, and only an account with the `admin` role reaches it;
every other signed-in account is answered `403`.[^gate] Accounts and roles are described on
[Accounts](accounts.md). The [Users](admin/users.md) and [API keys](admin/api-keys.md) screens each have a
page covering the list, adding, editing and removal, where the screen has them.

## Gate

The role gate covers the whole prefix, so a screen added under it is refused by default; each screen then
asks the operator's policy about the resource it shows.[^gate] The first operator is described on
[Setup](setup.md).

## Screens

The dashboard at `/admin` is a placeholder page reading `This is an example home page.`[^dashboard] The
[component showcase](components.md) at `/admin/components` renders every part of the interface
kit.[^screens]

{family-table}

## Refusals

| Condition | Answer |
|---|---|
| a signed-out reader on any `/admin` address | the sign-in page[^unauth] |
| a signed-in `user` | `403`[^gate] |
| a deactivated account | refused, as [Accounts](accounts.md) describes |

[^gate]: `routes/web.php` — the `admin` prefix carries `auth:sanctum` and `admin`;
    `app/Http/Middleware/EnsureUserIsAdmin.php` — `handle()` aborts with 403 unless the role
    `canAccessAdmin()`, and its comment says the policies still decide each resource;
    `app/Policies/UserPolicy.php` and `app/Policies/ApiKeyPolicy.php` — every ability asks
    `canManageAllUsers()`.
[^dashboard]: `routes/web.php` — `GET admin/`; `app/Http/Controllers/Admin/DashboardController.php` —
    `index()` renders `admin/Index`; `resources/js/pages/admin/Index.vue` — the placeholder text.
[^screens]: `routes/web.php` — the `users` resource, the three `api-keys` routes and the `components`
    prefix under `admin`; `app/Http/Controllers/Admin/UserController.php`,
    `app/Http/Controllers/Admin/ApiKeyController.php` and `routes/components.php`.
[^unauth]: `routes/web.php` — the `auth:sanctum` group; the framework's `auth` middleware sends a guest
    browser request to the `login` route.
