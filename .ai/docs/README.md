# docs/ — how this project remembers what it learns

Read this first. It defines where every kind of knowledge lives and how it flows, so the docs
**compound instead of sprawling**. The rule behind all of it: **one fact, one home**, and each home
answers exactly one question.

**These docs are agent-first, human-readable.** They exist so an agent can load the *minimum* context
needed to act correctly — deterministic structure, stable IDs, cross-references that resolve, and every
doc self-contained (full context from the repo alone, never from an external tracker). A human reads
them just as easily.

## Orientation vs. depth — what lives where

Knowledge is separated by **when it's loaded**:

- **`.ai/BRIEF.md`** (what we're building and why) and **`.ai/CODEMAP.md`** (where things are) —
  *always-loaded orientation.* Small, stable, repo-wide. An agent reads these at the **start of every
  task**, before it knows what it's touching.
- **`.ai/docs/`** (this folder) — *on-demand depth.* An agent reads a piece only when its task **enters
  that area**.

## The ladder

There is deliberately **no shared "current state" or roadmap file** — one mutable status doc becomes a
merge-conflict magnet the moment a team works in parallel. Current state is read from the ladder itself,
conflict-free because every item is its own file: **what's in flight** = what's in `design/`; **what's
shipped and guaranteed** = what's in `PRD/`. Next-up planning for a team lives in your tracker, not here.

Knowledge climbs a **maturity ladder** — from "what others already figured out" up to "what we guarantee
and test." Each rung is one folder.

| Rung | Home | Answers | Authority |
|---|---|---|---|
| **1. Research** | `research/` | "What has the world already done about `<X>`?" | Input — prior art |
| **1. References** | `references/` | "What are we building *toward* — targets, competitors, inspiration?" | Input — a direction |
| **2. Design** | `design/` | "What are we proposing to build, and how?" | A proposal — not yet proven |
| **3. Contract** | `PRD/` | "What must this built system do — enforced by tests?" | **Source of truth** |
| **(alongside)** | `lessons/` | "What did *we* learn by doing — what to never repeat?" | Our memory of this codebase |
| **(alongside)** | `guides/` | "How do I *do* a recurring task in this repo?" | Operational how-to |

```
   in flight = design/          shipped & guaranteed = PRD/
   ──────────────────────────────────────────────────────────────
   research/  ┐                                          build + PROVE
   (prior art)├─ read before designing ─► design/ ─────► with tests ──► PRD/PRD-Feature.md
   references/┘                           (proposal)                    (tested contract)
   (what to build toward)                     ▲                              │
                                              │   hit a dead-end / gotcha?   │
                                              └──── lessons/<area>.md ◄───────┘
   guides/ = how to perform a repeatable procedure · consulted at any stage
```

**Research vs. References** — both are inputs, but distinct: *research* is prior art you read to learn
**how to build** something; *references* are curated targets you look at to decide **what to build
toward**, and to generate related visuals.

## The one rule that keeps it clean

**A PRD is a *tested* contract, born when a system ships — not a plan for one.**

- **Design ≠ PRD.** "Here's what we'll build" is a *design* (rung 2). It becomes a PRD only after the
  system is implemented and tests prove each requirement.
- **A PRD and its tests are one thing.** Every requirement has an ID (`R-<AREA>-<n>`) *and* a test that
  proves it. To change behavior later, you change the PRD **and** its test in the same commit.
- **One feature, one PRD.** Each states what it **owns** and what it **does NOT own**, so no two PRDs
  define the same thing.
- **A PRD states what *is* true.** What we *tried and rejected* goes to `lessons/`.
- **Cite, don't copy.** A design or PRD links to a `research/` note instead of re-explaining it.

## Learned knowledge has one home: `lessons/`

Everything we learn by working the code lives in `lessons/`, split by area and read on demand. It is
**about this codebase only** — never user preferences, never how someone likes to work, never a lesson
that isn't specific to this repo. (Those belong in the agent's own memory, not here.) A repo-wide trap
that applies no matter what you touch still lives here, filed under its area (e.g. build/tooling traps in
`lessons/build.md`); a genuinely universal *rule* belongs in `AGENTS.md`.

## Writing & upkeep — keeping every doc tidy

**Before you create or edit a doc, read its home's `README.md`** — that file is the contract for what
belongs there and how to write it (plus any `R-`/`L-` ID convention), and you copy its `TEMPLATE.md` to
start. The rules below are the shared standard on top of that, and apply to **every doc in the repo**,
including `.ai/BRIEF.md` and `.ai/CODEMAP.md`. Each home's `README.md` adds only what's unique to it; the
standard below is not repeated there.

- **One question per section.** A section answers exactly one thing. If a fact fits another section — or
  another home — better, move it there. Never write the same fact twice (one fact, one home, down to the
  section).
- **Say it once, briefly.** Short bullets, one idea per line; a reader skims and gets it. Cut narrative,
  history, and restated context — these docs address the next person doing work, nothing else.
- **No stubs.** Replace every `<placeholder>` when you fill a doc. Delete a section that has nothing to
  say rather than leaving it empty or writing "none/N/A".
- **Keep it true.** Update a doc in the **same task** that changes the reality it describes; a doc that
  contradicts the code is worse than none. Prune stale lines in passing whenever you notice them.
- **Respect the size bar.** Orientation stays ~one screen. Depth splits instead of bloating: lessons by
  area, one PRD per feature — never one giant file.
- **Self-contained and PII-free.** Full context from the repo alone; never cite an external ticket or
  tracker. Refer to people by role, never by name; no secrets or machine paths.

## How an agent uses this

1. Start of task → read `.ai/BRIEF.md` and `.ai/CODEMAP.md` (always).
2. Task enters an area → read that area's `PRD/` (what's guaranteed) and `lessons/` (what to avoid);
   check `design/` for anything in flight and `research/`/`references/` for prior art and targets.
3. Building something new → draft in `design/`; cite `research/`; look at `references/` for the target.
4. It shipped and tests are green → graduate the design to a `PRD/`, mapping every `R-` to its test.
5. Learned something the hard way → `lessons/<area>.md`. A repeatable procedure → `guides/`.

Each folder has its own `README.md` (its rules) and a `TEMPLATE.md` (copy it to start a new doc).
