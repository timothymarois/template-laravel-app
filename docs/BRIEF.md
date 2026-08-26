# Brief — template-laravel-app

*What this is and why it exists. One screen, stable, and free of private detail.*

## Story

template-laravel-app is an opinionated Laravel starter template for new product builds and
template-managed forks. It ships a production-shaped foundation — Laravel + Vue/Inertia + Tailwind, with
auth, an admin dashboard, a shadcn-vue component library, testing, health checks, and deployment
scaffolding — so a new product starts from a consistent base instead of a bare framework install. A
rewrite of earlier hand-rolled starters, it exists so every downstream fork shares the same conventions
and upgrade path.

## Why it exists

Starting each product from a bare framework, or copying a previous project by hand, makes every repo
drift into its own conventions and go stale in isolation. This template is the shared, known-good base:
improvements made here flow outward to the forks, and a fork has a predictable path to pull upgrades in
rather than diverging. It resolves "how should this be structured?" once, so downstream products inherit
the answer.

## Users

- The template's owner and collaborating agents building new products from a known-good base.
- Downstream product repos (forks) that mirror the template and periodically pull in upgrades.
- What matters most: consistent UI/UX, backend/frontend parity, a strict-typed and well-tested baseline,
  and a predictable path to keep a fork current with the template.

## Scope

- **Covers:** the Laravel app itself — auth, the admin dashboard and user management, the Vue/Inertia UI
  and shadcn-vue component showcase, health checks, the release and versioning process, and testing.
  Multi-tenancy ships but is inert by default.
- **Refuses:** product-specific features (each fork owns those), and any cross-fork bookkeeping
  (which fork runs which template version — that lives in the workspace tracker).
- **Reference:** the app deploys on the sibling `docker-laravel-base` PHP-FPM image (published to GHCR);
  template Docker-base work happens in that separate repo.

## External systems

- `GHCR` — hosts the `docker-laravel-base` PHP-FPM image the app runs on.
- `Sentry` — error and exception monitoring.
- `Discord` — destination for health-check / maintenance notifications.
- `Redis` — queues (Horizon) and cache.
- `S3 (flysystem)` — object storage for uploads.
