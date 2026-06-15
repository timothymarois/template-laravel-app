#!/bin/sh
set -e

# SIMPLE variant entrypoint — DB-less site. No database wait, no migrations.
# To use: copy this over docker/entrypoint.sh (and docker/simple/supervisord.conf
# over docker/supervisord.conf), then delete docker/simple/.
# Set SESSION_DRIVER=file and CACHE_STORE=file in Coolify env.

# Cache config/routes/events/views using the env Coolify injects at runtime.
php artisan optimize

# Ensure the public storage symlink exists (no-op if already linked / on S3).
php artisan storage:link --force >/dev/null 2>&1 || true

exec "$@"
