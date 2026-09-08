# Guide: Cut a production release

**When to use:** Everything you intend to ship is on `main` and you are ready to deploy it and publish a tag.
**Prerequisites:** Everything merged and pushed to `origin/main`, `pnpm check` green there, a clean worktree on `main` level with `origin/main`, and `gh auth status` showing an account that can publish releases.

A release moves `main` into `production`, deploys it, proves it is live, and only then publishes the tag.
The version lives in `composer.json` and `package.json`; `main` always holds `0.0.0`, and a release branch
is the only place a real version exists before production. **Never tag `main`** — a tag that is not the
deployed commit describes nothing.

## Decide first

The version. A published tag is never moved or deleted; a wrong one is corrected by cutting the next patch.
Judge the bump on what a user can observe, against `gh release list --limit 5`:

| Bump | When |
|---|---|
| Patch | Fixes, copy corrections, dependency bumps with no visible change |
| Minor | A new page, endpoint, or capability — additive |
| Major | An incompatible change — a removed or renamed route, a changed contract, a migration users must run |

## Steps

### 1. Fill in the deploy block — first time in a fork only

The scripts read the fork's identity from `template-manifest.json`. Until it is edited they refuse, rather
than polling `example.com` and "verifying" a deploy that never happened.

```json
"deploy": {
    "repository": "owner/repo",
    "productionUrl": "https://example.com",
    "productionBranch": "production"
}
```

`repository` is the GitHub `owner/name`, `productionUrl` the deployed site's origin, `productionBranch`
whatever branch actually deploys.

### 2. Prepare the release branch

```bash
git fetch origin main --tags
git switch -c release/v1.2.3 origin/main
scripts/prepare-production-release v1.2.3
```

It refuses unless the branch is named `release/v1.2.3`, starts exactly at `origin/main`, the worktree is
clean, both manifests read `0.0.0`, and the tag does not already exist. It changes only `composer.json` and
`package.json`. Expected: `Prepared v1.2.3 in composer.json and package.json.`

```bash
git commit -am "chore(release): v1.2.3"
git push -u origin release/v1.2.3
```

### 3. Open the pull request to production

```bash
gh pr create --base production --head release/v1.2.3 --title "Release v1.2.3"
```

The base is `production`, not `main`. Wait for required checks on the head commit. The neutral-version
guard does not run here — a release branch is versioned on purpose.

### 4. Merge and let it deploy

Merging is a production deployment. Get it approved, merge, and wait for the deploy to finish.

### 5. Publish the release

```bash
git fetch origin production --tags
git switch production && git merge --ff-only origin/production
scripts/publish-production-release --confirm
```

It refuses unless `HEAD` equals `origin/production` exactly, then polls the production URL until `/release`
reports the version being published and `/up` and `/health` both return `200`, retrying for up to 15
minutes. Only then does it tag, create the GitHub Release against the production commit, and read the
release back to prove the tag points where it should. Re-running after a successful publish is safe.

Expected: `Published v1.2.3: https://github.com/owner/repo/releases/tag/v1.2.3`

### 6. Reconcile production back to main

The release commit exists only on `production`. Bring it back so the next release starts from a `main` that
contains it — **and neutralize both manifests in the same branch**, or the next release is blocked.

```bash
git switch -c reconcile/v1.2.3 origin/main
git merge origin/production
```

Set `version` back to `0.0.0` in both manifests, commit, and open a pull request to `main`. CI runs
`scripts/assert-neutral-main-version` on main-bound changes and fails if you forget.

## Verify

```bash
gh release view v1.2.3 --json tagName,targetCommitish   # tag exists, target is the production commit
git rev-parse v1.2.3 origin/production                  # identical SHAs
curl -s https://example.com/release                     # reports v1.2.3
jq -r .version composer.json package.json               # 0.0.0 0.0.0 once reconciled
```

## Pitfalls

| Message | Cause |
|---|---|
| `deploy.repository is still the template placeholder` | Fill in the `deploy` block (step 1) |
| `template-manifest.json has no valid deploy block` | The block is missing entirely |
| `the worktree must be clean` | Uncommitted or untracked files |
| `run on release/vX.Y.Z, not <branch>` | The branch name must match the version exactly |
| `the release branch must start at current origin/main` | `main` moved. Rebase the release branch onto it |
| `composer.json must start at version 0.0.0` | `main` carries a version — a back-merge was not neutralized. Fix that on a branch to `main` first |
| `tag vX.Y.Z already exists` | That version was released. Choose the next one |
| `HEAD must exactly match current origin/<branch>` | The production PR is not merged, or you have local commits |
| `was not verified live and healthy within the allowed time` | The deploy did not finish, or `/release` still reports the old version. Check the deployment log before retrying |
| `0.0.0 is not a production release` | You are on `main` or an unprepared branch |
