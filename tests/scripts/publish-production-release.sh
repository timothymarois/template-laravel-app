#!/usr/bin/env bash

set -euo pipefail

repository_root="$(git rev-parse --show-toplevel)"
fixture_root="$(mktemp -d "${TMPDIR:-/tmp}/template-publish-test.XXXXXX")"

cleanup() {
    [[ -d "$fixture_root" ]] && rm -rf "$fixture_root"
}
trap cleanup EXIT

new_fixture() {
    local name="$1"
    local fixture="$fixture_root/$name"
    local remote="$fixture_root/$name-origin.git"

    mkdir -p "$fixture/scripts" "$fixture/fake-bin"
    cp "$repository_root/scripts/production-release-version" "$fixture/scripts/production-release-version"
    cp "$repository_root/scripts/publish-production-release" "$fixture/scripts/publish-production-release"
    cp "$repository_root/tests/scripts/stubs/release-gh" "$fixture/fake-bin/gh"
    cp "$repository_root/tests/scripts/stubs/release-curl" "$fixture/fake-bin/curl"
    chmod +x "$fixture/fake-bin/gh" "$fixture/fake-bin/curl"
    printf '{"version":"1.2.3"}\n' > "$fixture/composer.json"
    printf '{"version":"1.2.3"}\n' > "$fixture/package.json"
    cat > "$fixture/template-manifest.json" <<'MANIFEST'
{
    "template": "template-laravel-app",
    "version": "0.0.0",
    "deploy": {
        "repository": "test-owner/test-repo",
        "productionUrl": "https://release-test.invalid",
        "productionBranch": "production"
    }
}
MANIFEST
    git init --quiet --initial-branch=production "$fixture"
    git init --quiet --bare "$remote"
    git -C "$fixture" config user.name "Release Test"
    git -C "$fixture" config user.email "release-test@example.invalid"
    git -C "$fixture" add .
    git -C "$fixture" commit --quiet -m "Production fixture"
    git -C "$fixture" remote add origin "$remote"
    git -C "$fixture" push --quiet -u origin production
    printf 'release.log\nrelease-state\n' >> "$fixture/.git/info/exclude"

    printf '%s\n' "$fixture"
}

run_publisher() {
    local fixture="$1"
    shift

    (
        cd "$fixture"
        PATH="$fixture/fake-bin:$PATH" \
            RELEASE_TEST_STATE="$fixture/release-state" \
            RELEASE_TEST_LOG="$fixture/release.log" \
            RELEASE_VERIFY_ATTEMPTS=1 \
            RELEASE_VERIFY_INTERVAL_SECONDS=0 \
            scripts/publish-production-release "$@"
    )
}

valid_fixture="$(new_fixture valid)"
run_publisher "$valid_fixture" --confirm >/dev/null
grep -Fqx 'create v1.2.3 --repo test-owner/test-repo --generate-notes' "$valid_fixture/release.log"
grep -Fqx 'release https://release-test.invalid/release' "$valid_fixture/release.log"
grep -Fqx 'up https://release-test.invalid/up' "$valid_fixture/release.log"
grep -Fqx 'health https://release-test.invalid/health' "$valid_fixture/release.log"
[[ "$(git -C "$valid_fixture" rev-parse v1.2.3)" == "$(git -C "$valid_fixture" rev-parse HEAD)" ]]

log_lines_before="$(wc -l < "$valid_fixture/release.log")"
run_publisher "$valid_fixture" --confirm >/dev/null
[[ "$(wc -l < "$valid_fixture/release.log")" -eq "$log_lines_before" ]]

unverified_fixture="$(new_fixture unverified)"
if (
    export RELEASE_TEST_DEPLOYED_VERSION=1.2.2
    run_publisher "$unverified_fixture" --confirm >/dev/null 2>&1
); then
    echo "Expected an unverified deployment to fail" >&2
    exit 1
fi
! grep -q '^create ' "$unverified_fixture/release.log"

unhealthy_fixture="$(new_fixture unhealthy)"
if (
    export RELEASE_TEST_HEALTH_STATUS=503
    run_publisher "$unhealthy_fixture" --confirm >/dev/null 2>&1
); then
    echo "Expected an unhealthy deployment to fail" >&2
    exit 1
fi
! grep -q '^create ' "$unhealthy_fixture/release.log"

dirty_fixture="$(new_fixture dirty)"
printf 'dirty\n' >> "$dirty_fixture/package.json"
if run_publisher "$dirty_fixture" --confirm >/dev/null 2>&1; then
    echo "Expected a dirty worktree to fail" >&2
    exit 1
fi

stale_fixture="$(new_fixture stale)"
git -C "$stale_fixture" commit --quiet --allow-empty -m "Unpushed commit"
if run_publisher "$stale_fixture" --confirm >/dev/null 2>&1; then
    echo "Expected a checkout ahead of production to fail" >&2
    exit 1
fi

confirmation_fixture="$(new_fixture confirmation)"
if run_publisher "$confirmation_fixture" >/dev/null 2>&1; then
    echo "Expected a missing confirmation flag to fail" >&2
    exit 1
fi

# A fork that never edited the manifest must be refused, not pointed at
# example.com — where every probe would fail and, worse, could one day succeed.
# The refusal must name the placeholder. Asserting only on a non-zero exit
# would pass for the wrong reason: an unedited `repository` also trips the
# separate repo-mismatch check, so the test could not tell the two apart and
# would stay green with the placeholder guard deleted.
for placeholder in repository productionUrl; do
    fixture="$(new_fixture "placeholder-${placeholder}")"
    python3 - "$fixture/template-manifest.json" "$placeholder" <<'PYSET'
import json
import sys
from pathlib import Path

path, key = Path(sys.argv[1]), sys.argv[2]
data = json.loads(path.read_text())
data["deploy"][key] = "owner/repo" if key == "repository" else "https://example.com"
path.write_text(json.dumps(data, indent=4) + "\n")
PYSET
    # Commit the edit so the fixture worktree stays clean. Otherwise the script
    # could refuse for a dirty tree instead of the placeholder, and this case
    # would be asserting the wrong thing the moment the check order changed.
    git -C "$fixture" commit --quiet -am "Placeholder ${placeholder}"
    if output="$(run_publisher "$fixture" --confirm 2>&1)"; then
        echo "Expected the ${placeholder} placeholder to fail" >&2
        exit 1
    fi
    case "$output" in
        *"still the template placeholder"*) ;;
        *)
            echo "Expected the ${placeholder} refusal to name the placeholder, got: ${output}" >&2
            exit 1
            ;;
    esac
    # A refusal before any probe leaves no log at all, which trivially satisfies
    # "nothing was created".
    ! grep -q '^create ' "$fixture/release.log" 2>/dev/null
done

missing_manifest_fixture="$(new_fixture missing-manifest)"
rm "$missing_manifest_fixture/template-manifest.json"
if run_publisher "$missing_manifest_fixture" --confirm >/dev/null 2>&1; then
    echo "Expected a missing manifest to fail" >&2
    exit 1
fi

echo "Production release publication checks passed."
