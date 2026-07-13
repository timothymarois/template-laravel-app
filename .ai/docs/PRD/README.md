# PRD/ — tested contracts (source of truth)

This folder holds **ratified, test-backed PRDs** — the settled requirements each built system must
satisfy, with every requirement mapped to a test. If a doc is here, the system **exists and its tests
enforce it**. This is the source of truth for what the product does; when anything else disagrees with a
PRD, the PRD wins (or the PRD is wrong and gets fixed — in the same commit as its test).

Answers: *"What must `<system>` do — guaranteed and proven?"*

## The one rule

**A PRD is born when a system ships — not when it's planned.** A proposal for something unbuilt lives in
`../PRD-drafts/`; it graduates here only when implemented and green. A PRD is "true" only because a test
says so.

## Rules

- **One feature per file, flat.** `PRD-<System>.md`.
- **Every requirement has an ID and a test.** `R-<AREA>-<n>`, listed in the PRD's **Tests** section
  against the exact test that proves it. Changing behavior means editing the PRD **and** its test in the
  same commit — they never drift.
- **Owns / does NOT own.** Each PRD names what it owns and what it doesn't, cross-referencing siblings,
  so no two PRDs define the same thing.
- **State what *is* true.** Rejected approaches and dead-ends go to `.ai/MEMORY.md`.
- **Self-contained.** State the actual requirement — never point at a ticket or tracker.

## Graduation checklist (PRD-draft → PRD)

1. The system is implemented and behaves as drafted.
2. Every proposed `R-<AREA>-<n>` from the draft has a **passing** test.
3. Copy the requirements into a new `PRD-<System>.md`, fill the Tests table, declare owns / does-NOT-own.
4. **Delete** the old `PRD-drafts/` doc — do not leave a pointer stub. The PRD is the only source of truth.

## Current PRDs

*One row per built, tested system. Keep this index current.*

| PRD | System | Area |
|---|---|---|
| _(none yet)_ | | |

Copy `TEMPLATE.md` to graduate a draft into a PRD.
