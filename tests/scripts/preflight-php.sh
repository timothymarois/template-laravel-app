#!/usr/bin/env bash

# Proves scripts/preflight-php only ever reports, and reports something usable.
#
# It guards `check:php` and `build`, so it runs on every gate. The failure it exists to
# explain — `env: php: No such file or directory` while pnpm works fine — reads as a
# broken install rather than a shell that cannot see the binary. It must therefore:
#   1. stay silent and succeed when php is already on PATH;
#   2. name a directory only when a php really exists there on THIS machine, so the
#      advice is never a hardcoded guess about somebody else's setup;
#   3. fall back to a generic message when it finds nothing; and
#   4. never modify PATH itself.

set -euo pipefail

repository_root="$(git rev-parse --show-toplevel)"
script="$repository_root/scripts/preflight-php"
fixture_root="$(mktemp -d "${TMPDIR:-/tmp}/template-preflight-test.XXXXXX")"
trap 'rm -rf "$fixture_root"' EXIT

fail() {
    echo "preflight-php check failed: $1" >&2
    exit 1
}

# 1. php on PATH — silent success.
fake_bin="$fixture_root/onpath"
mkdir -p "$fake_bin"
printf '#!/usr/bin/env bash\nexit 0\n' > "$fake_bin/php"
chmod +x "$fake_bin/php"

output="$(PATH="$fake_bin:$PATH" HOME="$fixture_root/empty-home" bash "$script" 2>&1)" \
    || fail "expected success when php is on PATH"
[[ -z "$output" ]] || fail "expected no output when php is on PATH, got: $output"

# 2. php absent from PATH but present in a known candidate directory.
candidate_home="$fixture_root/home"
candidate_dir="$candidate_home/Library/Application Support/Herd/bin"
mkdir -p "$candidate_dir"
printf '#!/usr/bin/env bash\nexit 0\n' > "$candidate_dir/php"
chmod +x "$candidate_dir/php"

if output="$(PATH="/usr/bin:/bin" HOME="$candidate_home" bash "$script" 2>&1)"; then
    fail "expected a non-zero exit when php is not on PATH"
fi
grep -Fq "$candidate_dir" <<<"$output" || fail "expected the discovered directory to be named, got: $output"
grep -Fq 'export PATH=' <<<"$output" || fail "expected a runnable export line, got: $output"

# 3. nothing found anywhere — generic advice, no invented path.
empty_home="$fixture_root/nothing"
no_php_bin="$fixture_root/nophp"
mkdir -p "$empty_home" "$no_php_bin"
# A PATH that can still resolve bash/grep but contains no php, and a HOME with no
# candidate directories, so the script has genuinely nothing to find.
for tool in bash grep printf; do
    resolved="$(command -v "$tool" || true)"
    [[ -n "$resolved" ]] && ln -sf "$resolved" "$no_php_bin/$tool"
done
if output="$(PATH="$no_php_bin" HOME="$empty_home" bash "$script" 2>&1)"; then
    fail "expected a non-zero exit when no php exists"
fi
grep -Fq 'No PHP found' <<<"$output" || fail "expected the generic message, got: $output"
! grep -Fq 'export PATH=' <<<"$output" || fail "must not suggest a path it never found"

# 4. reporting only — the caller's PATH is untouched.
before="/usr/bin:/bin"
after="$(PATH="$before" HOME="$candidate_home" bash -c 'bash "$0" >/dev/null 2>&1 || true; printf "%s" "$PATH"' "$script")"
[[ "$after" == "$before" ]] || fail "preflight-php must not modify PATH (was '$before', now '$after')"

echo "PHP preflight checks passed."
