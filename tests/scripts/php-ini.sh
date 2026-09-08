#!/usr/bin/env bash
#
# Contract test for the PHP configuration the production image actually ships.
#
# The application test runner has its own ini, so a green Pest suite proves
# nothing about what runs in the container. This asserts the committed file, and
# that the Dockerfile still installs it where PHP will read it — and read it LAST.

set -euo pipefail

repository_root="$(git rev-parse --show-toplevel)"
php_ini="$repository_root/docker/config/php.ini"
dockerfile="$repository_root/Dockerfile"

fail() {
    echo "php-ini: $1" >&2
    exit 1
}

[[ -f "$php_ini" ]]    || fail "docker/config/php.ini is missing — the image would ship PHP's defaults."
[[ -f "$dockerfile" ]] || fail "Dockerfile is missing."

# --- the Dockerfile still installs it, and it still sorts last ---------------
#
# PHP reads conf.d alphabetically, so the application's overrides only win if
# their filename sorts after every extension ini the base image drops there.
# Renaming the destination without the zz- prefix leaves a file that is loaded
# and then silently overridden.
destination="$(grep -E '^COPY[[:space:]]+docker/config/php\.ini' "$dockerfile" | awk '{print $NF}' || true)"

[[ -n "$destination" ]] \
    || fail "the Dockerfile no longer copies docker/config/php.ini — the committed settings never reach the image."

case "$destination" in
    /usr/local/etc/php/conf.d/*) ;;
    *) fail "php.ini is installed to '$destination', which is not PHP's conf.d — it will not be read." ;;
esac

basename_destination="${destination##*/}"
case "$basename_destination" in
    zz*) ;;
    *) fail "php.ini is installed as '$basename_destination'; it must sort last in conf.d (a zz- prefix), or an extension ini loaded after it wins." ;;
esac

# --- the settings that must survive an edit ---------------------------------
setting() {
    grep -E "^[[:space:]]*$1[[:space:]]*=" "$php_ini" | tail -n1 | sed -E 's/.*=[[:space:]]*//' | tr -d '[:space:]'
}

[[ -n "$(setting memory_limit)" ]] \
    || fail "memory_limit is unset — the container would inherit PHP's default, not this image's budget."

validate_timestamps="$(setting 'opcache\.validate_timestamps')"
[[ "$validate_timestamps" == "0" ]] \
    || fail "opcache.validate_timestamps is '$validate_timestamps', expected 0 — a per-request stat of every file is a production cost with no benefit in an immutable image."

opcache_enable="$(setting 'opcache\.enable')"
[[ "$opcache_enable" == "1" ]] \
    || fail "opcache.enable is '$opcache_enable', expected 1."

echo "PHP ini checks passed."
