+++
title = "Users"
subtitle = "the account list, its search and sorting, and the add, edit and archive actions"
status = "approved"
goals = false
intent = """
The Users screen exists so that an operator sees every account at a glance and changes a name, an address
or a role without leaving the list. It should never let an operator delete themselves.
"""

[[infobox]]
group = "Identity"
rows = [
  { label = "Address", value = "/admin/users", cite = "list" },
]

[[infobox]]
group = "Defaults"
rows = [
  { label = "Page size", value = "50", cite = "list" },
  { label = "Sort", value = "by name, ascending", cite = "list" },
]

[[infobox]]
group = "Rules"
rows = [
  { label = "Role", value = "required when adding or editing", cite = "rules" },
  { label = "Own account", value = "not deletable", cite = "self" },
  { label = "Archive", value = "a permanent delete", cite = "delete" },
]
+++

`/admin/users` lists every account, 50 to a page and sorted by name, with a search box over name and email
address.[^list] The search, sort, page size and chosen columns are kept for the session, as
[Data tables](../components/data-tables.md) describes.[^session]

## List

`Search user name or email` matches either field.[^search] The page size is chosen at the foot of the
list, and the columns and sort in the column menu.[^page] A name opens the account's own page at
`/admin/users/{id}`.[^show]

## Adding and editing

`Add user` opens a form with `Name`, `Email` and `Role`, and `Edit` in a row's menu opens the same form
filled in.[^form] All three are required; the address must be unique among accounts, and the role is
`Administrator` or `User`.[^rules] The form says `An administrator can reach /admin and manage every user.`
beside the role.[^form] A new account gets a random password its owner never sees, so the owner signs in
through [Password reset](../accounts/password-reset.md).[^password]

## Archiving

`Archive` in a row's menu asks `Are you sure you want to archive this user? This action can be undone.`
and, on `Archive`, deletes the account.[^delete] The deletion is permanent.[^hard] An operator cannot
archive their own account; the server answers `403`.[^self]

[^list]: `routes/web.php` — the `users` resource under `admin`;
    `app/Http/Controllers/Admin/UserController.php` — `index()` with `$indexDefaults` of `perPage` 50,
    `sortField` `name` and `sortOrder` 1; `resources/js/pages/admin/users/Index.vue` — the search box
    and the table.
[^session]: `app/Http/Concerns/InertiaDataTableOptions.php` — `resolveIndexOptions()` stores and reads
    `search`, `filters`, `viewFields`, `perPage`, `sortField` and `sortOrder` under
    `options.admin.users.index` in the session.
[^search]: `app/Services/Models/UserService.php` — `buildQuery()` applies the search to `name` and
    `email`; `resources/js/pages/admin/users/Index.vue` — the placeholder `Search user name or email`.
[^page]: `resources/js/pages/admin/users/Index.vue` — the `perPage` select in the footer and
    `CustomizeColumns` with its sort.
[^show]: `resources/js/pages/admin/users/Index.vue` — the name links to `admin.users.show`;
    `app/Http/Controllers/Admin/UserController.php` — `show()`.
[^form]: `resources/js/components/app/modals/EditUserModal.vue` — the headers `Add user` and
    `Edit user`, the three fields, the role help text and the buttons `Create user` and `Save changes`;
    `resources/js/pages/admin/users/Index.vue` — the `Add user` button and the row menu.
[^rules]: `app/Http/Requests/User/StoreUserRequest.php` and
    `app/Http/Requests/User/UpdateUserRequest.php` — `rules()`: `name`, `email` and `role` required,
    `email` unique, `role` an enum case; `app/Enums/UserRole.php` — `label()`.
[^password]: `app/Services/Models/UserService.php` — `create()` fills `password` with 24 random
    characters when none is given.
[^delete]: `resources/js/components/app/modals/DeleteUserModal.vue` — the title `Archive user`, the
    message and the `Archive` button; `app/Http/Controllers/Admin/UserController.php` — `destroy()`.
[^hard]: `app/Services/Models/ModelService.php` — `delete()` calls the model's `delete()`;
    `app/Models/User.php` — no soft-delete trait.
[^self]: `app/Policies/UserPolicy.php` — `delete()` refuses when the actor is the target.
