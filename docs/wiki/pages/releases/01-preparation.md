+++
title = "Preparation"
subtitle = "scripts/prepare-production-release vMAJOR.MINOR.PATCH"
status = "approved"
goals = false
intent = """
Preparation exists so that a release branch carries exactly one change, the version in both manifests,
and cannot be cut from anything but the current main. A wrong branch, a dirty tree or a reused version
should be refused before a file changes.
"""

[[infobox]]
group = "Identity"
rows = [
  { label = "Command", value = "scripts/prepare-production-release", cite = "usage" },
  { label = "Arguments", value = "vMAJOR.MINOR.PATCH", cite = "usage" },
  { label = "Release branch", value = "release/vMAJOR.MINOR.PATCH", cite = "conditions" },
]

[[infobox]]
group = "Rules"
rows = [
  { label = "Changes", value = "composer.json, package.json", cite = "files" },
]

[[infobox]]
group = "Exit codes"
rows = [
  { label = "Success", value = "0", cite = "output" },
  { label = "Refused", value = "1", cite = "fail" },
  { label = "Misuse", value = "2", cite = "usage" },
]
+++

`scripts/prepare-production-release` writes the version into `composer.json` and `package.json` on a
release branch and changes nothing else.[^files] It is the first of the four steps described on
[Releases](../releases.md); the next is the [production pull request](02-production-pull-request.md).

## Usage

The tag is the only argument, and the script runs on a branch named for it.[^usage]
```sh
scripts/prepare-production-release vMAJOR.MINOR.PATCH
scripts/prepare-production-release v1.2.3
```

## Output

On success it prints three lines and leaves the two changed files uncommitted, for review.[^output]
```text
Prepared v1.2.3 in composer.json and package.json.
Review and commit these changes, then open a pull request to production.
Do not tag or publish the release until the production deployment is approved and verified.
```

A refusal on a branch with uncommitted files, copied from a local run:[^run]
```text
Release preparation refused: the worktree must be clean
```

## Exit codes

A failed condition exits with `Release preparation refused:` and the reason.[^fail] The checks on the tag,
the repository, both manifests existing, the worktree, the branch, `origin/main`, the existing tags and
`python3` all run before a file changes.[^setup][^conditions] Each manifest's version, and whether its
version line can be rewritten, is checked just before that manifest is rewritten, so a `package.json`
refused on either count is refused after `composer.json` has already been rewritten.[^python] The
check that nothing but the two manifests changed runs last, after both are rewritten.[^files]

| Code | Condition | Message |
|---|---|---|
| `0` | both manifests carry the version[^output] | `Prepared v1.2.3 in composer.json and package.json.` |
| `1` | the tag is not `vMAJOR.MINOR.PATCH` without leading zeros | `version must use strict vMAJOR.MINOR.PATCH syntax`[^fail] |
| `1` | the script is run outside a Git checkout | `run this inside the repository`[^setup] |
| `1` | `composer.json` or `package.json` does not exist | `composer.json or package.json is missing`[^setup] |
| `1` | the worktree has uncommitted or untracked files | `the worktree must be clean`[^fail] |
| `1` | the checkout is not on a branch | `detached HEAD is not allowed`[^fail] |
| `1` | the branch is not `release/<tag>` | `run on release/v1.2.3, not main`[^conditions] |
| `1` | `origin/main` is not fetched | `origin/main is unavailable; fetch it first`[^conditions] |
| `1` | the branch head differs from `origin/main` | `the release branch must start at current origin/main`[^conditions] |
| `1` | the tag exists locally, or on `origin` | `tag v1.2.3 already exists locally`, `tag v1.2.3 already exists on origin`[^conditions] |
| `1` | the tags on `origin` cannot be read | `could not verify tags on origin`[^conditions] |
| `1` | `python3` is not installed | `python3 is required`[^fail] |
| `1` | either manifest is not at `0.0.0` | `composer.json must start at version 0.0.0`[^python] |
| `1` | a manifest's `"version"` does not open its own line, so it cannot be rewritten in place | `could not update composer.json safely`, or `package.json`[^python] |
| `1` | anything but the two manifests changed | `unexpected files changed`[^files] |
| `2` | no argument, or more than one[^usage] | `Usage: scripts/prepare-production-release vMAJOR.MINOR.PATCH` |

[^files]: `scripts/prepare-production-release` — the Python block rewrites only the `version` line of
    each manifest, and the lines after it (86 to 88) fail with `unexpected files changed` unless `git diff
    --name-only` lists exactly `composer.json` and `package.json`.
[^usage]: `scripts/prepare-production-release` — `usage()` prints the usage line to standard error and
    the script exits 2 unless exactly one argument is given.
[^fail]: `scripts/prepare-production-release` — `fail()` prints `Release preparation refused:` with the
    reason and exits 1; the syntax, clean-tree, detached-head and `python3` checks call it.
[^setup]: `scripts/prepare-production-release` — line 27 fails with `run this inside the repository` when
    `git rev-parse --show-toplevel` fails, and line 30 with `composer.json or package.json is missing`
    unless both files exist.
[^conditions]: `scripts/prepare-production-release` — the syntax, clean-tree, detached-head,
    `expected_branch`, `origin/main`, head-commit, local and remote tag and `python3` checks (lines 21 to
    55), each calling `fail()` before the Python block runs; lines 49 to 53 fail with `could not verify
    tags on origin` when `git ls-remote --exit-code` returns anything but 0 or 2.
[^python]: `scripts/prepare-production-release` — the Python block (lines 57 to 84) loops over
    `composer.json` then `package.json`, exiting with `Release preparation refused: {filename} must start
    at version 0.0.0`, or with `Release preparation refused: could not update {filename} safely` when the
    pattern `(?m)^(\s*"version"\s*:\s*)"0\.0\.0"` matches no line (line 81), or writing that file before
    it reads the next.
[^output]: `scripts/prepare-production-release` — the three closing `echo` lines, reached only after
    every check passed.
[^run]: `scripts/prepare-production-release` — `fail()`; the sample is the output of running the script
    on a checkout with uncommitted files.
