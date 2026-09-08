#!/usr/bin/env bash
#
# Contract test for the nginx configuration the production image ships, and for
# the project extension point that keeps a fork out of managed core.
#
# This asserts AGREEMENT BETWEEN FILES and nothing more — it cannot start nginx.
# The built-image proof (nginx -t, effective nginx -T, a real oversized POST) is
# .github/workflows/docker-config.yml. This runs in `pnpm check` in milliseconds
# and catches the wiring mistakes before CI does.

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

# --- the project extension point still exists -------------------------------
for directory in docker/project/nginx/http docker/project/nginx/server docker/project/php; do
    [[ -d "$repository_root/$directory" ]] \
        || fail "$directory is missing — its Dockerfile COPY would fail the image build."
done

for snippet in laravel-fastcgi laravel-front-controller; do
    [[ -f "$repository_root/docker/config/nginx-snippets/$snippet.conf" ]] \
        || fail "docker/config/nginx-snippets/$snippet.conf is missing — a project location has no supported way to reach PHP-FPM."
done

# --- the Dockerfile still bakes every one of them ---------------------------
bakes() {
    # $2 is a regex for grep; $3 is the same path written plainly for the message.
    grep -qE "^[[:space:]]*COPY[[:space:]]+$1[[:space:]]+$2" "$dockerfile" \
        || fail "the Dockerfile no longer copies $1 to $3 — $4"
}
bakes 'docker/config/nginx-snippets/' '/etc/nginx/snippets/'        '/etc/nginx/snippets/'        'the FastCGI snippets would not exist in the image, and nginx.conf includes one.'
bakes 'docker/project/nginx/http/'    '/etc/nginx/project/http/'    '/etc/nginx/project/http/'    'http-context project files (limit_req_zone, limit_conn_zone, map) would never load.'
bakes 'docker/project/nginx/server/'  '/etc/nginx/project/server/'  '/etc/nginx/project/server/'  'project location blocks would never load.'
bakes 'docker/project/php/'           '/usr/local/etc/php/conf\.d/' '/usr/local/etc/php/conf.d/' 'a fork could not raise a PHP limit without editing managed core.'

# --- the http include is at http context, outside the server block ----------
#
# Shared-memory zones cannot be declared inside `server`, so this include has to
# sit before it. This file is installed at sites-available/default, which nginx
# includes from http.
http_include_line="$(grep -nE '^[[:space:]]*include[[:space:]]+/etc/nginx/project/http/\*\.conf;' "$nginx_conf" | head -n1 | cut -d: -f1 || true)"
server_open_line="$(grep -nE '^[[:space:]]*server[[:space:]]*\{' "$nginx_conf" | head -n1 | cut -d: -f1 || true)"

[[ -n "$http_include_line" ]] || fail "nginx.conf no longer includes /etc/nginx/project/http/*.conf — a project could not declare a limit_req_zone anywhere."
[[ -n "$server_open_line" ]]  || fail "nginx.conf has no server block; this test can no longer prove the includes' contexts."
(( http_include_line < server_open_line )) \
    || fail "the http include is at line $http_include_line, inside the server block opened at line $server_open_line — nginx rejects limit_req_zone there."

# --- the server include is LAST, so nothing shipped can be shadowed ---------
#
# nginx tries regex locations in configuration order. If a project include came
# first, a project regex could shadow `location ~ \.php$` or punch a hole in the
# dotfile deny. Last means everything the template ships wins a tie.
server_include_line="$(grep -nE '^[[:space:]]*include[[:space:]]+/etc/nginx/project/server/\*\.conf;' "$nginx_conf" | head -n1 | cut -d: -f1 || true)"
php_location_line="$(grep -nE '^[[:space:]]*location[[:space:]]+~[[:space:]]+\\\.php\$' "$nginx_conf" | head -n1 | cut -d: -f1 || true)"
deny_location_line="$(grep -nE '^[[:space:]]*location[[:space:]]+~[[:space:]]+/\\\.' "$nginx_conf" | head -n1 | cut -d: -f1 || true)"

[[ -n "$server_include_line" ]] || fail "nginx.conf no longer includes /etc/nginx/project/server/*.conf — docker/project/nginx/server/ would be baked into the image and never read."
[[ -n "$php_location_line" ]]   || fail "nginx.conf has no 'location ~ \\.php\$' block."
[[ -n "$deny_location_line" ]]  || fail "nginx.conf has no dotfile deny block."

(( server_include_line > php_location_line )) \
    || fail "the project include is at line $server_include_line, before the PHP handler at line $php_location_line — a project regex could shadow it."
(( server_include_line > deny_location_line )) \
    || fail "the project include is at line $server_include_line, before the dotfile deny at line $deny_location_line — a project regex could expose a dotfile."

# --- the PHP handler still goes through the shared snippet ------------------
grep -qE '^[[:space:]]*include[[:space:]]+/etc/nginx/snippets/laravel-fastcgi\.conf;' "$nginx_conf" \
    || fail "the PHP location no longer includes laravel-fastcgi.conf — a project location reusing that snippet would drift from what the template actually serves."

# --- the transport ceilings can be reached ----------------------------------
#
# nginx refuses a body over client_max_body_size before PHP sees it, and PHP
# refuses a request over post_max_size before upload_max_filesize is consulted.
# Each must be at least the one it gates, or the inner limit is unreachable.
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
