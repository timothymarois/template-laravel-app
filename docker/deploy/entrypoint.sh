#!/bin/sh
set -e

# Ensure Laravel's storage scaffolding exists FIRST. `optimize` below (and the
# post-deploy migrations, which run later) write to storage/framework + storage/logs,
# and a fresh mounted storage volume starts empty — without this, `optimize` would
# crash the boot. Idempotent, needs no DB, safe on every container start.
php artisan app:ensure-storage

# Wait for the database before booting — ONLY if this project uses one.
# Service providers (e.g. stancl/tenancy) can touch the DB during `optimize`, and
# on a cold deploy the DB container may not be up yet — without this wait, `set -e`
# would kill the container in a crash loop. DB-less projects set WAIT_FOR_DB=false
# (see the docker README "This project's setup").
if [ "${WAIT_FOR_DB:-true}" = "true" ]; then
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
fi

# Cache config/routes/events/views using the env Coolify injects at runtime.
# (Caching at build time is impossible — the real env isn't present then.)
php artisan optimize

# Ensure the public storage symlink exists (no-op if already linked / on S3).
php artisan storage:link --force >/dev/null 2>&1 || true

# Migrations are NOT run here (would run on every restart + race across replicas).
# They live in docker/deploy/post-deployment.sh — run once per deploy via Coolify's
# Post-deployment Command:  sh /var/www/html/docker/deploy/post-deployment.sh

exec "$@"
