+++
title = "Publication"
subtitle = "scripts/publish-production-release --confirm"
status = "approved"
goals = false
intent = """
Publication exists so that the tag and the GitHub release name a commit that production is proved to be
running and to be healthy on. A deploy that is still starting, or whose checks have not been stored,
should hold the tag back rather than let it through.
"""

[[infobox]]
group = "Identity"
rows = [
  { label = "Command", value = "scripts/publish-production-release", cite = "usage" },
  { label = "Arguments", value = "--confirm", cite = "usage" },
  { label = "Settings", value = "RELEASE_VERIFY_ATTEMPTS, RELEASE_VERIFY_INTERVAL_SECONDS", cite = "loop" },
]

[[infobox]]
group = "Values"
rows = [
  { label = "Attempts", value = "90", cite = "loop" },
  { label = "Pause between attempts", value = "10 seconds", cite = "loop" },
  { label = "Request timeout", value = "15 seconds", cite = "loop" },
]

[[infobox]]
group = "Exit codes"
rows = [
  { label = "Success", value = "0", cite = "publish" },
  { label = "Refused", value = "1", cite = "fail" },
  { label = "Misuse", value = "2", cite = "usage" },
]
+++

`scripts/publish-production-release --confirm` waits until production serves the version stored in the
manifests, then creates the Git tag and the GitHub release on that commit and reads both back.[^publish]
It is the third of the four steps described on [Releases](../releases.md), before
[Reconciliation](04-reconciliation.md).

## Usage

The script runs on a checkout of the production branch whose head equals the branch on `origin`.[^head]
```sh
scripts/publish-production-release --confirm
```

## Verification

The script asks the production address three questions on each attempt, up to 90 times, 10 seconds
apart, and each request gives up after 15 seconds: `/release` reports the stored version, `/up` answers
`200`, and `/health` answers `200` with a stored `checkResults`.[^loop] `RELEASE_VERIFY_ATTEMPTS` and
`RELEASE_VERIFY_INTERVAL_SECONDS` change the count and the pause.[^loop] A `200` from `/health` with no
stored results does not count, for the reason described on [Health checks](../health.md).[^cold] Each
attempt that falls short prints one line.[^loop]
```text
Waiting for v1.2.3: deployed=1.2.2, up=200, health=200, checks-stored=no (3/90)
```

Nothing is tagged before the loop passes.[^loop]

```mermaid
flowchart TB
  accTitle: Release verification
  accDescr: Each attempt asks the production address for the version, the up status and the health snapshot. When all three answer as required the tag is published; otherwise the script waits and tries again, and gives up after the last attempt.
  started(["Verification started"]) --> ask["Ask /release, /up and /health"] --> live{"Version, 200, 200<br/>and stored results?"}
  live -- "Yes" --> publish["Create the tag and the release"] --> read["Read the release back"] --> published(["Release published"])
  live -- "No" --> left{"Attempts left?"}
  left -- "Yes" --> wait["Wait 10 seconds"] --> ask
  left -- "No" --> refused(["Release refused"])
```

## Output

On success the last line names the release.[^publish]
```text
Published v1.2.3: https://github.com/owner/repo/releases/tag/v1.2.3
```

A refusal against the shipped manifest, from a local run:[^manifest]
```text
Production release refused: deploy.repository: deploy.repository is still the template placeholder (owner/repo)
```

## Exit codes

Every refusal prints `Production release refused:` and the reason, and exits 1; messages show the shipped
`owner/repo` and `production`.[^fail][^manifest]

| Code | Condition | Message |
|---|---|---|
| `0` | released and read back[^publish] | `Published v1.2.3: https://github.com/owner/repo/releases/tag/v1.2.3` |
| `0` | already released on this commit[^existing] | `Release v1.2.3 already exists: ` and its address |
| `1` | outside a Git checkout[^setup] | `run this inside the repository` |
| `1` | a tool missing[^setup] | `curl is required`, `gh is required`, `git is required`, `python3 is required` |
| `1` | no manifest[^manifest] | `deploy.repository: template-manifest.json is missing` |
| `1` | no readable `deploy` block[^manifest] | `deploy.repository: template-manifest.json has no valid deploy block` |
| `1` | a `deploy` value empty[^manifest] | `deploy.KEY: deploy.KEY is missing` |
| `1` | a `deploy` placeholder | `deploy.repository: deploy.repository is still the template placeholder (owner/repo)`[^manifest] |
| `1` | uncommitted files | `the worktree must be clean`[^fail] |
| `1` | fetch failed[^fetch] | `could not fetch origin/production and tags` |
| `1` | no branch on `origin`[^fetch] | `origin/production is unavailable` |
| `1` | head behind `origin` | `HEAD must exactly match current origin/production`[^head] |
| `1` | `gh` signed out | `authenticate the GitHub CLI with gh auth login`[^fail] |
| `1` | `gh` on another repository | `GitHub CLI resolved other/repo, expected owner/repo`[^fail] |
| `1` | no usable version | `the stored release version could not be resolved`[^version] |
| `1` | tag unreadable[^existing] | `could not inspect v1.2.3 on origin` |
| `1` | release without tag[^existing] | `GitHub Release v1.2.3 exists without a matching Git tag` |
| `1` | tag on another commit | `tag v1.2.3 points to a different commit`[^existing] |
| `1` | existing release misnamed[^existing] | `Production release refused: existing release must use tag and title v1.2.3` |
| `1` | bad attempt count[^settings] | `RELEASE_VERIFY_ATTEMPTS must be a positive integer` |
| `1` | bad pause[^settings] | `RELEASE_VERIFY_INTERVAL_SECONDS must be a non-negative integer` |
| `1` | never verified | `v1.2.3 was not verified live and healthy within the allowed time (a 200 from /health with no stored checkResults does not count as healthy)`[^loop] |
| `1` | release unreadable[^readback] | `could not verify the published release` |
| `1` | published tag unreadable[^readback] | `could not verify the published tag` |
| `1` | published tag elsewhere[^readback] | `published tag v1.2.3 does not point to production HEAD` |
| `1` | published release misnamed[^readback] | `Production release refused: published release must use tag and title v1.2.3` |
| `2` | argument not `--confirm`[^usage] | `Usage: scripts/publish-production-release --confirm` |

[^publish]: `scripts/publish-production-release` — after verification, `gh release create` with
    `--target` the head commit and `--generate-notes`, then `gh release view` and `remote_tag_commit()`
    confirm the tag points at the head; the last Python block prints `Published {tag}: {url}`.
[^usage]: `scripts/publish-production-release` — `usage()` prints the usage line to standard error and
    the script exits 2 unless the one argument is `--confirm`.
[^head]: `scripts/publish-production-release` — the `head_commit` check against
    `origin/${production_branch}` after `git fetch`.
[^loop]: `scripts/publish-production-release` — the `for` loop over `attempts`, default 90, sleeping
    `interval`, default 10, with `curl --max-time 15` for each of `/release`, `/up` and `/health`, the
    `Waiting for` line, and the final `fail()` message.
[^cold]: `scripts/publish-production-release` — the `health_checked` test and its comment.
[^fail]: `scripts/publish-production-release` — `fail()` prints `Production release refused:` with the
    reason and exits 1; every check from line 20 to line 171 calls it in turn, including the clean-tree
    (line 64), `gh auth` (line 72) and repository (line 73) checks, and the two Python blocks that raise
    `SystemExit` print the same prefix themselves, which `set -e` turns into exit 1.
[^setup]: `scripts/publish-production-release` — line 20 fails with `run this inside the repository` when
    `git rev-parse --show-toplevel` fails; the `for` loop on lines 23 to 25 fails with `{command} is
    required` for each of `curl`, `gh`, `git` and `python3` not on the path.
[^manifest]: `scripts/publish-production-release` — `manifest_deploy()` exits with `template-manifest.json
    is missing`, `template-manifest.json has no valid deploy block`, `deploy.{key} is missing` or
    `deploy.{key} is still the template placeholder ({value})`, for the placeholders `owner/repo` and
    `https://example.com`, and lines 59 to 61 read `repository`, `productionUrl` and `productionBranch` in
    that order, each wrapping the message in `fail "deploy.{key}: …"`; `template-manifest.json` — ships
    `owner/repo`, `https://example.com` and `production`; the sample is the output of a local run against
    the shipped manifest.
[^fetch]: `scripts/publish-production-release` — line 66 fails with `could not fetch
    origin/${production_branch} and tags` and line 69 with `origin/${production_branch} is unavailable`.
[^version]: `scripts/publish-production-release` — line 84 fails with `the stored release version could
    not be resolved` when `scripts/production-release-version` returns no matching version and tag;
    `scripts/production-release-version` — prints `Release version refused:` with a missing manifest, a
    manifest with no valid version, a version not in `MAJOR.MINOR.PATCH` form, versions that disagree, or
    `0.0.0`.
[^existing]: `scripts/publish-production-release` — line 96 fails with `could not inspect ${tag} on
    origin` when `remote_tag_commit()` fails; the `existing_release` branch fails with `GitHub Release
    ${tag} exists without a matching Git tag` when no tag exists, fails with `tag {tag} points to a
    different commit` when the tag names another commit (as line 118 does with no release), raises
    `Production release refused: existing release must use tag and title {expected_tag}` when the
    release's tag or name differs, and otherwise prints `Release {tag} already exists: {url}` and exits 0.
[^settings]: `scripts/publish-production-release` — line 123 requires `RELEASE_VERIFY_ATTEMPTS` to match
    `^[1-9][0-9]*$` and line 124 requires `RELEASE_VERIFY_INTERVAL_SECONDS` to match `^[0-9]+$`, each
    calling `fail()` with its message.
[^readback]: `scripts/publish-production-release` — after `gh release create`, line 169 fails with `could
    not verify the published release`, line 170 with `could not verify the published tag`, line 171 with
    `published tag ${tag} does not point to ${production_branch} HEAD`, and the last Python block raises
    `Production release refused: published release must use tag and title {expected_tag}`.
