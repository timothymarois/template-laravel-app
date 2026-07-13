# Brief - template-laravel-app

## Story

template-laravel-app is a Laravel starter template for new product builds and template-managed forks. It provides a modern, opinionated Laravel + Vue/Inertia + Tailwind foundation — auth, admin, a shadcn-vue component library, testing, docs, health checks, and deployment scaffolding — so a new product can start from a consistent, production-shaped base instead of a bare framework install. A rewrite of earlier hand-rolled starters, it exists to make every downstream fork share the same conventions and upgrade path, so improvements flow outward and forks don't drift.

## Users / ICP

- The template's owner and collaborating agents building new products from a known-good base.
- Downstream product repos (forks) that mirror the template and periodically pull in upgrades.
- What matters: consistent UI/UX, backend/frontend parity, a strict-typed and well-tested baseline, and a predictable path to keep a fork current with the template.

## Scope

- **Active areas:** the Laravel app itself — auth, admin dashboard/users, the Vue/Inertia UI and shadcn-vue component showcase, health checks, testing, and the docs site. Multi-tenancy ships but is inert by default.
- **Out of scope:** product-specific features (each fork owns those), and any cross-fork bookkeeping (which fork runs which template version).
- **Reference:** the app deploys on the sibling `docker-laravel-base` PHP-FPM image (published to GHCR); template Docker-base work happens in that separate repo.

## External Systems

- `GHCR` — hosts the `docker-laravel-base` PHP-FPM image the app runs on.
- `Sentry` — error and exception monitoring.
- `Discord` — destination for health-check / maintenance notifications.
- `Redis` — queues (Horizon) and cache.
- `S3 (flysystem)` — object storage for uploads.
