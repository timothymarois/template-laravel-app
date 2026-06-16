#!/bin/sh
set -e

# ── PRE-deployment (Coolify "Pre-deployment Command") ────────────────────────
# Runs in the CURRENT/OLD container, with the OLD code, BEFORE the new image is
# deployed. Wire it once in Coolify (it's a harmless no-op until you fill it in):
#
#   sh /var/www/html/docker/deploy/pre-deployment.sh
#
# Because it runs the OLD code, this is NOT where migrations go — the new
# migrations don't exist in the old container yet. Those live in
# docker/post-deployment.sh. Use this phase only for "before the swap" tasks
# against the still-live app, e.g.:
#
#   php artisan down        # maintenance mode for a risky deploy
#                           # (bring it back with `php artisan up` at the end
#                           #  of post-deployment.sh)
#
# Empty by default — kept wired so every fork always has the hook ready.
exit 0
