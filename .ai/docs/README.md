# docs/ — how this project remembers what it learns

Read this first. It defines where every kind of knowledge lives, so the docs **compound instead of
sprawling**. The rule behind all of it: **one fact, one home**.

**Agent-first, human-readable.** These docs exist so an agent can load the *minimum* context to act
correctly — deterministic structure, cross-references that resolve, every doc self-contained (full
context from the repo, never an external tracker). A human reads them just as easily.

## Two layers

- **Always-loaded orientation** — `.ai/BRIEF.md` (what & why), `.ai/CODEMAP.md` (where things are), and
  `.ai/MEMORY.md` (current friction to avoid). Small and stable; read at the **start of every task**.
- **On-demand depth** — this `docs/` folder. Read a piece only when your task **enters that area**.

## The ladder

Knowledge climbs from "what others already figured out" up to "what we guarantee and test." Each rung is
one folder. There is **no status or roadmap file** — current state is read from the ladder itself:
what's in `PRD-drafts/` is proposed / in flight, what's in `PRD/` is shipped and guaranteed.

| Rung | Home | Answers | Authority |
|---|---|---|---|
| **1. Research** | `research/` | "What has the world already done, and what are we aiming at?" — prior art, plus a `references/` subfolder for visual targets (screenshots, competitors, inspiration) | Input |
| **2. PRD draft** | `PRD-drafts/` | "What are we proposing to build, and how?" | A proposal — not yet proven |
| **3. Contract** | `PRD/` | "What must this built system do — enforced by tests?" | **Source of truth** |
| **(alongside)** | `guides/` | "How do I *do* a recurring task in this repo?" | Operational how-to |

```
   research/  ──read before drafting──►  PRD-drafts/  ──build + PROVE with tests──►  PRD/PRD-Feature.md
   (prior art +                          (draft PRD,                                 (tested contract)
    references/ = visual targets)         not yet built)
   guides/  = how to perform a repeatable procedure · consulted at any stage
   MEMORY.md = friction we've hit (always loaded) · delete an entry once it's solved
```

## The one rule that keeps it clean

**A PRD is a *tested* contract, born when a system ships — not a plan for one.**

- A draft in `PRD-drafts/` becomes a `PRD/` only after the system is built and tests prove each requirement.
- Every requirement has an ID (`R-<AREA>-<n>`) *and* a test that proves it; behavior and its PRD change
  in the same commit.
- One feature, one PRD; each states what it **owns** and what it **does NOT own**.
- A PRD states what *is* true. Friction and dead-ends we hit go to `.ai/MEMORY.md`, never a PRD.
- Cite, don't copy: a draft or PRD links to a `research/` note instead of re-explaining it.

## Friction lives in MEMORY, not here

What we learn the hard way — traps, gotchas, non-obvious constraints — goes in `.ai/MEMORY.md` (always
loaded, this codebase only). It's a **living list: delete an entry once the friction is solved.** A trap
that hardens into a permanent rule graduates to `AGENTS.md`.

## Writing & upkeep

Before you create or edit a doc, **read its home's `README.md`** — the contract for what belongs there
and how to write it; copy its `TEMPLATE.md` to start. Then, for every doc (including the `.ai/` files):

- **One question per section**, said once, briefly — never the same fact twice.
- **No stubs** — fill every `<placeholder>`; delete an empty section rather than writing "none".
- **Keep it true** — update a doc in the same task that changes reality; prune stale lines in passing.
- **Self-contained & PII-free** — full context from the repo; refer to people by role; no external tracker.

## How an agent uses this

1. Start of task → read `.ai/BRIEF.md`, `.ai/CODEMAP.md`, `.ai/MEMORY.md` (always).
2. Task enters an area → read its `PRD/` (what's guaranteed); check `PRD-drafts/` for anything in flight,
   `research/` for prior art and visual targets, `guides/` for a procedure.
3. Building something new → draft it in `PRD-drafts/`, citing `research/`.
4. It shipped and tests are green → graduate the draft to a `PRD/`, mapping every `R-` to its test, and
   **delete the draft** (no pointer stub).
5. Hit friction → add a line to `.ai/MEMORY.md` (and delete it once solved). A repeatable procedure → `guides/`.

Each home has its own `README.md` (its rules) and a `TEMPLATE.md` (copy it to start a new doc).
