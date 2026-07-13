# design/ — what we propose to build

Proposals. "Here's what we'll build and how" for a system that **isn't built yet**. A design is a
draft: rewrite it freely, argue with it, throw it out. It is **not** a contract — until the system ships
with passing tests, nothing here is guaranteed.

Answers: *"What are we proposing to build for `<feature>`, and how?"*

## The lifecycle that matters

A design has one destiny: **graduate to a `PRD/`**, or die in review.

```
research/ + references/ ──►  design/<slug>.md  ──build it──►  tests pass  ──►  PRD/PRD-<Feature>.md
   (inputs)                  (proposal, drafts)                                (tested contract)
```

- While in flight it lives here and changes as much as needed.
- The moment the system is implemented and its tests are green, its **truth moves to a `PRD/`** — every
  requirement mapped to a test. Leave a one-line pointer here (or delete the design); don't keep two
  copies of the same truth.
- A rejected design becomes a `lessons/` entry if we learned why — not a lingering file.

## Rules

- **One system per file.** `design/<slug>.md` (e.g. `design/undo-redo.md`).
- **Cite inputs, don't restate them.** Link the `research/` notes and `references/` boards that informed
  it. Keep that detail in its home.
- **State proposed requirements with IDs.** Draft them as `R-<AREA>-<n>` so they carry straight into the
  PRD on graduation — the design already says how each will be proven.
- **A proposal never lives in `PRD/`.** If it isn't built and tested, it stays here. No exceptions.

Copy `TEMPLATE.md` to start a design.
