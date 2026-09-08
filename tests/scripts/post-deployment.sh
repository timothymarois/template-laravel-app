#!/usr/bin/env bash
#
# Contract test for the deployment scripts the production image ships.
#
# Nothing in the application suite runs these — they execute in the container,
# wired into the host's deploy hooks. A migration line quietly lost or commented
# out is invisible until a release ships schema changes that never applied.

set -euo pipefail

repository_root="$(git rev-parse --show-toplevel)"
post_deployment="$repository_root/docker/deploy/post-deployment.sh"
entrypoint="$repository_root/docker/deploy/entrypoint.sh"
dockerfile="$repository_root/Dockerfile"

fail() {
    echo "post-deployment: $1" >&2
    exit 1
}

[[ -f "$post_deployment" ]] || fail "docker/deploy/post-deployment.sh is missing — migrations would never run on deploy."
[[ -f "$entrypoint" ]]      || fail "docker/deploy/entrypoint.sh is missing — the container has nothing to start."

# --- migrations run, exactly once, and are not commented out ----------------
#
# `grep -c` exits non-zero on no match, which under `set -e` would abort with no
# diagnostic — the assertion below must be the thing that reports the failure.
migrate_count="$(grep -cE '^[[:space:]]*php artisan migrate --force[[:space:]]*$' "$post_deployment" || true)"

[[ "$migrate_count" -eq 1 ]] || {
    if [[ "$migrate_count" -eq 0 ]]; then
        commented="$(grep -cE '^[[:space:]]*#[[:space:]]*php artisan migrate --force' "$post_deployment" || true)"
        if [[ "$commented" -gt 0 ]]; then
            fail "'php artisan migrate --force' is commented out in post-deployment.sh — schema changes would ship without applying."
        fi
        fail "post-deployment.sh does not run 'php artisan migrate --force' — schema changes would ship without applying."
    fi
    fail "post-deployment.sh runs 'php artisan migrate --force' $migrate_count times; it must run exactly once."
}

# --- the entrypoint still reaches the image ---------------------------------
grep -qE '^[[:space:]]*COPY[[:space:]]+docker/deploy/entrypoint\.sh' "$dockerfile" \
    || fail "the Dockerfile no longer copies docker/deploy/entrypoint.sh — the container would start without it."

grep -qE 'chmod \+x /usr/local/bin/entrypoint' "$dockerfile" \
    || fail "the Dockerfile no longer makes the entrypoint executable — the container would fail to start."

grep -qE '^ENTRYPOINT[[:space:]]*\[' "$dockerfile" \
    || fail "the Dockerfile has no ENTRYPOINT."

echo "Deployment script checks passed."
