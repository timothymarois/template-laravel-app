#!/usr/bin/env bash
# A fork that supplies NO project configuration must get byte-identical
# behavior to before this capability existed. This is the regression half of
# the proof: the wildcard includes resolve to nothing and change nothing.

set -euo pipefail
cd "$(dirname "$0")"
# shellcheck source=tests/docker/lib.sh
. ./lib.sh

IMAGE="${1:?usage: verify-default.sh <image>}"
echo "== Default image: $IMAGE"
start_container "$IMAGE"

# --- Syntax -----------------------------------------------------------------
if in_container 'nginx -t' >/dev/null 2>&1; then
    pass "nginx -t passes"
else
    fail "nginx -t passes"
    in_container 'nginx -t' || true
fi

NGINX="$(effective_nginx)"

# --- Wiring: the includes exist and are in the intended context --------------
assert_contains "$NGINX" 'include /etc/nginx/project/http/*.conf;' \
    "http-context project include is present"
assert_contains "$NGINX" 'include /etc/nginx/project/server/*.conf;' \
    "server-context project include is present"

# The http include must sit OUTSIDE `server {` and the server include INSIDE it.
# Reading the line numbers out of the effective config is what makes this a
# context assertion rather than a "the string is somewhere" assertion.
CTX=$(printf '%s' "$NGINX" | awk '
    BEGIN            { depth = 0; http = "none"; srv = "none" }
    # `nginx -T` prints each config file under its own banner; block depth is
    # per file, so reset at every banner rather than carrying it across.
    /^# configuration file/            { depth = 0; next }
    /project\/http\/\*\.conf;/       { http = depth }
    /project\/server\/\*\.conf;/     { srv  = depth }
    /^[[:space:]]*server[[:space:]]*\{/ { depth++; next }
    /^\}[[:space:]]*$/                  { if (depth > 0) depth-- ; next }
    END { print http "/" srv }
')
assert_eq "$CTX" "0/1" "http include is at http context, server include inside server{}"

# --- Snippets shipped where the docs say they are ---------------------------
if in_container 'test -f /etc/nginx/snippets/laravel-fastcgi.conf'; then
    pass "laravel-fastcgi.conf snippet is installed"
else
    fail "laravel-fastcgi.conf snippet is installed"
fi
if in_container 'test -f /etc/nginx/snippets/laravel-front-controller.conf'; then
    pass "laravel-front-controller.conf snippet is installed"
else
    fail "laravel-front-controller.conf snippet is installed"
fi

# --- The default ceiling is unchanged and UNIQUE ----------------------------
# A second client_max_body_size anywhere in the effective config means the
# default is no longer the only answer to "how big may a body be here".
assert_eq "$(count_directive client_max_body_size)" "1" \
    "exactly one client_max_body_size in the effective configuration"
assert_contains "$NGINX" "client_max_body_size 25M;" "the default ceiling is still 25M"

# --- PHP limits are the template's ------------------------------------------
assert_eq "$(php_ini upload_max_filesize)" "25M" "upload_max_filesize is the template default"
assert_eq "$(php_ini post_max_size)" "25M" "post_max_size is the template default"

# --- Effective behavior: an oversized body is refused before PHP ------------
assert_eq "$(post_status / 30)" "413" "a 30M body to / is refused with 413"

finish
