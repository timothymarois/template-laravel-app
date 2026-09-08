#!/bin/sh
set -e

# ── POST-deployment (Coolify "Post-deployment Command") ──────────────────────
# Runs in the NEW container, with the NEW code, AFTER the build succeeds and the
# new container is live. This is the home for migrations + one-time release
# tasks. Wire it once in Coolify:
#
#   sh /var/www/html/docker/deploy/post-deployment.sh
#
# The build (composer install + pnpm build-ssr) is a HARD GATE before this ever
# runs — a failed build aborts the deploy and this never executes.
#
# NOTE: the new container is already serving when this runs, so keep migrations
# backward-compatible (expand/contract) — additive changes are safe; split
# destructive ones (drop/rename/NOT NULL) across two deploys.

php artisan migrate --force


# Storage scaffolding (app:ensure-storage) is handled in entrypoint.sh, which runs
# BEFORE this — so storage/framework + storage/logs already exist for migrate.
# Other one-time release tasks can go below, e.g.:
# php artisan up        # if pre-deployment.sh put the app into maintenance mode
