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
  { label = "Argument", value = "vMAJOR.MINOR.PATCH", cite = "usage" },
  { label = "Branch", value = "release/vMAJOR.MINOR.PATCH", cite = "conditions" },
]

[[infobox]]
group = "Exit codes"
rows = [
  { label = "Success", value = "0", cite = "output" },
  { label = "Refused", value = "1", cite = "fail" },
  { label = "Misuse", value = "2", cite = "usage" },
]

[[infobox]]
group = "Rules"
rows = [
  { label = "Files changed", value = "composer.json, package.json", cite = "files" },
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

## Conditions

Every condition is checked before a file changes, and a failed one exits with
`Release preparation refused:` and the reason.[^fail]

| Condition | Reason printed |
|---|---|
| the tag is not `vMAJOR.MINOR.PATCH` without leading zeros | `version must use strict vMAJOR.MINOR.PATCH syntax`[^fail] |
| the worktree has uncommitted or untracked files | `the worktree must be clean`[^fail] |
| the checkout is not on a branch | `detached HEAD is not allowed`[^fail] |
| the branch is not `release/<tag>` | `run on release/v1.2.3, not main`[^conditions] |
| `origin/main` is not fetched | `origin/main is unavailable; fetch it first`[^conditions] |
| the branch head differs from `origin/main` | `the release branch must start at current origin/main`[^conditions] |
| the tag exists locally, or on `origin` | `tag v1.2.3 already exists locally`, `tag v1.2.3 already exists on origin`[^conditions] |
| `python3` is not installed | `python3 is required`[^fail] |
| either manifest is not at `0.0.0` | `composer.json must start at version 0.0.0`[^python] |
| anything but the two manifests changed | `unexpected files changed`[^files] |

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

| Code | Condition | Message |
|---|---|---|
| `0` | both manifests carry the version[^output] | `Prepared v1.2.3 in composer.json and package.json.` |
| `1` | a condition above failed[^fail] | `Release preparation refused: …` |
| `2` | no argument, or more than one[^usage] | `Usage: scripts/prepare-production-release vMAJOR.MINOR.PATCH` |

[^files]: `scripts/prepare-production-release` — the Python block rewrites only the `version` line of
    each manifest, and the script then fails with `unexpected files changed` unless `git diff
    --name-only` lists exactly `composer.json` and `package.json`.
[^usage]: `scripts/prepare-production-release` — `usage()` prints the usage line to standard error and
    the script exits 2 unless exactly one argument is given.
[^fail]: `scripts/prepare-production-release` — `fail()` prints `Release preparation refused:` with the
    reason and exits 1; the syntax, clean-tree, detached-head and `python3` checks call it.
[^conditions]: `scripts/prepare-production-release` — the `expected_branch`, `origin/main`, head-commit
    and local and remote tag checks, each calling `fail()`.
[^python]: `scripts/prepare-production-release` — the Python block exits with `Release preparation
    refused: {filename} must start at version 0.0.0`.
[^output]: `scripts/prepare-production-release` — the three closing `echo` lines, reached only after
    every check passed.
[^run]: `scripts/prepare-production-release` — `fail()`; the sample is the output of running the script
    on a checkout with uncommitted files.
