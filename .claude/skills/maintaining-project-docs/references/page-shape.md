# Keep a page scannable

A correct page still fails if a reader has to read it all to find one fact. Most documentation is too
long, and the length is rarely the content — it is the run-up.

## Lead with the answer

The first line of a section is the fact. Reasoning, history, and the incident that taught it come
after, if at all.

```text
Bad:   It is worth understanding why gateways behave this way. Historically the build this
       replaces asked a file whether a gateway was alive, which turned out to be unreliable
       because a file survives the process that wrote it, and so the answer was often wrong.
       For that reason liveness is now asked of the kernel.
Good:  Liveness is asked of the kernel. No file decides it — a file outlives the process that
       wrote it, and the previous build shipped that bug.
```

## Budgets

A page past its budget usually contains two pages. Check the number before you publish.

| Page | Budget |
|---|---|
| Orientation — brief, codemap | one screen; codemap under ~200 lines |
| Concept | **under ~120 lines** |
| Guide | **under ~150 lines** |
| One reference group | under ~300 lines |
| Home index | one row per page |

## Every guide has the same shape

A reader who has followed one guide should not have to learn a new layout for the next. Use these
headings, in this order, and add nothing that is not one of them:

```md
# Guide: <do the thing>

**When to use:** one line.
**Prerequisites:** one line, or "None."

## Steps

### 1. <Imperative — do this>

What to do, in as few lines as possible. Then the check that proves it worked.

### 2. <Next>

## Verify

Paste-runnable commands with the expected result beside each.

## Pitfalls

| Symptom | Fix |
|---|---|
```

- **Steps are numbered and imperative.** "Enable tenancy", not "Enabling tenancy" or "About enabling".
- **Every step ends in a check.** A step whose result cannot be observed is an instruction the reader
  cannot confirm they followed.
- **`Verify` is commands, not prose.** If it cannot be pasted, it is not verification.
- **`Pitfalls` is a table.** Symptom the reader will actually see, and the fix. Not a discussion.
- Optional, only when they genuinely apply: a `Decide first` section **above** Steps when a choice is
  expensive to reverse, and a `What this does not cover` line at the foot.

## Every concept page has the same shape

```md
# <Subsystem>

One paragraph: what it is and what it is for.

## How it works

Tables and short sections. The mechanism, not a tour.

## How it fails

| Symptom | Cause |
|---|---|

## Verify

Commands that prove it is working in a live environment.
```

- Sections under `How it works` are named for the subsystem, not for the skeleton — `The head has one
  owner` beats `Mechanism`. The three top-level headings are what stays fixed.
- Optional, only when they genuinely apply: a `Configure` section between `How it works` and `How it
  fails`, when turning the subsystem on is part of what the page owns, and a `What is not here` line
  at the foot pointing at the page that owns the neighbouring fact.
- **`Steps` is a guide heading.** A concept page that grows one has either borrowed guide vocabulary
  for its `Configure` section, or is two pages.

**`How it fails` is the reason the page exists.** It is what a reader cannot derive from the code, and
it is the first thing they will search for at 2am. Write it first if it helps.

## Cut these on sight

- **The run-up.** "Before we look at X, it is worth noting…" Delete to the first fact.
- **The restatement.** A section that says what the previous section said, in other words.
- **The tour.** Narrating what the reader is about to read. The headings already do it.
- **The hedge.** "generally", "typically", "should usually". State it, or mark it unverified.
- **The aside.** A clause about something else, set off by dashes, that the sentence does not need.
- **Two examples that make one point.** Keep the better one.
- **The reassurance.** "Don't worry", "this is straightforward", "as you can see".

## Prefer a table when the content is tabular

Every state, every flag, every exit code, every field. Prose describing six parallel things is six
rows badly formatted. Give the table a column for the thing and a column for what it means, and put
the explanation in the cell rather than in a paragraph above it.

## One idea per paragraph, one question per heading

Three sentences is a long paragraph. A heading a reader cannot answer from the section beneath it is
the wrong heading. Where a section runs past a screen, it has become two.

## Voice earns its place

Character is not the enemy of concision — a line that carries the invariant *and* the failure it
prevents is doing two jobs at once, and is worth its length. What does not earn its place is a
sentence carrying only tone. Keep the one that changes what a reader does; cut the one that only
sets a mood.
