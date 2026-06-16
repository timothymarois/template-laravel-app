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
#   1. PHP_VERSION build arg (below) — default 8.4
#   2. Asset build command in the build stage:
#        SSR app ........ pnpm build-ssr   (default)
#        non-SSR app .... pnpm build
#        npm instead .... swap pnpm -> npm
#        no frontend .... remove the Node install + build lines
#   3. docker/config/supervisord.conf — enable only this project's processes
#        (delete unused OPTIONAL blocks; swap horizon for queue:work; add reverb)
#   4. Coolify post-deployment command (migrations) — docker/deploy/post-deployment.sh, per app
#
# The extension set is the SUPERSET our apps need — MySQL AND Postgres, Redis,
# Horizon (pcntl), atlas-php/spatie-fork (sockets), image handling (gd/exif),
# gmp — so forks don't hit missing-extension builds.
# ─────────────────────────────────────────────────────────────────────────────

ARG PHP_VERSION=8.4

###############################################################################
# 1. Base — PHP-FPM with the full extension set
###############################################################################
FROM php:${PHP_VERSION}-fpm-bookworm AS base

RUN apt-get update && apt-get install -y --no-install-recommends \
        git unzip ca-certificates curl wget nginx supervisor \
        libzip-dev libicu-dev libpng-dev libjpeg62-turbo-dev libfreetype6-dev \
        libonig-dev libpq-dev libgmp-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j"$(nproc)" \
        pdo_mysql pdo_pgsql bcmath intl zip gd pcntl opcache sockets exif gmp \
    && pecl install redis \
    && docker-php-ext-enable redis \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

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
RUN composer install --no-dev --no-interaction --prefer-dist --no-scripts --no-autoloader

# Node deps
COPY package.json pnpm-lock.yaml ./
RUN pnpm install --frozen-lockfile

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
RUN pnpm build-ssr

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

# Service configuration
COPY docker/config/nginx.conf       /etc/nginx/sites-available/default
COPY docker/config/php.ini          /usr/local/etc/php/conf.d/zz-app.ini
COPY docker/config/supervisord.conf /etc/supervisor/conf.d/app.conf
COPY docker/deploy/entrypoint.sh    /usr/local/bin/entrypoint
RUN chmod +x /usr/local/bin/entrypoint

EXPOSE 80

ENTRYPOINT ["/usr/local/bin/entrypoint"]
CMD ["/usr/bin/supervisord", "-n", "-c", "/etc/supervisor/supervisord.conf"]
