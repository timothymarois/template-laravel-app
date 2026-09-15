+++
title = "Adoption"
subtitle = "moving an existing product's data into tenants, and the pitfalls met on the way"
status = "approved"
goals = false
intent = """
Adoption exists so that a product with customers already in one database moves them into workspaces
without losing a row, and can go back if the cutover fails. The rehearsal on a copy of production is the
step that decides whether it works.
"""

[[infobox]]
group = "Rules"
rows = [
  { label = "Migrator", value = "idempotent and resumable", missing = true },
  { label = "Rehearsal", value = "on a restored copy of production", missing = true },
  { label = "Rollback", value = "written before the cutover", missing = true },
]
+++

Adoption moves each account's rows from one central database into that account's own tenant database, in
an order a fork rehearses before it runs for real.{missing} Adding tenancy to a fork in the first place is
described on [Multi-tenancy](../tenancy.md).

## Steps

1. Categorise every table as central or tenant, and write the list down: identity, infrastructure and
   billing stay central; anything a customer owns becomes tenant data.{missing}
2. Audit every reference to a user: a foreign key cannot span two databases, so those constraints are
   dropped, the user's id is copied into the tenant row, and integrity is kept in the application.{missing}
3. Write the migrator, which creates a tenant, provisions it, copies that account's rows and attaches the
   pivot; it must be idempotent and resumable, because it fails partway at least once.{missing}
4. Rehearse against a restored copy of production until the audit log is clean for every user.{missing}
5. Run it on staging in full, then in production under maintenance mode.{missing}
6. Write the rollback before the cutover: the central data untouched, the tenant databases droppable, a
   tested path back.{missing}

## Deletion

Deleting a tenant model dispatches `TenantDeleted`, and dropping its database is a separate event a fork
chooses to listen to.[^deleted] A grace window between the two, a scheduled purge of tenants deleted more
than a set number of hours ago, makes an accidental deletion recoverable.{missing}

## Pitfalls

| Symptom | Cause |
|---|---|
| A tenant query from central context returns nothing | tenancy is not initialised there; enter the tenant with `tenancy()->initialize($tenant)` first[^manual] |
| Queued jobs write to the wrong database | `QueueTenancyBootstrapper` is missing from `tenancy.bootstrappers`[^queue] |
| Broadcasts leak across tenants | channel names in `routes/channels.php` do not include the tenant's id{missing} |
| A tenant-scoped change passes the checks but breaks in production | the default suite ran, which has no tenant context{missing} |
| A tenant model errors about a missing or duplicate table | its inferred table name collides with a central table; set `$table`{missing} |
| Users see another tenant's data | identification without authorisation: the membership middleware is missing from that route group{missing} |
| The session is lost between subdomains | the session cookie's domain is the bare host, not the parent domain{missing} |

[^deleted]: Tenancy for Laravel — [Event system](https://tenancyforlaravel.com/docs/v3/event-system/):
    `TenantDeleted` is dispatched from the model's Eloquent event, and `DatabaseDeleted` is an optional
    database event.
[^manual]: Tenancy for Laravel — [Manual initialization](https://tenancyforlaravel.com/docs/v3/manual-initialization/):
    `tenancy()->initialize($tenant)` enters a tenant outside middleware.
[^queue]: Tenancy for Laravel — [Tenancy bootstrappers](https://tenancyforlaravel.com/docs/v3/tenancy-bootstrappers/):
    `QueueTenancyBootstrapper` initialises tenancy for a queued job from the id in its payload.
