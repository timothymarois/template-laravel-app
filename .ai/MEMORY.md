# Memory - template-laravel-app

## Lessons

- `.template/CHANGELOG.md` is a migration log for forks, not a normal app changelog. Add entries only for changes a fork needs to mirror, and keep Docker-core changes tagged `Docker:`.
- Detailed upgrade steps belong in `.template/migrations/template-vX.Y.Z.md`, not inline in the changelog entry — the changelog stays a terse index and follows the section order in `.template/README.md`.

## Preferences

None.

## Known Traps / Gotchas

- **PhoneNumber preserves a stray non-leading `+`** (`app/Support/PhoneNumber.php`) — `normalize()` only strips a leading `+`, so a `+` placed mid/end of input survives and e.g. `415-555-019+` formats as `(415) 555-019+`. Requires a contrived input that still lands on a 10/11-digit result; edge case, unfixed.
- **`viewFields` scalar param is not cast** (`app/Http/Concerns/InertiaDataTableOptions.php`) — unlike `filters`, a scalar `viewFields` never reaches `Caster::cast`, so it returns 200 with no reproducible crash. Harmless but inconsistent with the filter path.
