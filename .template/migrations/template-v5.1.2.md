# Migrating a fork to template v5.1.2

v5.1.2 extracts the deploy `Dockerfile`'s PHP-FPM **base stage** into a pre-built,
published image — **`ghcr.io/timothymarois/docker-laravel-base`**
([repo](https://github.com/timothymarois/docker-laravel-base)) — and `FROM`s it
instead of compiling the PHP extensions on every build. `Docker:` patch — no
schema, API, or app-runtime change; the produced container is byte-equivalent.
Deploy-time speed + cache-stability only.

> **Why:** the `base` stage (apt + compiling `gd`/`pdo_pgsql`/`pdo_mysql`/`redis`/`pcntl`/… + composer) is the slowest, least-changing part of the build. Coolify's periodic Docker cleanup prunes the local layer cache, so that stage was recompiled cold (~minutes) on many deploys. A **named, pre-built** base runs the compile once in CI, pulls in seconds, and is never evicted by `docker image prune`.

## Prerequisites

- Fork is on template **v5.1.1**. Check: `jq -r .version template-manifest.json` → `5.1.1`.
- Fork uses **PHP 8.4** (`docker.php` = `8.4`). 8.3 forks: stay on the inline base until an `:8.3-vN` base tag is published, or move to 8.4 first.
- The base image is public — no auth needed:
  `docker manifest inspect ghcr.io/timothymarois/docker-laravel-base:8.4-v1`.

---

## Part A — Swap the base stage (every fork on Coolify)

In the root `Dockerfile`, replace the `ARG PHP_VERSION` line **and** the entire
`# 1. Base` stage (the `FROM php:...-fpm-bookworm AS base`, the
`RUN apt-get … docker-php-ext-install … pecl install redis …` block, and the
`COPY --from=composer:2 …` line) with a single `FROM`:

```diff
-ARG PHP_VERSION=8.4
-
-###############################################################################
-# 1. Base — PHP-FPM with the full extension set
-###############################################################################
-FROM php:${PHP_VERSION}-fpm-bookworm AS base
-
-RUN apt-get update && apt-get install -y --no-install-recommends \
-        git unzip ca-certificates curl wget nginx supervisor \
-        libzip-dev libicu-dev libpng-dev libjpeg62-turbo-dev libfreetype6-dev \
-        libonig-dev libpq-dev libgmp-dev \
-    && docker-php-ext-configure gd --with-freetype --with-jpeg \
-    && docker-php-ext-install -j"$(nproc)" \
-        pdo_mysql pdo_pgsql bcmath intl zip gd pcntl opcache sockets exif gmp \
-    && pecl install redis \
-    && docker-php-ext-enable redis \
-    && apt-get clean \
-    && rm -rf /var/lib/apt/lists/*
-
-COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
+###############################################################################
+# 1. Base — pre-built PHP-FPM image (docker-laravel-base)
+###############################################################################
+FROM ghcr.io/timothymarois/docker-laravel-base:8.4-v1 AS base
```

Leave the `build` and `runtime` stages (`FROM base …`) **untouched** — they still
install Node/pnpm and run composer/pnpm as before; composer + the PHP extensions
now come from the base image.

If your fork had customized the base stage's extension list, that customization
must move into the [`docker-laravel-base`](https://github.com/timothymarois/docker-laravel-base)
repo (as a new tag) — **never** re-add an apt/extension block here.

---

## Part B — Record the base pin in the manifest (every fork)

Add `baseImage` to the `docker` block of `template-manifest.json`:

```diff
     "docker": {
         "php": "8.4",
         "pkg": "pnpm",
         "build": "build-ssr",
+        "baseImage": "ghcr.io/timothymarois/docker-laravel-base:8.4-v1",
         "requires": { ... }
     }
```

---

## Verify

```sh
# the base image is public and pullable (amd64)
docker manifest inspect ghcr.io/timothymarois/docker-laravel-base:8.4-v1 >/dev/null && echo OK
```

Then **redeploy on Coolify** — the build pulls the base (seconds) instead of
compiling extensions; the app must boot and pass the `/up` health check exactly as
before.

## Finally

Bump the fork's `template-manifest.json` → `"version": "5.1.2"`. Don't copy this
changelog into the fork — the manifest `version` is the record (see the template's
[`.template/README.md`](../README.md)).
