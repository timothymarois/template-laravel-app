#!/usr/bin/env bash
# The capability half of the proof: a project's files are copied, loaded in the
# intended context, syntactically valid, unique, wired to the front controller,
# and EFFECTIVE — while the template's own default stays exactly where it was
# for every other route.

set -euo pipefail
cd "$(dirname "$0")"
# shellcheck source=tests/docker/lib.sh
. ./lib.sh

IMAGE="${1:?usage: verify-project.sh <image>}"
FIXTURE_URI="/__template-upload-fixture"
echo "== Project-configured image: $IMAGE"
start_container "$IMAGE"

# --- Syntax: an invalid project file must fail the build, not the request ----
if in_container 'nginx -t' >/dev/null 2>&1; then
    pass "nginx -t passes with project configuration loaded"
else
    fail "nginx -t passes with project configuration loaded"
    in_container 'nginx -t' || true
fi

NGINX="$(effective_nginx)"

# --- The project files actually reached the image ---------------------------
assert_eq "$(in_container 'ls /etc/nginx/project/http/*.conf | wc -l' | tr -d ' ')" "1" \
    "the http-context project file was copied into the image"
assert_eq "$(in_container 'ls /etc/nginx/project/server/*.conf | wc -l' | tr -d ' ')" "1" \
    "the server-context project file was copied into the image"

# --- ...and were LOADED, not merely present ---------------------------------
# `nginx -T` prints only what the running configuration actually includes, so a
# file copied to a path nothing includes fails here even though it exists.
assert_contains "$NGINX" "location = ${FIXTURE_URI}" "the project location is in the effective configuration"
assert_contains "$NGINX" "limit_req_zone" "the project http-context zone is in the effective configuration"

# --- Uniqueness: loaded exactly once ----------------------------------------
# A file included from two contexts, or a directory copied twice, yields a
# duplicate that nginx may accept and that silently doubles a shared-memory
# zone or makes "which ceiling wins" unanswerable.
assert_eq "$(printf '%s' "$NGINX" | grep -cF "location = ${FIXTURE_URI}")" "1" \
    "the project location appears exactly once"
assert_eq "$(count_directive limit_req_zone)" "1" "limit_req_zone is declared exactly once"
assert_eq "$(count_directive limit_conn_zone)" "1" "limit_conn_zone is declared exactly once"

# --- Context: the elevated ceiling is scoped, the default is not moved ------
assert_eq "$(count_directive client_max_body_size)" "2" \
    "exactly two body ceilings: the server default and the project's location"
assert_contains "$NGINX" "client_max_body_size 25M;" "the server-wide default is still 25M"
assert_contains "$NGINX" "client_max_body_size 60M;" "the project's location carries its own ceiling"

# The elevated value must live INSIDE the project location, not leak to server
# level. Read the ceiling that is in scope between the location's braces.
SCOPED=$(printf '%s' "$NGINX" | awk -v uri="$FIXTURE_URI" '
    index($0, "location = " uri) { inblk = 1 }
    inblk && /client_max_body_size/ { gsub(/[^0-9A-Za-z]/, "", $2); print $2; exit }
')
assert_eq "$SCOPED" "60M" "the elevated ceiling is scoped inside the project location"

# --- Wiring: the location hands off to the front controller -----------------
assert_contains "$NGINX" 'fastcgi_param SCRIPT_FILENAME $document_root/index.php;' \
    "the front-controller snippet is wired into the effective configuration"

# --- PHP: the project ini is loaded, and loaded LAST ------------------------
assert_eq "$(php_ini upload_max_filesize)" "60M" "upload_max_filesize is the project's value"
assert_eq "$(php_ini post_max_size)" "66M" "post_max_size is the project's value"
assert_contains "$(in_container 'php --ini')" "zzz-project.ini" "the project ini is among the scanned files"

# --- Effective behavior: the ceiling bites exactly where it was scoped ------
# 30M is over the 25M default and under the project's 60M, so one body size
# separates "refused everywhere" from "accepted on the named route only".
assert_eq "$(post_status / 30)" "413" "a 30M body to a default route is still refused"

STATUS="$(post_status "$FIXTURE_URI" 30)"
if [ "$STATUS" = "413" ]; then
    fail "a 30M body to the elevated route is accepted by nginx — got 413"
else
    pass "a 30M body to the elevated route is accepted by nginx (status $STATUS)"
fi

# --- The pre-body guard is real ---------------------------------------------
# limit_conn/limit_req run in the preaccess phase, before the body is read, so a
# throttled request never lands in client_body_temp_path. Proving the directives
# are in scope for the elevated location is what makes the temp-disk and
# bandwidth exposure bounded rather than merely documented.
GUARD=$(printf '%s' "$NGINX" | awk -v uri="$FIXTURE_URI" '
    index($0, "location = " uri) { inblk = 1 }
    inblk && /limit_conn / { conn = 1 }
    inblk && /limit_req / { req = 1 }
    inblk && /^[[:space:]]*\}/ && seen { exit }
    inblk { seen = 1 }
    END { print (conn ? "conn" : "-") "/" (req ? "req" : "-") }
')
assert_eq "$GUARD" "conn/req" "the elevated location carries pre-body concurrency and rate guards"

finish
