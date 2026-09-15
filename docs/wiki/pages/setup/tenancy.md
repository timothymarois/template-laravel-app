+++
title = "Multi-tenancy"
subtitle = "the decisions and steps a fork follows to add isolated workspaces with stancl/tenancy"
status = "approved"
goals = false
intent = """
Multi-tenancy exists so that a product whose customers each need their own isolated data adds workspaces
to a fork without the template carrying the code for every product that does not. The template ships
none of it; what a fork builds here is its own, and template upgrades never touch it.
"""

[[infobox]]
group = "Identity"
rows = [
  { label = "Package", value = "stancl/tenancy 3", link = "https://tenancyforlaravel.com/docs/v3/", cite = "install" },
  { label = "Central route file", value = "routes/tenant.php", cite = "install" },
]

[[infobox]]
group = "Rules"
rows = [
  { label = "Template", value = "ships no tenancy", cite = "none" },
  { label = "Recommended mode", value = "path, under /t/{tenant}", missing = true },
  { label = "Sign-in", value = "on the central domain", missing = true },
]
+++

The template ships no tenancy: no package, no tenant routes, no central connection.[^none] A fork that
needs many workspaces installs `stancl/tenancy` itself and owns the result.[^install] Moving an existing
product's data into tenants is described on [Adoption](tenancy/adoption.md).

## Decisions

Two choices are expensive to reverse once there are customers.{missing}

| Choice | Path mode | Subdomain mode |
|---|---|---|
| Address | `app.example.com/t/acme/dashboard`{missing} | `acme.example.com/dashboard`{missing} |
| Identification | `InitializeTenancyByPath`, routes prefixed `/{tenant}`[^identify] | `InitializeTenancyBySubdomain`, on any central domain[^identify] |
| Operations | none beyond the domain already served{missing} | wildcard DNS and a wildcard certificate, and a session cookie scoped to the parent domain{missing} |

Path mode stays viable for good; subdomains are chosen for a marketing reason, never on the assumption
that paths will be outgrown.{missing} The second choice is one workspace per user or several: both use the
same `tenant_user` pivot, so a fork builds for one and adds the picker later.{missing}

## Steps

1. Install the package and run `php artisan tenancy:install`, which publishes `config/tenancy.php`,
   `routes/tenant.php`, `app/Providers/TenancyServiceProvider.php` and the migrations; add the provider
   to `bootstrap/providers.php`.[^install]
2. Move tenant migrations to `database/migrations/tenant`, which `tenants:migrate` reads through the
   `--path` entry of `migration_parameters`; central tables, such as `users` and `personal_access_tokens`,
   stay in a central folder the provider loads.[^migrations]
3. Give every central model, `User` above all, an explicit connection named by
   `tenancy.central_connection`, and add the `tenant_user` pivot.[^central]
4. Copy the database connection as a central one and point `tenancy.central_connection` at it.[^central]
5. Register tenant routes in `routes/tenant.php` behind the identification middleware, and behind a
   middleware of the fork's own that refuses a user who does not belong to the workspace: identification
   proves which tenant was asked for, never that the caller is entitled to it.{missing}
6. Run the central migrations against the central connection.{missing}
7. Provision a tenant by creating its model: the `TenantCreated` event runs the `CreateDatabase` and
   `MigrateDatabase` jobs, queued when the pipeline says `shouldBeQueued(true)`.[^pipeline] A queued
   pipeline means the workspace is not ready the moment its user is redirected there, so a readiness check
   answers `503` until it is.{missing}
8. Keep `QueueTenancyBootstrapper` in `tenancy.bootstrappers`, so a queued job runs in the tenant it was
   dispatched from.[^queue]
9. Add a second test suite with tenancy initialised, because the default suite has no tenant context and
   proves nothing about tenant code.{missing}

Code outside a tenant request enters a workspace with `tenancy()->initialize($tenant)`.[^manual]

[^none]: `composer.json` — `require` lists no `stancl/tenancy`; `routes/web.php` — no route carries a
    tenant parameter or middleware.
[^install]: Tenancy for Laravel — [Installation](https://tenancyforlaravel.com/docs/v3/installation/):
    `composer require stancl/tenancy` then `php artisan tenancy:install` creates the migrations,
    `config/tenancy.php`, `routes/tenant.php` and `app/Providers/TenancyServiceProvider.php`, which is
    added to `bootstrap/providers.php`.
[^identify]: Tenancy for Laravel — [Tenant identification](https://tenancyforlaravel.com/docs/v3/tenant-identification/):
    `InitializeTenancyByPath` with routes prefixed `/{tenant}`, and `InitializeTenancyBySubdomain`, which
    works on any central domain.
[^migrations]: Tenancy for Laravel — [Migrations](https://tenancyforlaravel.com/docs/v3/migrations/):
    tenant migrations move to `database/migrations/tenant`, run by `php artisan tenants:migrate`, and the
    paths are set in `migration_parameters` of `config/tenancy.php`.
[^central]: Tenancy for Laravel — [Installation](https://tenancyforlaravel.com/docs/v3/installation/): a
    separate central connection is configured in `config/database.php` under the name
    `tenancy.central_connection` expects.
[^pipeline]: Tenancy for Laravel — [Event system](https://tenancyforlaravel.com/docs/v3/event-system/):
    `JobPipeline::make([CreateDatabase::class, MigrateDatabase::class, …])` sent from `TenantCreated`,
    synchronous unless `shouldBeQueued(true)`.
[^queue]: Tenancy for Laravel — [Tenancy bootstrappers](https://tenancyforlaravel.com/docs/v3/tenancy-bootstrappers/):
    `QueueTenancyBootstrapper` adds the tenant's id to a queued job and initialises tenancy from it when the
    job runs.
[^manual]: Tenancy for Laravel — [Manual initialization](https://tenancyforlaravel.com/docs/v3/manual-initialization/):
    `tenancy()->initialize($tenant)` enters a tenant outside middleware.
