# PRD: <system>

> Copy this file to `PRD-<System>.md` and fill it in. A PRD exists only once the system is built and its
> tests are green — being in `PRD/` already means it's the active source of truth. Delete this line.

**Owns:** <the one system/feature this document is the contract for.>

## What this owns

<The behavior this PRD is the source of truth for — in a sentence or two.>

## Does NOT own

- <adjacent behavior that belongs to another PRD> → see [`PRD-<Other>.md`](./PRD-<Other>.md)

## Core contract

<What the system does, stated as settled fact: the model, the guarantees, the invariants, the edge
cases it handles. Present tense — this *is* how it behaves, because tests prove it.>

## Requirements

*Each is a guarantee the tests enforce. Stable IDs — never reused; a deleted requirement's number is
retired, not refilled.*

- **R-<AREA>-1 (<name>).** <the behavior that must hold>
- **R-<AREA>-2 (<name>).** <behavior>

## Tests

*Every requirement maps to the test that proves it. If a row has no test, the requirement isn't real yet.*

| Requirement | Verified by |
|---|---|
| R-<AREA>-1 | `<test file / case name>` |
| R-<AREA>-2 | `<test file / case name>` |
