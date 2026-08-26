#!/usr/bin/env bash

# Proves the neutral-main reconciliation invariant end to end:
#
#   1. scripts/assert-neutral-main-version accepts a neutral `main`;
#   2. it refuses the exact state a production back-merge leaves behind, in
#      either manifest, the failure this guard was written for after a real
#      back-merge left it behind in aprillaneart-site; and
#   3. that refused state really does block the next release, while the
#      neutralized state prepares cleanly.
#
# Step 3 is the reason the guard exists. The fixtures the other release checks
# build normalize both manifests to 0.0.0 before running, so they can never
# observe a versioned `main` — this file deliberately does not.

set -euo pipefail

repository_root="$(git rev-parse --show-toplevel)"
fixture_root="$(mktemp -d "${TMPDIR:-/tmp}/template-neutral-version-test.XXXXXX")"

cleanup() {
    [[ -n "$fixture_root" && -d "$fixture_root" ]] && rm -rf "$fixture_root"
}
trap cleanup EXIT

# Builds a git repository holding the real manifests at $version, plus both
# release scripts, so the guard and the helper see a faithful repository root.
new_fixture() {
    local name="$1"
    local version="$2"
    local fixture="$fixture_root/$name"
    local remote="$fixture_root/$name-origin.git"

    mkdir -p "$fixture/scripts"
    cp "$repository_root/scripts/assert-neutral-main-version" "$fixture/scripts/assert-neutral-main-version"
    cp "$repository_root/scripts/prepare-production-release" "$fixture/scripts/prepare-production-release"
    cp "$repository_root/composer.json" "$fixture/composer.json"
    cp "$repository_root/package.json" "$fixture/package.json"

    python3 - "$fixture" "$version" <<'PY'
import json
import re
import sys
from pathlib import Path

root, version = Path(sys.argv[1]), sys.argv[2]

for filename in ("composer.json", "package.json"):
    path = root / filename
    source = path.read_text()
    current = json.loads(source)["version"]
    updated, count = re.subn(
        rf'(?m)^(\s*"version"\s*:\s*)"{re.escape(current)}"',
        rf'\g<1>"{version}"',
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

# Rewrites a single manifest's version, modelling a half-finished back-merge.
set_version() {
    python3 - "$1" "$2" <<'PY'
import json
import re
import sys
from pathlib import Path

path, version = Path(sys.argv[1]), sys.argv[2]
source = path.read_text()
current = json.loads(source)["version"]
updated, count = re.subn(
    rf'(?m)^(\s*"version"\s*:\s*)"{re.escape(current)}"',
    rf'\g<1>"{version}"',
    source,
    count=1,
)
assert count == 1
path.write_text(updated)
PY
}

neutral_fixture="$(new_fixture neutral 0.0.0)"
(cd "$neutral_fixture" && scripts/assert-neutral-main-version >/dev/null)

# The observed state: a back-merge carried production's version onto main untouched.
back_merged_fixture="$(new_fixture back-merged 1.10.0)"
if (cd "$back_merged_fixture" && scripts/assert-neutral-main-version >/dev/null 2>&1); then
    echo "Expected a version carried onto main by a back-merge to fail" >&2
    exit 1
fi

# One manifest neutralized and the other forgotten must fail just as loudly.
composer_only_fixture="$(new_fixture composer-only 0.0.0)"
set_version "$composer_only_fixture/composer.json" 1.10.0
if (cd "$composer_only_fixture" && scripts/assert-neutral-main-version >/dev/null 2>&1); then
    echo "Expected a versioned composer.json to fail" >&2
    exit 1
fi

package_only_fixture="$(new_fixture package-only 0.0.0)"
set_version "$package_only_fixture/package.json" 1.10.0
if (cd "$package_only_fixture" && scripts/assert-neutral-main-version >/dev/null 2>&1); then
    echo "Expected a versioned package.json to fail" >&2
    exit 1
fi

missing_version_fixture="$(new_fixture missing-version 0.0.0)"
python3 - "$missing_version_fixture/package.json" <<'PY'
import re
import sys
from pathlib import Path

path = Path(sys.argv[1])
path.write_text(re.sub(r'(?m)^\s*"version"\s*:\s*"0\.0\.0",\n', '', path.read_text(), count=1))
PY
if (cd "$missing_version_fixture" && scripts/assert-neutral-main-version >/dev/null 2>&1); then
    echo "Expected a manifest without a version to fail" >&2
    exit 1
fi

# The consequence the guard is protecting: a release cut from a versioned main
# is refused, and cannot be corrected from the release branch because the helper
# also pins the branch head to origin/main.
blocked_fixture="$(new_fixture blocked 1.10.0)"
git -C "$blocked_fixture" switch --quiet -c release/v1.11.0
if (cd "$blocked_fixture" && scripts/prepare-production-release v1.11.0 >/dev/null 2>&1); then
    echo "Expected preparation from a versioned main to fail" >&2
    exit 1
fi

# The same release, cut from a neutralized main, prepares cleanly.
reconciled_fixture="$(new_fixture reconciled 0.0.0)"
git -C "$reconciled_fixture" switch --quiet -c release/v1.11.0
(
    cd "$reconciled_fixture"
    scripts/prepare-production-release v1.11.0 >/dev/null
)

python3 - "$reconciled_fixture" <<'PY'
import json
import sys
from pathlib import Path

root = Path(sys.argv[1])
for filename in ("composer.json", "package.json"):
    assert json.loads((root / filename).read_text())["version"] == "1.11.0"
PY

echo "Neutral main version checks passed."
