#!/bin/sh
set -e

# Wait for the database before booting. Service providers (e.g. stancl/tenancy)
# can touch the DB during `optimize`, and on a cold deploy the DB container may
# not be up yet — without this, `set -e` would kill the container in a crash loop.
echo "Waiting for database..."
tries=0
until php artisan db:show >/dev/null 2>&1; do
    tries=$((tries + 1))
    if [ "$tries" -ge 30 ]; then
        echo "Database still unreachable after ~60s — continuing anyway."
        break
    fi
    echo "  not ready, retry ${tries}/30..."
    sleep 2
done

# Cache config/routes/events/views using the env Coolify injects at runtime.
# (Caching at build time is impossible — the real env isn't present then.)
php artisan optimize

# Ensure the public storage symlink exists (no-op if already linked / on S3).
php artisan storage:link --force >/dev/null 2>&1 || true

# Database migrations are NOT run here (would run on every restart + race across
# replicas). They live in docker/deploy.sh — run once per deploy via Coolify's
# pre-deployment command:  sh /var/www/html/docker/deploy.sh

exec "$@"
