# Tracking

A brief is the smallest current record that lets you, or the next session, resume the outcome
without rebuilding its scope from the conversation. It is execution state, not a second task
tracker and not a log.

## When to keep one

Keep one when the work will cross a context compaction, an interruption, a later session or a
delegated result, or when the owner asks for it. Do not when the work finishes in this turn with a
short checklist, or when an existing tracker already holds the scope, state, next action and proof.
When a ticket or plan exists, link it and keep only the execution state it does not hold; a stale
brief never overrides its source.

## Where it lives

Use the location the owner or repository names, or the working area the environment provides. Never
invent a directory, and keep scratch state out of the repository unless its rules say otherwise.
Name it `YYYY-MM-DD-<outcome>.md`; one brief per outcome, updated in place.

## Shape

```markdown
# <Outcome>

**Status:** active | waiting | blocked
**Source:** <ticket link or the request>
**Last checked:** YYYY-MM-DD HH:MM

## Done when
## Slices
## Decisions
## Next action
## Blocked on
```

Write current facts. **Slices** is the slice list with each one's rung, state and observed proof.
**Decisions** holds only choices a later session must not reopen.

## When to update it

After a slice is proven, a decision changes, a delegation goes out or comes back, a blocker appears,
or before ending a turn with work unfinished — then make **Next action** executable. Do not record
routine tool calls or chronology: they make stale instructions look current.

## Resuming

1. Read the source and the brief; confirm they still describe the same outcome.
2. Reconcile anything newer: a changed requirement, external state, a returned delegation.
3. Check the next action against the actual files and systems before acting.
4. Continue from the first unproven slice.

## Closing

Remove the brief once the outcome's proof passes or the owner cancels it. Move durable decisions and
final evidence to the ticket or project first. A finished brief left behind becomes misleading state.
