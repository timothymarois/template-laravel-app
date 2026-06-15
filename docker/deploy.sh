#!/bin/sh
set -e

# Release script — runs ONCE per deploy, AFTER the image is built, against the
# live database. Wire it to Coolify's pre-deployment command:
#
#   sh /var/www/html/docker/deploy.sh
#
# This is the home for migrations and one-time release tasks. They must NOT go
# in the Dockerfile (no DB at build time) or in entrypoint.sh (which runs on
# every container start and would race across replicas).

php artisan migrate --force

# Multi-tenant apps also migrate tenant databases:
# php artisan tenants:migrate --force

# Other one-time release tasks can go here, e.g.:
# php artisan app:ensure-storage
