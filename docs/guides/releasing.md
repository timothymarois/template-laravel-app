# Cutting a production release

A release moves `main` into `production`, deploys it, proves it is live, and only then publishes the tag.
The version lives in `composer.json` and `package.json`; `main` always holds `0.0.0`, and a release branch
is the only place a real version exists before production.

`production` is the live deployment branch. `main` is integration. **Never tag `main`** — a tag that is not
the deployed commit describes nothing.

## First time in a fork: fill in the deploy block

The scripts read the fork's identity from `template-manifest.json`. Until it is edited they refuse, rather
than polling `example.com` and "verifying" a deploy that never happened.

```json
"deploy": {
    "repository": "owner/repo",
    "productionUrl": "https://example.com",
    "productionBranch": "production"
}
```

Set `repository` to the GitHub `owner/name`, `productionUrl` to the deployed site's origin, and
`productionBranch` to whatever branch actually deploys.

## Before you start

- [ ] Everything you intend to release is merged and pushed to `origin/main`.
- [ ] `pnpm check` passes on `main`.
- [ ] The worktree is clean and you are on `main`, level with `origin/main`.
- [ ] `gh auth status` shows an account that can publish releases on the repository.

## Step 1 — Choose the version

Semantic versioning against the previous release, judged on what a user can observe:

| Bump | When |
|---|---|
| Patch | Fixes, copy corrections, dependency bumps with no visible change |
| Minor | A new page, endpoint, or capability — additive |
| Major | An incompatible change — a removed or renamed route, a changed contract, a migration users must run |

```bash
gh release list --limit 5
```

## Step 2 — Prepare the release branch

```bash
git fetch origin main --tags
git switch -c release/v1.2.3 origin/main
scripts/prepare-production-release v1.2.3
```

It refuses unless the branch is named `release/v1.2.3`, starts exactly at `origin/main`, the worktree is
clean, both manifests read `0.0.0`, and the tag does not already exist locally or on origin. It changes
only `composer.json` and `package.json`.

Expected: `Prepared v1.2.3 in composer.json and package.json.`

```bash
git commit -am "chore(release): v1.2.3"
git push -u origin release/v1.2.3
```

## Step 3 — Open the pull request to production

```bash
gh pr create --base production --head release/v1.2.3 --title "Release v1.2.3"
```

The base is `production`, not `main`. Wait for required checks on the head commit. The neutral-version
guard does not run here — a release branch is versioned on purpose.

## Step 4 — Merge and let it deploy

Merging is a production deployment. Get it approved, merge, and wait for the deploy to finish.

## Step 5 — Publish the release

```bash
git fetch origin production --tags
git switch production && git merge --ff-only origin/production
scripts/publish-production-release --confirm
```

It refuses unless `HEAD` equals `origin/production` exactly. It then polls the production URL until
`/release` reports the version being published and `/up` and `/health` both return `200`, retrying for up
to 15 minutes. Only then does it create the tag and the GitHub Release against the production commit, and
read the published release back to prove the tag points where it should.

Expected: `Published v1.2.3: https://github.com/owner/repo/releases/tag/v1.2.3`

Re-running it after a successful publish is safe — it recognises the existing release and exits.

## Step 6 — Reconcile production back to main

The release commit exists only on `production`. Bring it back so the next release starts from a `main` that
contains it — **and neutralize both manifests in the same branch**, or the next release is blocked.

```bash
git switch -c reconcile/v1.2.3 origin/main
git merge origin/production
```

Set `version` back to `0.0.0` in `composer.json` and `package.json`, commit, and open a pull request to
`main`. CI runs `scripts/assert-neutral-main-version` on main-bound changes and fails if you forget.

## When it refuses

| Message | Cause |
|---|---|
| `deploy.repository is still the template placeholder` | Fill in the `deploy` block — see the top of this guide |
| `template-manifest.json has no valid deploy block` | The block is missing entirely |
| `the worktree must be clean` | Uncommitted or untracked files |
| `run on release/vX.Y.Z, not <branch>` | The branch name must match the version exactly |
| `the release branch must start at current origin/main` | `main` moved. Rebase the release branch onto it |
| `composer.json must start at version 0.0.0` | `main` carries a version — a back-merge was not neutralized. Fix that on a branch to `main` first |
| `tag vX.Y.Z already exists` | That version was released. Choose the next one |
| `HEAD must exactly match current origin/<branch>` | The production PR is not merged, or you have local commits |
| `was not verified live and healthy within the allowed time` | The deploy did not finish, or `/release` still reports the old version. Check the deployment log before retrying |
| `0.0.0 is not a production release` | You are on `main` or an unprepared branch |

Never delete or move a published tag to correct a mistake. Cut the next patch version instead.
