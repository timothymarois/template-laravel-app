#!/usr/bin/env bash

set -euo pipefail

repository_root="$(git rev-parse --show-toplevel)"
fixture_root="$(mktemp -d "${TMPDIR:-/tmp}/template-version-test.XXXXXX")"

cleanup() {
    [[ -d "$fixture_root" ]] && rm -rf "$fixture_root"
}
trap cleanup EXIT

new_fixture() {
    local name="$1"
    local composer_version="$2"
    local package_version="$3"
    local fixture="$fixture_root/$name"

    mkdir -p "$fixture/scripts"
    cp "$repository_root/scripts/production-release-version" "$fixture/scripts/production-release-version"
    printf '{"version":"%s"}\n' "$composer_version" > "$fixture/composer.json"
    printf '{"version":"%s"}\n' "$package_version" > "$fixture/package.json"
    git init --quiet "$fixture"

    printf '%s\n' "$fixture"
}

valid_fixture="$(new_fixture valid 1.2.3 1.2.3)"
valid_output="$(cd "$valid_fixture" && scripts/production-release-version)"
[[ "$valid_output" == $'version=1.2.3\ntag=v1.2.3' ]]

for refusal in mismatch zero malformed; do
    case "$refusal" in
        mismatch) fixture="$(new_fixture "$refusal" 1.2.3 1.2.4)" ;;
        zero) fixture="$(new_fixture "$refusal" 0.0.0 0.0.0)" ;;
        malformed) fixture="$(new_fixture "$refusal" 01.2.3 01.2.3)" ;;
    esac

    if (cd "$fixture" && scripts/production-release-version >/dev/null 2>&1); then
        echo "Expected ${refusal} versions to fail" >&2
        exit 1
    fi
done

missing_fixture="$(new_fixture missing 1.2.3 1.2.3)"
rm "$missing_fixture/package.json"
if (cd "$missing_fixture" && scripts/production-release-version >/dev/null 2>&1); then
    echo "Expected a missing manifest to fail" >&2
    exit 1
fi

echo "Production release version checks passed."
