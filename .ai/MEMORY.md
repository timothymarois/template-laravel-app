# Memory — template-laravel-app

Always-loaded orientation, read at the start of every task: the friction we've hit in **this codebase**
and the workaround for each — so you don't re-hit it.

**This is a living list, not an archive.** When a piece of friction is actually solved — fixed in the
code, or made impossible by a guard or test — **delete its entry**. If it hardens into a permanent "never
do X" rule, move it to `AGENTS.md` and delete it here. Keep this short.

**Scope: this codebase only.** Not user preferences, not how someone likes to work, not cross-project
notes — those live in the agent's own memory, never here.

## Friction / gotchas

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
