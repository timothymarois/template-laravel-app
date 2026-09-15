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
  { label = "Argument", value = "--confirm", cite = "usage" },
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
It is the third of the four steps described on [Releases](../releases.md), after the
[production pull request](02-production-pull-request.md) and before
[Reconciliation](04-reconciliation.md).

## Usage

The `--confirm` argument is required, and the script runs on a checkout of the production branch whose head
equals the branch on `origin`.[^usage][^head]
```sh
scripts/publish-production-release --confirm
```

## Verification

The script asks the production address three questions on each attempt, up to 90 times, 10 seconds
apart, and each request gives up after 15 seconds: `/release` reports the stored version, `/up` answers
`200`, and `/health` answers `200` with a stored `checkResults`.[^loop] `RELEASE_VERIFY_ATTEMPTS` and
`RELEASE_VERIFY_INTERVAL_SECONDS` change the count and the pause.[^loop] A `200` from `/health` with no
stored results is a container the scheduler has not reached yet, and does not count.[^cold] Each attempt
that falls short prints one line.[^loop]
```text
Waiting for v1.2.3: deployed=1.2.2, up=200, health=200, checks-stored=no (3/90)
```

The loop below runs before anything is tagged.[^loop]

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

## Refusals

Every refusal prints `Production release refused:` and the reason, and exits 1.[^fail]

| Condition | Reason printed |
|---|---|
| a `deploy` value is the shipped placeholder | `deploy.repository: deploy.repository is still the template placeholder (owner/repo)`[^manifest] |
| the worktree has uncommitted or untracked files | `the worktree must be clean`[^fail] |
| the head is not the branch on `origin` | `HEAD must exactly match current origin/production`[^head] |
| `gh` is not signed in | `authenticate the GitHub CLI with gh auth login`[^fail] |
| `gh` resolves another repository | `GitHub CLI resolved other/repo, expected owner/repo`[^fail] |
| the manifests read `0.0.0` | `the stored release version could not be resolved`[^version] |
| the tag exists on another commit | `tag v1.2.3 points to a different commit`[^existing] |
| every attempt fell short | `v1.2.3 was not verified live and healthy within the allowed time (a 200 from /health with no stored checkResults does not count as healthy)`[^loop] |

A release that already exists on the same commit, with the tag as its title, is reported and the script
exits 0 without publishing again.[^existing]

## Output

On success the last line names the release.[^publish]
```text
Published v1.2.3: https://github.com/owner/repo/releases/tag/v1.2.3
```

A refusal on a checkout whose manifest still holds the placeholders, copied from a local run:[^manifest]
```text
Production release refused: deploy.repository: deploy.repository is still the template placeholder (owner/repo)
```

[^publish]: `scripts/publish-production-release` — after verification, `gh release create` with
    `--target` the head commit and `--generate-notes`, then `gh release view` and `remote_tag_commit()`
    confirm the tag points at the head; the last Python block prints `Published {tag}: {url}`.
[^usage]: `scripts/publish-production-release` — `usage()` and the exit 2 unless the one argument is
    `--confirm`.
[^head]: `scripts/publish-production-release` — the `head_commit` check against
    `origin/${production_branch}` after `git fetch`.
[^loop]: `scripts/publish-production-release` — the `for` loop over `attempts`, default 90, sleeping
    `interval`, default 10, with `curl --max-time 15` for each of `/release`, `/up` and `/health`, the
    `Waiting for` line, and the final `fail()` message.
[^cold]: `scripts/publish-production-release` — the `health_checked` test and its comment.
[^fail]: `scripts/publish-production-release` — `fail()` prints `Production release refused:` with the
    reason and exits 1; the clean-tree, `gh auth` and repository checks call it.
[^manifest]: `scripts/publish-production-release` — `manifest_deploy()` exits with `deploy.{key} is still
    the template placeholder ({value})`, which the caller wraps in `fail()`; the sample is the output of a
    local run against the shipped manifest.
[^version]: `scripts/publish-production-release` — reads `scripts/production-release-version`, which
    refuses `0.0.0`, and fails when no tag came back.
[^existing]: `scripts/publish-production-release` — the `existing_release` branch prints `Release {tag}
    already exists: {url}` and exits 0 when the tag matches the head, and fails with `tag {tag} points
    to a different commit` otherwise.
