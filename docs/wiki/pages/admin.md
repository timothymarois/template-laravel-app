+++
title = "Admin area"
subtitle = "the /admin surface: dashboard, users, API keys and the component showcase"
status = "approved"
intent = """
The admin area exists so that an operator manages the application from one place behind one gate, and so
that a screen added there is refused by default until it is authorised. A signed-in account without the
operator role should see none of it.
"""

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
  { label = "Own account", value = "cannot be deleted", cite = "self" },
]
+++

The **admin area** is everything under `/admin`, and only an account with the `admin` role reaches it;
every other signed-in account is answered `403`.[^gate] Accounts and roles are described on
[Accounts](accounts.md).

## Gate

The role gate covers the whole prefix, so a screen added under it is refused by default; each screen then
asks the operator's policy about the resource it shows.[^gate] The first operator comes from the console,
as [user:create](commands/user-create.md) describes, or from the seed, which creates `admin@example.com`
as an administrator beside a plain user.[^first]

## Screens

The dashboard at `/admin` is a placeholder page reading `This is an example home page.`[^dashboard]
[Users](admin/users.md) lists, creates, edits and deletes accounts.[^screens] The API keys screen at
`/admin/api-keys` issues and revokes the operator's own keys, as [Issuing](api-keys/issuing.md)
describes.[^screens] The [component showcase](components.md) at `/admin/components` renders every part
of the interface kit.[^screens]

## Refusals

| Condition | Answer |
|---|---|
| a signed-out reader on any `/admin` address | the sign-in page[^unauth] |
| a signed-in `user` | `403`[^gate] |
| a deactivated account | signed out and sent to the sign-in page with `Your account has been deactivated.`[^inactive] |
| an operator deleting their own account | `403`[^self] |

[^gate]: `routes/web.php` — the `admin` prefix carries `auth:sanctum` and `admin`;
    `app/Http/Middleware/EnsureUserIsAdmin.php` — `handle()` aborts with 403 unless the role
    `canAccessAdmin()`, and its comment says the policies still decide each resource;
    `app/Policies/UserPolicy.php` and `app/Policies/ApiKeyPolicy.php` — every ability asks
    `canManageAllUsers()`.
[^first]: `app/Console/Commands/CreateUser.php` — `--admin`; `database/seeders/DatabaseSeeder.php` —
    `run()` creates `admin@example.com` as `SuperAdmin` and `test@example.com` as a user.
[^dashboard]: `routes/web.php` — `GET admin/`; `app/Http/Controllers/Admin/DashboardController.php` —
    `index()` renders `admin/Index`; `resources/js/pages/admin/Index.vue` — the placeholder text.
[^screens]: `routes/web.php` — the `users` resource, the three `api-keys` routes and the `components`
    prefix under `admin`; `app/Http/Controllers/Admin/UserController.php`,
    `app/Http/Controllers/Admin/ApiKeyController.php` and `routes/components.php`.
[^unauth]: `routes/web.php` — the `auth:sanctum` group; the framework's `auth` middleware sends a guest
    browser request to the `login` route.
[^inactive]: `app/Http/Middleware/EnsureUserIsActive.php` — `handle()` ends the session and redirects to
    `login` with `Your account has been deactivated.`.
[^self]: `app/Policies/UserPolicy.php` — `delete()` refuses when the actor is the target;
    `app/Http/Controllers/Admin/UserController.php` — `destroy()` authorises `delete` first.
