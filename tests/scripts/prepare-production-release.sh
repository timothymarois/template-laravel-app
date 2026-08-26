#!/usr/bin/env bash

set -euo pipefail

repository_root="$(git rev-parse --show-toplevel)"
fixture_root="$(mktemp -d "${TMPDIR:-/tmp}/template-release-test.XXXXXX")"

cleanup() {
    [[ -n "$fixture_root" && -d "$fixture_root" ]] && rm -rf "$fixture_root"
}
trap cleanup EXIT

new_fixture() {
    local name="$1"
    local fixture="$fixture_root/$name"
    local remote="$fixture_root/$name-origin.git"

    mkdir -p "$fixture/scripts"
    cp "$repository_root/scripts/prepare-production-release" "$fixture/scripts/prepare-production-release"
    cp "$repository_root/composer.json" "$fixture/composer.json"
    cp "$repository_root/package.json" "$fixture/package.json"

    # Release branches intentionally change the repository manifests. Fixtures
    # must still model a fresh release starting from main's neutral version.
    python3 - "$fixture" <<'PY'
import json
import re
import sys
from pathlib import Path

root = Path(sys.argv[1])

for filename in ("composer.json", "package.json"):
    path = root / filename
    source = path.read_text()
    current_version = json.loads(source)["version"]
    updated, count = re.subn(
        rf'(?m)^(\s*"version"\s*:\s*)"{re.escape(current_version)}"',
        r'\g<1>"0.0.0"',
        source,
        count=1,
    )
    assert count == 1
    path.write_text(updated)
PY

    git init --quiet --initial-branch=main "$fixture"
    git init --quiet --bare "$remote"
    git -C "$fixture" config user.name "Release Test"
    git -C "$fixture" config user.email "release-test@example.invalid"
    git -C "$fixture" add .
    git -C "$fixture" commit --quiet -m "Initial fixture"
    git -C "$fixture" remote add origin "$remote"
    git -C "$fixture" push --quiet -u origin main

    echo "$fixture"
}

valid_fixture="$(new_fixture valid)"
git -C "$valid_fixture" switch --quiet -c release/v1.0.0
(
    cd "$valid_fixture"
    scripts/prepare-production-release v1.0.0 >/dev/null
)

python3 - "$valid_fixture" <<'PY'
import json
import sys
from pathlib import Path

root = Path(sys.argv[1])
for filename in ("composer.json", "package.json"):
    assert json.loads((root / filename).read_text())["version"] == "1.0.0"
PY

[[ "$(git -C "$valid_fixture" diff --name-only)" == $'composer.json\npackage.json' ]]

wrong_branch_fixture="$(new_fixture wrong-branch)"
if (cd "$wrong_branch_fixture" && scripts/prepare-production-release v1.0.0 >/dev/null 2>&1); then
    echo "Expected preparation on main to fail" >&2
    exit 1
fi

mismatch_fixture="$(new_fixture mismatch)"
git -C "$mismatch_fixture" switch --quiet -c release/v1.0.1
if (cd "$mismatch_fixture" && scripts/prepare-production-release v1.0.0 >/dev/null 2>&1); then
    echo "Expected a mismatched release branch to fail" >&2
    exit 1
fi

dirty_fixture="$(new_fixture dirty)"
git -C "$dirty_fixture" switch --quiet -c release/v1.0.0
printf '\n' >> "$dirty_fixture/package.json"
if (cd "$dirty_fixture" && scripts/prepare-production-release v1.0.0 >/dev/null 2>&1); then
    echo "Expected a dirty worktree to fail" >&2
    exit 1
fi

stale_fixture="$(new_fixture stale)"
git -C "$stale_fixture" switch --quiet -c release/v1.0.0
git -C "$stale_fixture" switch --quiet main
git -C "$stale_fixture" commit --quiet --allow-empty -m "Advance main"
git -C "$stale_fixture" push --quiet origin main
git -C "$stale_fixture" switch --quiet release/v1.0.0
if (cd "$stale_fixture" && scripts/prepare-production-release v1.0.0 >/dev/null 2>&1); then
    echo "Expected a stale release base to fail" >&2
    exit 1
fi

existing_tag_fixture="$(new_fixture existing-tag)"
git -C "$existing_tag_fixture" tag v1.0.0
git -C "$existing_tag_fixture" switch --quiet -c release/v1.0.0
if (cd "$existing_tag_fixture" && scripts/prepare-production-release v1.0.0 >/dev/null 2>&1); then
    echo "Expected an existing release tag to fail" >&2
    exit 1
fi

echo "Production release preparation checks passed."
