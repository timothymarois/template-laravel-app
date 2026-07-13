# PRD-drafts/ — upcoming PRDs

Draft PRDs: "here's what we'll build and how" for a system that **isn't built yet**. A draft is exactly
that — rewrite it freely, argue with it, throw it out. It is **not** a contract; until the system ships
with passing tests, nothing here is guaranteed.

Answers: *"What are we proposing to build for `<feature>`, and how?"*

## The lifecycle that matters

A draft has one destiny: **graduate to a `PRD/`**, or die in review.

```
research/  ──►  PRD-drafts/<slug>.md  ──build it──►  tests pass  ──►  PRD/PRD-<Feature>.md
(input)         (draft PRD)                                          (tested contract)
```

- While in flight it lives here and changes as much as needed.
- The moment the system is built and its tests are green, its **truth moves to `PRD/`** — every
  requirement mapped to a test. **Delete the draft** (no pointer stub). Never keep two copies of the
  same truth.
- A rejected draft's lesson, if any, goes to `.ai/MEMORY.md` — the draft itself doesn't linger.

## Rules

- **One system per file.** `PRD-drafts/<slug>.md` (e.g. `PRD-drafts/undo-redo.md`).
- **Cite inputs, don't restate them.** Link the `research/` notes (and `research/references/` visuals)
  that informed it.
- **Draft the requirements as IDs.** Write them as `R-<AREA>-<n>` so they carry straight into the PRD on
  graduation — each already saying how it'll be proven.
- **A draft never lives in `PRD/`.** If it isn't built and tested, it stays here.

Copy `TEMPLATE.md` to start a draft.
