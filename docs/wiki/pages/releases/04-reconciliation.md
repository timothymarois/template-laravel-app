+++
title = "Reconciliation"
subtitle = "bringing the release commit back to main and returning the manifests to 0.0.0"
status = "approved"
goals = false
intent = """
Reconciliation exists so that the next release starts from a main that contains the last one, and so
that main returns to 0.0.0 before a versioned main can block preparation.
"""

[[infobox]]
group = "Identity"
rows = [
  { label = "Command", value = "scripts/assert-neutral-main-version", cite = "guard" },
]

[[infobox]]
group = "Values"
rows = [
  { label = "Version on main", value = "0.0.0", cite = "guard" },
]

[[infobox]]
group = "Rules"
rows = [
  { label = "Guard runs", value = "on changes bound for main", cite = "ci" },
]
+++

The release commit exists only on `production` after publication, and reconciliation brings it back to
`main` with both manifests reset to `0.0.0`.[^guard] It is the last of the four steps described on
[Releases](../releases.md), after [Publication](03-publication.md).

## Merge

A branch cut from `main` merges `origin/production`, sets `version` back to `0.0.0` in `composer.json`
and `package.json`, and is opened as a pull request to `main`.[^guard]
```sh
git switch -c reconcile/v1.2.3 origin/main
git merge origin/production
```

## Guard

A pull request to `main`, or a push to it, whose manifests carry a version fails the
`Require neutral package versions on main` step with the message below, and passes with
`composer.json and package.json are neutral at 0.0.0.`[^ci][^guard]
```text
Neutral version check refused: composer.json carries release version '1.2.3'; main must stay at 0.0.0 — reset both manifests on the reconciliation branch
```

A versioned `main` blocks the next release: preparation requires both manifests at `0.0.0` and a branch
that starts at `origin/main`, so the fault cannot be corrected from the release branch.[^guard]

[^guard]: `scripts/assert-neutral-main-version` — the header comment describes the back-merge and why
    `main` must be neutral; the Python block exits with that message for a version other than `0.0.0`,
    and the closing `echo` prints the passing line.
[^ci]: `.github/workflows/js-checks.yml` — the `Require neutral package versions on main` step runs
    `scripts/assert-neutral-main-version` only when `github.base_ref` or `github.ref_name` is `main`.
