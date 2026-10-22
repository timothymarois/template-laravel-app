---
name: managing-development-work
description: Use when taking on, scoping, planning, delivering or finishing development work — a ticket, feature, bug fix, refactor or configuration change — including deciding what actually needs building, cutting down a large or vague request, tracking it across sessions, delegating parts, and deciding when it is done. It supplies the completion contract, the least-work ladder, vertical slicing, a working brief and proof. Do not use it to diagnose a failure of unknown cause, or to write a ticket for people to read.
---

# Manage development work

Deliver the requested outcome with the least new code that fully solves it, and prove it. Be lazy
about the solution and never about the reading: the smallest change is only small once you know
everything it has to reach. This skill coordinates the work; a stack's own skill owns how to write
its code, tests and migrations.

## 1. Take the work in

Turn the request — or the ticket it arrived as — into one contract before choosing anything:

- **Outcome:** what the requester receives. A ticket's Goal is this.
- **Proof:** the observable checks that tell complete from plausible. A ticket's **Done when**
  lines are these; never rewrite them to fit what was built.
- **Scope:** what may change, and what is excluded. A ticket's **Not included** is excluded.
- **Preservation:** behavior, data, compatibility and interfaces that must not change.
- **Authority:** what was asked for, and which later actions still need approval. Investigating,
  planning and implementing are separate authorizations.

Write the outcome and proof in the turn before the first edit, as the head of the slice list below,
even when the work will be done in one go. The requester can correct a wrong contract before any
code exists, and a contract kept only in your head drifts toward whatever got built.

Read the repository rules, then the code, tests and docs the change touches. List every place it
must reach: callers, tests, fixtures, configuration, exports, documentation. That list is the
scope; a feature nobody asked for is not. Ask the owner only when the answer cannot be found and
would change the outcome or the authority — a gap in a ticket goes back as a question, not a guess.

## 2. Find the least work that solves it

Take the first rung that fully works, and record which one and why:

1. **Does it need to exist?** Drop options, flexibility and features the outcome does not need,
   and name them in one line. A vague request gets the smallest version that does the core job.
2. **Is it already here?** Reuse the helper, component, service or pattern the codebase has, the
   way the surrounding code uses it.
3. **Does the language or platform do it?** A standard-library, framework or browser feature
   beats new code; the project's own component beats a native one.
4. **Does an installed dependency do it?** Never add a dependency for a few lines.
5. **Is it a setting or one readable line?** Change the configuration before writing code.
6. **Only then** write the minimum code that works, in the structure the codebase already has.

**Never cut** validation at a trust boundary, handling that prevents data loss, security,
accessibility, compliance (consent, opt-out, Do Not Call) or anything the requester asked for. These
are the lines a smaller version must still carry, because the cost of missing them lands on users.

Shape a large request before cutting it: find what is going wrong today, set what the outcome is
worth, and split a grab-bag ("redesign X", "v2") into separate outcomes, each with its own contract.

## 3. Slice it

Slice vertically: each slice is a thin path that works end to end and can be observed. Never slice
by layer — schema, then API, then interface leaves everything nearly done and nothing usable. Start
with the riskiest core path; check each slice has one outcome, its own proof, and fits one working
session. Mark nice-to-haves `~`; they are cut first. Read [Slicing](references/slicing.md) when a
slice resists splitting or the obvious split looks wrong.

```markdown
Outcome: <what the requester receives>
Proof: <the observable checks; a ticket's Done when lines, unchanged>

1. [x] <outcome> — rung: <2, reuses X> — proof: <observed check>
2. [ ] <outcome> — rung: <6> — proof: <check> — after: 1
~ <nice-to-have, cut first>
```

Write the list in the turn, after the contract and before the first change: a list written afterwards
describes what was built instead of deciding it. Finish and prove one slice — its test passing, its
check observed — before writing code for the next, because a defect found later sits under code that
already depends on it. A slice
waiting on a credential, a service or an owner decision waits as a named slice; never build it on a
guess or behind a stub that pretends to work.

## 4. Choose how much process

- **Direct work** — a contained change with a known cause: a short checklist in the turn, no plan.
- **Bounded implementation** — one coherent outcome inside a known boundary, one implementer.
- **Planned delivery** — three or more dependent steps, several components, staged compatibility,
  or a handoff: write an implementation plan with the planning guidance, one task per slice; this
  skill executes it, integrates each task and proves the whole.
- **Discovery** — one unknown that changes the approach: ask the one question, read only what
  answers it, and stop.

## 5. Keep a brief when the work outlives the turn

When work will cross a compaction, an interruption, a later session or a delegation, keep one short
brief of current state — not a diary — and resume from the first unproven slice, not the last thing
described. Read [Tracking](references/tracking.md) for its shape, where it lives, and when to update
and remove it.

## 6. Build, coordinate and hold the line

- Finish everything the change breaks: callers, tests and fixtures. New logic with a branch, a loop,
  a parser, money or security leaves one small test; trivial changes need none.
- A deliberate shortcut gets a comment naming its limit and when to upgrade, so "later" is findable.
- New scope that surfaces becomes a `~` or a later slice, never part of the slice in progress. When a
  slice runs past double its expected size, stop and re-slice.
- Never infer a refactor from a feature or bug request.
- Add evidence only for a named risk: auth or secrets, money, schema or stored data, production,
  public contracts, concurrency. Read [Coordination](references/coordination.md) for the risk
  responses and for delegating and integrating work.

## 7. Prove it and hand it over

Done means every **Done when** line traced to observed evidence: focused checks and the repository's
gate passed, a representative path produced the expected result, named risks have their proof, and
temporary files and processes are gone. A clean exit status, a started process or "should work" is
not proof.

End with the slice list, each slice naming the existing code it reused and its proof. Then give each
**Done when** line, or each proof line from the contract, the test or command output that met it,
one line each, so the requester checks the claim instead of trusting it. Then one or two lines: what
you skipped or did not check, and any risk the requester must know. A pull request, push or release happens only when asked or required
by the repository, under its GitHub workflow.

Read [Sources](references/sources.md) when auditing or changing a rule here.
