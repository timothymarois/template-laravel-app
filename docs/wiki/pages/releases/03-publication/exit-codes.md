+++
title = "Exit codes"
subtitle = "every outcome of scripts/publish-production-release, with its code and message"
status = "approved"
goals = false
intent = """
The exit codes page exists so that an operator whose release was refused can find the exact message and
what caused it, without reading the script. Every outcome the publication script can end with is listed,
with its code and its message word for word.
"""

[[infobox]]
group = "Identity"
rows = [
  { label = "Command", value = "scripts/publish-production-release", cite = "usage" },
]

[[infobox]]
group = "Exit codes"
rows = [
  { label = "Success", value = "0", cite = "publish" },
  { label = "Refused", value = "1", cite = "fail" },
  { label = "Misuse", value = "2", cite = "usage" },
]
+++

Every refusal prints `Production release refused:` and the reason, and exits 1; messages show the shipped
`owner/repo` and `production`.[^fail][^manifest] What the script checks, and the output it prints on
success, are described on [Publication](../03-publication.md).

| Code | Condition | Message |
|---|---|---|
| `0` | released and read back[^publish] | `Published v1.2.3: https://github.com/owner/repo/releases/tag/v1.2.3` |
| `0` | already released on this commit[^existing] | `Release v1.2.3 already exists: ` and its address |
| `1` | outside a Git checkout[^setup] | `run this inside the repository` |
| `1` | a tool missing[^setup] | `curl is required`, `gh is required`, `git is required`, `python3 is required` |
| `1` | no manifest[^manifest] | `deploy.repository: template-manifest.json is missing` |
| `1` | no readable `deploy` block[^manifest] | `deploy.repository: template-manifest.json has no valid deploy block` |
| `1` | a `deploy` value absent, empty or not text[^manifest] | `deploy.KEY: deploy.KEY is missing` |
| `1` | a `deploy` placeholder | `deploy.repository: deploy.repository is still the template placeholder (owner/repo)`[^manifest] |
| `1` | uncommitted or untracked files | `the worktree must be clean`[^fail] |
| `1` | fetch failed[^fetch] | `could not fetch origin/production and tags` |
| `1` | no branch on `origin`[^fetch] | `origin/production is unavailable` |
| `1` | the head differs from `origin/production` | `HEAD must exactly match current origin/production`[^head] |
| `1` | `gh` cannot read the repository, such as when signed out | `authenticate the GitHub CLI with gh auth login`[^fail] |
| `1` | `gh` on another repository | `GitHub CLI resolved other/repo, expected owner/repo`[^fail] |
| `1` | the manifests are missing, unreadable, not `MAJOR.MINOR.PATCH`, disagree, or read `0.0.0` | `Release version refused: ` and its reason on one line, then `the stored release version could not be resolved`[^version] |
| `1` | tag unreadable[^existing] | `could not inspect v1.2.3 on origin` |
| `1` | release without tag[^existing] | `GitHub Release v1.2.3 exists without a matching Git tag` |
| `1` | tag on another commit | `tag v1.2.3 points to a different commit`[^existing] |
| `1` | existing release misnamed[^existing] | `existing release must use tag and title v1.2.3` |
| `1` | bad attempt count[^settings] | `RELEASE_VERIFY_ATTEMPTS must be a positive integer` |
| `1` | bad pause[^settings] | `RELEASE_VERIFY_INTERVAL_SECONDS must be a non-negative integer` |
| `1` | never verified | `v1.2.3 was not verified live and healthy within the allowed time (a 200 from /health with no stored checkResults does not count as healthy)`[^loop] |
| gh's own | `gh release create` fails[^create] | gh's own message, with no `Production release refused:` prefix |
| `1` | release unreadable[^readback] | `could not verify the published release` |
| `1` | published tag unreadable[^readback] | `could not verify the published tag` |
| `1` | published tag elsewhere[^readback] | `published tag v1.2.3 does not point to production HEAD` |
| `1` | published release misnamed[^readback] | `published release must use tag and title v1.2.3` |
| `2` | no argument, more than one, or anything but `--confirm`[^usage] | `Usage: scripts/publish-production-release --confirm` |

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
    `owner/repo`, `https://example.com` and `production`.
[^fetch]: `scripts/publish-production-release` — line 66 fails with `could not fetch
    origin/${production_branch} and tags` and line 69 with `origin/${production_branch} is unavailable`.
[^version]: `scripts/publish-production-release` — line 82 reads `scripts/production-release-version`
    through a process substitution, whose failure does not stop the script, and line 84 fails with `the
    stored release version could not be resolved` when no matching version and tag came back;
    `scripts/production-release-version` — prints to standard error `Release version refused: ` followed
    by `{filename} is missing`, `{filename} has no valid version`, `{filename} must use strict
    MAJOR.MINOR.PATCH syntax`, `composer.json and package.json versions disagree` or `0.0.0 is not a
    production release`.
[^create]: `scripts/publish-production-release` — line 162 runs `gh release create` without `fail()`, so
    under `set -euo pipefail` on line 3 a failure ends the script with gh's exit code and gh's own output.
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
