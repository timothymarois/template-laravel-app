# syntax=docker/dockerfile:1
#
# ── Universal Laravel production image ───────────────────────────────────────
# Canonical deploy image for this template and its forks (Coolify).
# Single container; supervisord runs the processes this project enables.
#
# Coolify:  Build Pack = Dockerfile · Port = 80 · Health check = /up
# See docker/README.md → "This project's setup" (mirrors template-manifest.json)
# for what this project requires, the knobs, and the Coolify env/resource checklist.
#
# PER-PROJECT KNOBS:
#   1. Base image tag (below) — pin a docker-laravel-base version / PHP line.
#        The PHP version is set by WHICH base tag you pin (e.g. :8.4-v1), not a build arg.
#   2. Asset build command in the build stage:
#        SSR app ........ pnpm build-ssr   (default)
#        non-SSR app .... pnpm build
#        npm instead .... swap pnpm -> npm
#        no frontend .... remove the Node install + build lines
#   3. docker/config/supervisord.conf — enable only this project's processes
#        (delete unused OPTIONAL blocks; swap horizon for queue:work; add reverb)
#   4. Coolify post-deployment command (migrations) — docker/deploy/post-deployment.sh, per app
#
# The PHP-FPM base — the extension SUPERSET our apps need (MySQL AND Postgres,
# Redis, Horizon/pcntl, sockets, gd/exif, gmp) plus composer — is pre-built and
# published as ghcr.io/timothymarois/docker-laravel-base. Its source + publish CI
# live in that repo (https://github.com/timothymarois/docker-laravel-base); this
# Dockerfile just FROMs it, so the heavy extension compile runs ONCE in CI instead
# of on every Coolify deploy (and a named image survives `docker image prune`). To
# change the extension set, bump the base repo and re-pin the tag below — see
# docker/README.md.
# ─────────────────────────────────────────────────────────────────────────────

###############################################################################
# 1. Base — pre-built PHP-FPM image (docker-laravel-base)
###############################################################################
FROM ghcr.io/timothymarois/docker-laravel-base:8.4-v1 AS base

###############################################################################
# 2. Build — composer deps + client/SSR bundles
###############################################################################
FROM base AS build

# Node 22 + pnpm (Vite 7 requires Node >= 22.12)
RUN curl -fsSL https://deb.nodesource.com/setup_22.x | bash - \
    && apt-get install -y --no-install-recommends nodejs \
    && npm install -g pnpm@10.20.0 \
    && rm -rf /var/lib/apt/lists/*

WORKDIR /app

# --- Dependency layers: cached unless the lockfiles change ---
# PHP deps first, WITHOUT the autoloader (app source isn't present yet, so an
# optimized classmap here would be incomplete). --no-scripts defers
# ziggy:generate, which needs a booted app that doesn't exist yet.
COPY composer.json composer.lock ./
RUN --mount=type=cache,target=/tmp/composer-cache \
    COMPOSER_CACHE_DIR=/tmp/composer-cache \
    composer install --no-dev --no-interaction --prefer-dist --no-scripts --no-autoloader

# Node deps — pnpm store kept in a BuildKit cache mount so packages aren't
# re-downloaded across builds (survives even when the lockfile changes).
COPY package.json pnpm-lock.yaml ./
RUN --mount=type=cache,target=/pnpm-store \
    pnpm install --frozen-lockfile --store-dir=/pnpm-store

# --- Application source ---
COPY . /app

# Throwaway build-time env so artisan can boot during the build (ziggy:generate
# + package discovery). Removed below — real runtime env is injected by Coolify.
RUN cp .env.example .env \
    && composer dump-autoload --no-dev --optimize \
    && php artisan key:generate --ansi \
    && php artisan package:discover --ansi

# Client + SSR bundles (KNOB: build-ssr for SSR apps, build for non-SSR).
# Keep the FULL node_modules — the Vite SSR build externalises its imports
# (@inertiajs/vue3, @vue/server-renderer, vue, …), several of which are
# devDependencies, so they must be present at runtime. Do NOT `pnpm prune --prod`
# here — it removes the dev deps the SSR server needs.
# NODE_OPTIONS raises V8's heap so a memory-heavy Vite/Rollup build (large apps, or
# `ssr.noExternal: true` which bundles all deps) doesn't OOM with "JavaScript heap
# out of memory" (exit 134). Bump to 6144 if a build still aborts.
RUN NODE_OPTIONS=--max-old-space-size=4096 pnpm build-ssr

RUN rm -f .env

###############################################################################
# 3. Runtime — app + Node (for the SSR server) + supervisord
###############################################################################
FROM base AS runtime

# Node runtime is required to run the Inertia SSR server.
RUN curl -fsSL https://deb.nodesource.com/setup_22.x | bash - \
    && apt-get install -y --no-install-recommends nodejs \
    && rm -rf /var/lib/apt/lists/*

WORKDIR /var/www/html

# Give every process a readable HOME. supervisord runs as root (HOME=/root) and
# drops programs to www-data WITHOUT resetting HOME — so a worker's libpq would
# probe /root/.postgresql for a client cert, hit "Permission denied", fail the SSL
# handshake to managed Postgres (DO/RDS), and fall back to a rejected plaintext
# connection. php-fpm is unaffected (sane env), which is why the web works but
# horizon/scheduler/reverb can't reach the DB. Pointing HOME at the app dir
# (readable, has no .postgresql) makes that probe a harmless no-op.
ENV HOME=/var/www/html

# App with vendor/, public/build, bootstrap/ssr and pruned node_modules
COPY --from=build --chown=www-data:www-data /app /var/www/html

# Service configuration (MANAGED CORE — the template owns these)
COPY docker/config/nginx.conf          /etc/nginx/sites-available/default
COPY docker/config/nginx-snippets/     /etc/nginx/snippets/
COPY docker/config/php.ini             /usr/local/etc/php/conf.d/zz-app.ini
COPY docker/config/supervisord.conf    /etc/supervisor/conf.d/app.conf
COPY docker/deploy/entrypoint.sh       /usr/local/bin/entrypoint
RUN chmod +x /usr/local/bin/entrypoint

# Project configuration (KNOB — this fork owns these; empty by default).
# nginx: http-context files (shared-memory zones, maps) and server-context files
# (locations needing their own client_max_body_size). A wildcard include that
# matches nothing is not an error, so an empty directory changes nothing.
# php:   loaded from conf.d AFTER zz-app.ini, so a `zzz-`-prefixed project file
#        wins on any directive it repeats. PHP scans conf.d in filename order.
# See docker/README.md -> "Project configuration".
COPY docker/project/nginx/http/        /etc/nginx/project/http/
COPY docker/project/nginx/server/      /etc/nginx/project/server/
COPY docker/project/php/               /usr/local/etc/php/conf.d/

EXPOSE 80

ENTRYPOINT ["/usr/local/bin/entrypoint"]
CMD ["/usr/bin/supervisord", "-n", "-c", "/etc/supervisor/supervisord.conf"]
