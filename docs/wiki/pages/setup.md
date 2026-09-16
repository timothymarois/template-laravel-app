+++
title = "Setup"
subtitle = "a new project from the template, its deploy block and its first operator"
status = "approved"
goals = false
intent = """
Setup exists so that a product starts from the template as its own project, with its own name and
deployment target and an operator who can open the admin area, instead of as a copy of the starter. The
release scripts refuse to run until the deployment target is filled in.
"""

[[infobox]]
group = "Identity"
rows = [
  { label = "Version record", value = "template-manifest.json", cite = "manifest" },
  { label = "First operator", value = "php artisan user:create --admin", cite = "operator" },
]

[[infobox]]
group = "Rules"
rows = [
  { label = "Deploy block", value = "filled in before the first release", cite = "placeholder" },
  { label = "Seeded operator", value = "admin@example.com", cite = "seeder" },
]
+++

A new project keeps `template-manifest.json`, which records the template version it forked from, and
removes the template's own machinery: the `.template` folder with its changelog and migration
guides.{missing} A fork later pulls a newer template version in by following those guides from a fresh
clone of the template.[^manifest] Adding workspaces to a product is described on
[Multi-tenancy](setup/tenancy.md), and publishing these pages on [Documentation site](setup/documentation-site.md).

## Identity

The project is renamed in its README, in `composer.json` and `package.json`, and in the application
name.{missing} The `version` kept at `0.0.0` on `main` is described on
[Reconciliation](releases/04-reconciliation.md).

## Deployment target

The `deploy` block of `template-manifest.json` names the GitHub repository, the deployed site's address
and the branch that deploys, and the template ships it as below.[^deploy]

```json
"deploy": {
    "repository": "owner/repo",
    "productionUrl": "https://example.com",
    "productionBranch": "production"
}
```

The publishing script refuses to run while the block still holds the shipped placeholders, `owner/repo`
and `https://example.com`, rather than poll a site that does not exist.[^placeholder] Cutting a release
once it is filled in is described on [Releases](releases.md).

## First operator

The admin area opens only to an account with the operator role, so a fresh database has no way in until
an operator exists.[^gate] The seeder and the command each make one.[^seeder]

| Path | Result |
|---|---|
| `php artisan db:seed` | an operator `admin@example.com` and a plain user `test@example.com`, both with the password `password`[^seeder] |
| `php artisan user:create --admin` | an operator with a name and address of your choosing, and a password printed once[^operator] |

The seeded accounts are for a development database; a real deployment creates its operator with the
command, described on [user:create](commands/user-create.md).{missing} A development database is emptied
and rebuilt with [start:fresh](commands/start-fresh.md), which refuses to run in production.[^fresh]

## Documentation

`pnpm check` runs the wiki check with the other suites through `scripts/dev-wiki.sh`, which starts
wiki-builder with `uvx`, so `pnpm check` needs `uv` on the machine.[^uv]

[^uv]: `package.json` — `check` runs `pnpm check:wiki` with the other suites, and `check:wiki` runs
    `./scripts/dev-wiki.sh check`; `scripts/dev-wiki.sh` — runs
    `uvx --from "git+https://github.com/timothymarois/wiki-builder@$WIKI_VERSION" wiki` with
    `WIKI_VERSION=v0.6.0` and `--root`.
[^manifest]: `template-manifest.json` — `version` and `repo`; `.template/migrations/` — one guide per
    release, applied in order by a fork.
[^deploy]: `template-manifest.json` — `deploy.repository`, `deploy.productionUrl` and
    `deploy.productionBranch`; `scripts/publish-production-release` — `manifest_deploy()` reads the three.
[^placeholder]: `scripts/publish-production-release` — `manifest_deploy()` raises
    `deploy.{key} is still the template placeholder ({value})` for `owner/repo` and `https://example.com`.
[^gate]: `app/Http/Middleware/EnsureUserIsAdmin.php` — `handle()` aborts with 403 unless the account's
    role `canAccessAdmin()`; `app/Enums/UserRole.php` — only `SuperAdmin` can.
[^seeder]: `database/seeders/DatabaseSeeder.php` — `run()` creates `admin@example.com` with
    `UserRole::SuperAdmin` and `test@example.com`; `database/factories/UserFactory.php` — `definition()`
    hashes the password `password`.
[^operator]: `app/Console/Commands/CreateUser.php` — `handle()` creates the account as `SuperAdmin` with
    `--admin` and prints a generated password once.
[^fresh]: `app/Console/Commands/StartFresh.php` — `handle()` refuses with
    `Can not execute this command in production!`, then clears the caches and runs `migrate:fresh`.
