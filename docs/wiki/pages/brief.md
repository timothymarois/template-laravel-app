+++
title = "Brief"
subtitle = "purpose, reasoning, users, scope and outside systems"
status = "approved"
goals = false
intent = """
The brief exists so that a reader arriving at template-laravel-app, person or agent, learns in one screen
what it is, who it is for and what it refuses. It stays short and stable, and names nothing private.
"""

[[infobox]]
group = "Identity"
rows = [
  { label = "Name", value = "template-laravel-app", cite = "manifest" },
  { label = "Repository", value = "github.com/timothymarois/template-laravel-app", link = "https://github.com/timothymarois/template-laravel-app", cite = "manifest" },
  { label = "Stack", value = "Laravel 13, Vue 3, Inertia, Tailwind 4", cite = "stack" },
]

[[infobox]]
group = "Rules"
rows = [
  { label = "Forks", value = "track one template version", cite = "manifest" },
  { label = "Product features", value = "refused", missing = true },
]
+++

## Purpose

template-laravel-app is the starter a product forks from instead of a bare framework install: a Laravel
application with Vue and Inertia already wired, shipping [accounts](accounts.md), an
[admin area](admin.md), [API keys](api-keys.md), [health checks](health.md), a
[release process](releases.md), a [deployment](deployment.md) image and a [component kit](components.md).
The backend is Laravel 13 and the interface Vue 3 with Inertia and Tailwind 4.[^stack] Every fork records which version of the template it is aligned with, and pulls later versions in through
the template's own migration guides.[^manifest]

## Reasoning

Starting each product from a bare framework, or copying a previous project by hand, lets every repository
drift into its own conventions.{missing} The template resolves how an application is structured once, so
that improvements made here reach every fork, and a fork has a predictable path to pull an upgrade in
rather than diverging.[^manifest]

## Users

- **The template's owner and collaborating agents**, building new products from a known-good base.{missing}
- **Downstream product repositories**, the forks, which mirror the template and periodically pull upgrades
  in.[^manifest]
- What matters most to them: consistent interface behaviour, backend and frontend parity, a strictly typed
  and well-tested baseline, and a predictable path to keep a fork current.{missing}

## Scope

- **Covers:** the application itself: registration and sign-in, the admin area with user management, the
  Vue and Inertia interface with its component showcase, API keys, health checks, the release process
  and the production image.[^scope] Multi-tenancy is not shipped; a fork adds it by following
  [Multi-tenancy](setup/tenancy.md).
- **Refuses:** product-specific features, which each fork owns, and any cross-fork bookkeeping, such as
  which fork runs which template version.{missing}

## External systems

- **GHCR** hosts the PHP-FPM base image the production image is built from.[^image]
- **Sentry** receives exceptions when its DSN is set.[^sentry]
- **Discord** receives health notifications through a webhook when one is set.[^discord]
- **Redis** backs the queue, the cache and Horizon.[^redis]
- **S3-compatible object storage** holds uploads through the `s3` disk.[^s3]
- **GitHub Actions** runs the checks on every push and pull request.[^actions]

[^manifest]: `template-manifest.json` — `template`, `repo` and `version` record the template's name, its
    repository and the version a checkout is aligned with; `.template/migrations/` — one upgrade guide per
    release, named `template-vX.Y.Z.md`.
[^stack]: `composer.json` — `require` pins `laravel/framework` `^13.0` and `inertiajs/inertia-laravel`;
    `package.json` — `dependencies` pin `vue` `^3.5`, `@inertiajs/vue3` and `tailwindcss` `^4`.
[^scope]: `routes/web.php` — the `guest`, `auth:sanctum` and `admin` route groups; `routes/api.php` — the
    API-key chain; `routes/components.php` — the showcase; `routes/web.php` — the `health` and `release`
    routes; `scripts/publish-production-release` — the release flow; `Dockerfile` — the image.
[^image]: `Dockerfile` — `FROM ghcr.io/timothymarois/docker-laravel-base:8.4-v1 AS base`.
[^sentry]: `config/sentry.php` — `dsn` reads `SENTRY_LARAVEL_DSN`; `composer.json` — `require` lists
    `sentry/sentry-laravel`.
[^discord]: `config/health.php` — `notifications.discord.webhook_url` reads `HEALTH_DISCORD_WEBHOOK_URL`,
    and the `discord` channel is selected when it is set.
[^redis]: `config/queue.php` — `default` reads `QUEUE_CONNECTION` with `redis` as its default;
    `composer.json` — `require` lists `laravel/horizon`.
[^s3]: `config/filesystems.php` — the `s3` disk with driver `s3`; `composer.json` — `require` lists
    `league/flysystem-aws-s3-v3`.
[^actions]: `.github/workflows/php-checks.yml`, `js-checks.yml`, `docker-config.yml` and `wiki.yml` — each
    runs `on` a push or a pull request.
