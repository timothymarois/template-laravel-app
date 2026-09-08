# Guide: Name a branch and write a commit

**When to use:** You are about to create a branch or write a commit message. Read it before the first `git checkout -b` — a name is cheapest to get right before it exists.
**Prerequisites:** None.

## Steps

### 1. Pick the type

One set governs branches, commits, and pull-request titles. Pick by what the change *does* for a reader of
the history, not by which files it touched: a `refactor` that fixes a bug is a `fix`.

| Type | For |
|---|---|
| `feat` | A capability that did not exist before |
| `fix` | A defect corrected |
| `docs` | Documentation only |
| `test` | Tests only — adding, repairing, or proving them |
| `refactor` | Behavior unchanged, structure changed |
| `perf` | A measured performance improvement |
| `chore` | Housekeeping with no product effect |
| `build` | Dependencies, bundler, image, or toolchain |
| `ci` | Workflows and pipeline configuration |

### 2. Name the branch

```
<type>/<short-kebab-slug>
```

`fix/reject-mixed-batches` · `feat/csv-invoice-export` · `docs/clarify-release-steps`

- **Slug:** lowercase kebab-case, a few words describing *the change*. Never a ticket number, a date, or
  an initial — those tell a reader nothing the branch list doesn't already show.
- **No WIP labels** — `wip`, `temp`, `misc`, `test2`. Name the change, not its state.

### 3. Write the commit

```
<type>(optional-scope): <imperative summary>
```

```
fix(batch): reject mixed-model batches
feat(billing): expose invoice export to account admins
docs(releasing): say what happens when the deploy never reports live
```

- **Imperative, not past tense** — "reject", not "rejected" or "rejects". It completes the sentence
  *"Applying this commit will …"*.
- **Summary under ~72 characters**, no trailing period.
- **Scope is optional** and names the area, not the file — `batch`, not `BatchService.php`.
- A body is welcome when the *why* is not obvious from the diff. Separate it with a blank line.

### 4. Strip machine-authorship branding

A branch, commit, or pull request carries **no provider, model, agent, tool, or owner name** — in a name or
in a body — and no generated-by footer, identity block, robot emoji, or provider-style co-author trailer.

Some agent runtimes append these by default; remove them before the commit lands. A footer naming the tool
that produced a change tells a reviewer nothing they can act on, and dates the history permanently.

## Verify

```bash
git rev-parse --abbrev-ref HEAD          # matches <type>/<kebab-slug>
git log -1 --pretty=%s                   # matches <type>(scope): <imperative>
git log -1 --pretty='%s%n%b'             # no provider, model, agent, tool, or owner name
```

A non-conforming name is reshaped before a pull request opens, so it is cheaper to fix now than after the
branch is pushed and referenced.

## What this does not cover

- **Versions and tags** — [`releasing.md`](./releasing.md) owns the bump table and the `main` ->
  `production` flow. A version is decided at release time, not at branch time.
- **Committing at all.** `AGENTS.md` requires approval before any commit or push.
