#!/bin/sh
set -e

# Ensure Laravel's storage scaffolding exists FIRST. `optimize` below writes to
# storage/framework + storage/logs, and a fresh mounted storage volume starts empty
# — without this, `optimize` would crash the boot. Idempotent, safe on every start.
php artisan app:ensure-storage

# Cache config/routes/events/views using the env Coolify injects at runtime.
# (Caching at build time is impossible — the real env isn't present then.)
php artisan optimize

# Link  public/storage  →  storage/app/public  so files written to the "public"
# disk (uploads, etc.) are reachable at the /storage/... URL. --force recreates an
# existing link; output is silenced and failure ignored (|| true) because it's a
# harmless no-op when already linked or when uploads live on S3.
php artisan storage:link --force >/dev/null 2>&1 || true

# (No database wait: the DB is an external, always-on service — it isn't built into
#  this image or co-booted with the container, so there's nothing to wait for.)

# Migrations are NOT run here (would run on every restart + race across replicas).
# They live in docker/deploy/post-deployment.sh — Coolify Post-deployment Command:
#   sh /var/www/html/docker/deploy/post-deployment.sh

exec "$@"
