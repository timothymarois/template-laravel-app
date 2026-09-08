#!/usr/bin/env bash
#
# Contract test for the nginx configuration the production image ships, and for
# the extension point that keeps a fork out of it.
#
# Nothing here starts nginx — this repository has no container build. It asserts
# AGREEMENT between the files: that the fork-owned snippet include still exists
# and still precedes the PHP location, that the Dockerfile still bakes both
# fragment directories, and that the nginx and PHP transport ceilings can
# actually be reached. `nginx -t` on a built image is still required before
# trusting a change on a real deploy.

set -euo pipefail

repository_root="$(git rev-parse --show-toplevel)"
nginx_conf="$repository_root/docker/config/nginx.conf"
php_ini="$repository_root/docker/config/php.ini"
dockerfile="$repository_root/Dockerfile"

fail() {
    echo "nginx-config: $1" >&2
    exit 1
}

[[ -f "$nginx_conf" ]] || fail "docker/config/nginx.conf is missing."

# --- the fork-owned extension point still exists ----------------------------
for directory in docker/project/nginx/http docker/project/nginx/server; do
    [[ -d "$repository_root/$directory" ]] \
        || fail "$directory is missing — the Dockerfile COPY of it would fail the image build."
done

grep -qE '^[[:space:]]*COPY[[:space:]]+docker/project/nginx/http/[[:space:]]+/etc/nginx/conf\.d/' "$dockerfile" \
    || fail "the Dockerfile no longer bakes docker/project/nginx/http/ into conf.d — http-context fragments (limit_req_zone, limit_conn_zone, map) would never load."

grep -qE '^[[:space:]]*COPY[[:space:]]+docker/project/nginx/server/[[:space:]]+/etc/nginx/snippets/' "$dockerfile" \
    || fail "the Dockerfile no longer bakes docker/project/nginx/server/ into snippets — the include below would match nothing."

# --- the include is present, and in the only position that works -------------
#
# nginx matches regex locations in file order. A fork's location must be seen
# before `location ~ \.php$`, or that block wins the URI and every directive the
# fork set is replaced before the request body is read.
include_line="$(grep -nE '^[[:space:]]*include[[:space:]]+/etc/nginx/snippets/\*\.conf;' "$nginx_conf" | head -n1 | cut -d: -f1 || true)"
php_location_line="$(grep -nE '^[[:space:]]*location[[:space:]]+~[[:space:]]+\\\.php\$' "$nginx_conf" | head -n1 | cut -d: -f1 || true)"

[[ -n "$include_line" ]] \
    || fail "nginx.conf no longer includes /etc/nginx/snippets/*.conf — docker/project/nginx/server/ would be baked into the image and never read."
[[ -n "$php_location_line" ]] \
    || fail "nginx.conf has no 'location ~ \\.php\$' block; this test can no longer prove the include's position."
(( include_line < php_location_line )) \
    || fail "the snippets include is at line $include_line, after the PHP location at line $php_location_line — a fork's location would never win its URI."

# --- the transport ceilings can be reached ----------------------------------
#
# nginx refuses a body over client_max_body_size before PHP sees it, and PHP
# refuses a request over post_max_size before upload_max_filesize is consulted.
# So each must be at least the one it gates, or the inner limit is unreachable
# and its value is a lie.
to_bytes() {
    local raw="${1//[[:space:]]/}"
    local number="${raw//[^0-9]/}"
    local suffix="${raw//[0-9]/}"
    [[ -n "$number" ]] || { echo ""; return; }
    suffix="$(printf '%s' "$suffix" | tr '[:lower:]' '[:upper:]')"
    case "$suffix" in
        G)  echo $(( number * 1024 * 1024 * 1024 )) ;;
        M)  echo $(( number * 1024 * 1024 )) ;;
        K)  echo $(( number * 1024 )) ;;
        "") echo "$number" ;;
        *)  echo "" ;;
    esac
}

nginx_max="$(grep -E '^[[:space:]]*client_max_body_size' "$nginx_conf" | tail -n1 | sed -E 's/.*client_max_body_size[[:space:]]*//; s/;.*//' || true)"
php_post="$(grep -E '^[[:space:]]*post_max_size[[:space:]]*=' "$php_ini" | tail -n1 | sed -E 's/.*=[[:space:]]*//' || true)"
php_upload="$(grep -E '^[[:space:]]*upload_max_filesize[[:space:]]*=' "$php_ini" | tail -n1 | sed -E 's/.*=[[:space:]]*//' || true)"

nginx_bytes="$(to_bytes "$nginx_max")"
post_bytes="$(to_bytes "$php_post")"
upload_bytes="$(to_bytes "$php_upload")"

for pair in "client_max_body_size:$nginx_bytes" "post_max_size:$post_bytes" "upload_max_filesize:$upload_bytes"; do
    [[ -n "${pair#*:}" ]] || fail "could not read ${pair%%:*} — this test cannot prove the ceilings agree."
done

(( nginx_bytes >= post_bytes )) \
    || fail "client_max_body_size ($nginx_max) is below PHP's post_max_size ($php_post) — nginx refuses the request first, so post_max_size is unreachable."
(( post_bytes >= upload_bytes )) \
    || fail "post_max_size ($php_post) is below upload_max_filesize ($php_upload) — the whole request is refused before the file limit applies."

echo "nginx config checks passed."
