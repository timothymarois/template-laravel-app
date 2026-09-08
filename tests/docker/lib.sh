#!/usr/bin/env bash
# Shared helpers for the built-image configuration proofs.
#
# Every assertion here reads the EFFECTIVE configuration of a running container
# (`nginx -T`, `ini_get`, a real request) rather than the source files, so a
# file copied to the wrong path, included in the wrong context, duplicated, or
# shadowed fails instead of passing.

set -euo pipefail

FAILURES=0

pass() { printf '  \033[32mok\033[0m   %s\n' "$1"; }
fail() { printf '  \033[31mFAIL\033[0m %s\n' "$1"; FAILURES=$((FAILURES + 1)); }

# assert_eq <actual> <expected> <description>
assert_eq() {
    if [ "$1" = "$2" ]; then pass "$3"; else fail "$3 — expected '$2', got '$1'"; fi
}

# assert_contains <haystack> <needle> <description>
assert_contains() {
    if printf '%s' "$1" | grep -qF -- "$2"; then pass "$3"; else fail "$3 — '$2' not found"; fi
}

finish() {
    if [ "$FAILURES" -ne 0 ]; then
        printf '\n\033[31m%s assertion(s) failed\033[0m\n' "$FAILURES"
        exit 1
    fi
    printf '\n\033[32mAll assertions passed\033[0m\n'
}

# ── Container lifecycle ──────────────────────────────────────────────────────
# The image's ENTRYPOINT runs `php artisan optimize`, which needs runtime env
# this proof deliberately does not supply. nginx and php-fpm are started
# directly instead: every assertion below is about the web tier's configuration,
# which is settled before any application code runs.
CONTAINER=""

start_container() {
    CONTAINER=$(docker run -d --rm --entrypoint sh "$1" \
        -c 'php-fpm -D && nginx -g "daemon off;"')
    trap 'docker rm -f "$CONTAINER" >/dev/null 2>&1 || true' EXIT

    for _ in $(seq 1 30); do
        if docker exec "$CONTAINER" sh -c 'nginx -t' >/dev/null 2>&1; then return 0; fi
        sleep 1
    done
    echo "container did not become ready" >&2
    docker logs "$CONTAINER" >&2 || true
    exit 1
}

# in_container <shell command>
in_container() { docker exec "$CONTAINER" sh -c "$1"; }

# effective_nginx — the fully resolved configuration, every include expanded.
effective_nginx() { in_container 'nginx -T 2>/dev/null'; }

# php_ini <directive> — what PHP-FPM's own SAPI actually resolved, not the file.
php_ini() { in_container "php -r 'echo ini_get(\"$1\");'"; }

# post_status <uri> <megabytes> — HTTP status for a real body of that size.
# Proves where the body ceiling actually bites: 413 is nginx refusing before
# PHP, anything else means nginx accepted and passed the request on.
post_status() {
    in_container "head -c $((${2} * 1024 * 1024)) /dev/zero > /tmp/body.bin && \
        curl -s -o /dev/null -w '%{http_code}' -X POST \
        --data-binary @/tmp/body.bin \
        -H 'Content-Type: application/octet-stream' \
        'http://127.0.0.1${1}'"
}

# count_directive <directive> — occurrences in the effective configuration.
count_directive() { effective_nginx | grep -cE "^[[:space:]]*${1}[[:space:]]" || true; }
