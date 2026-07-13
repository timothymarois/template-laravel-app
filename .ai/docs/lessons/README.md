# lessons/ — hard-won, do not repeat

The single home for what we learned by working this codebase — mistakes that cost real time and the rule
that prevents repeating them. **Split by area so you read only what you're touching**, not one giant file.

Answers: *"What has this project already learned the hard way about `<area>`?"*

## Scope — this codebase only

A lesson here is **specific to this repo**: a trap in its build, a constraint in its data model, a quirk
of a subsystem. Never record:

- user preferences or "how someone likes to work,"
- lessons that aren't specific to this codebase,
- anything that would be true in another repo.

Those belong in the agent's own memory, not here. If it isn't a fact about *this code*, it doesn't go in.

## Rules

- **One lesson, one area.** Put it in the file for the area someone is working in when they'd hit it.
  A cross-cutting lesson picks its primary area and cross-references. A repo-wide build/tooling trap
  lives in `lessons/build.md` (or `lessons/tooling.md`).
- **Per-file IDs: `L-<AREA>-<n>`.** Each file carries its **own** counter (`L-BUILD-1`, `L-UI-1`, …), so
  a new lesson takes the next number by reading only its own file. IDs are stable and never reused —
  a deleted lesson's number is retired. Cross-reference by full ID (`L-BUILD-3`), never a bare `#n`.
- **Format:** `## L-<AREA>-<n>. <blunt title>` → **What happened** (brief in-repo context) →
  **Root cause** → **Fix** (what actually works — the solution, concretely) → **Rule** (the directive to
  prevent recurrence). A lesson carries the solution, not just a warning. Keep the why; cut the narrative.
- **Lessons, not contracts.** What *is* true and tested lives in `PRD/`. This folder is what we
  *learned* — including dead-ends and things we tried and rejected.
- **Self-contained.** Understandable from the repo alone — never cite a ticket or tracker.

## Index

*One row per area file. Add a row when you add a file.*

| Area | File | Covers |
|---|---|---|
| _(none yet)_ | | |

Copy `TEMPLATE.md` to start an area's lessons file.
