# Lessons: backend

## L-BACKEND-1. PhoneNumber keeps a stray non-leading `+`

**What happened.** `app/Support/PhoneNumber.php` `normalize()` strips only a *leading* `+`, so a `+`
placed mid- or end-of-input survives — e.g. `415-555-019+` formats as `(415) 555-019+`.

**Root cause.** The normalizer removes `^\+` rather than all `+` before digit extraction.

**Fix.** Not yet applied — low impact (needs a contrived input that still resolves to a 10/11-digit
result). The correct fix: strip all non-digits (keep at most one leading `+` if E.164 is supported)
before formatting.

**Rule.** Don't assume `PhoneNumber::normalize()` fully sanitizes; strip/validate user-supplied numbers
upstream when correctness matters.

---

## L-BACKEND-2. `viewFields` scalar param is not cast

**What happened.** In `app/Http/Concerns/InertiaDataTableOptions.php` a scalar `viewFields` param never
reaches `Caster::cast` (unlike `filters`), so it comes back untyped. Returns 200 with no reproducible crash.

**Root cause.** The casting path handles `filters` but the `viewFields` branch skips `Caster::cast`.

**Fix.** Route `viewFields` through the same `Caster::cast` path as `filters` when typed values are
needed. Currently harmless, so left as-is.

**Rule.** If you rely on a typed `viewFields`, cast it explicitly — the concern doesn't.
