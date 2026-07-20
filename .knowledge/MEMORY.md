# Memory — template-laravel-app

Always-loaded, read at the start of every task: the friction we've hit in **this codebase** and the
workaround for each — so you don't re-hit it. **A living list — delete an entry once it's genuinely solved;
a long MEMORY means something was solved and never pruned.** This codebase only.

## Friction / gotchas

*One bullet each: the trap, and the workaround. Delete when it's genuinely solved.*

- **`.template/CHANGELOG.md` is a fork migration log, not an app changelog** — add an entry only for a
  change a fork must mirror; tag Docker-core changes `Docker:`; keep it terse and in the section order
  from `.template/README.md`. Detailed upgrade steps go in `.template/migrations/template-vX.Y.Z.md`,
  never inline in the changelog.
- **`PhoneNumber::normalize()` keeps a stray non-leading `+`** (`app/Support/PhoneNumber.php`) — it
  strips only a *leading* `+`, so `415-555-019+` formats as `(415) 555-019+`. Low-impact edge case; if
  correctness matters, strip all non-digits upstream before formatting.
- **`viewFields` scalar param isn't cast** (`app/Http/Concerns/InertiaDataTableOptions.php`) — unlike
  `filters`, it never reaches `Caster::cast`, so it comes back untyped. Harmless (200, no crash); cast it
  explicitly if you need a typed value.

---
*Editing this file? Follow the standard first: [`guides/docs-memory.md`](./guides/docs-memory.md).*
