# Lessons: template-machinery

Lessons about the `.template/` fork-management system (changelog, migrations) that keeps downstream forks
in step with this template.

## L-TEMPLATE-1. `.template/CHANGELOG.md` is a fork migration log, not an app changelog

**What happened.** Entries were written like a normal app changelog — every change, in full — bloating a
file that forks read only to learn what they must mirror.

**Root cause.** `.template/CHANGELOG.md` exists to tell downstream forks what to mirror, not to log all app work.

**Fix.** Add an entry only for changes a fork needs to mirror; tag Docker-core changes `Docker:`; keep
entries terse and in the section order defined by `.template/README.md`.

**Rule.** The changelog is a terse index of fork-relevant changes only.

---

## L-TEMPLATE-2. Detailed upgrade steps go in `.template/migrations/`, not the changelog

**What happened.** Step-by-step upgrade instructions were written inline in changelog entries.

**Root cause.** The changelog is an index; detailed upgrades belong in a versioned migration doc.

**Fix.** Put the steps in `.template/migrations/template-vX.Y.Z.md`; the changelog entry just points to it.

**Rule.** Detailed migration steps live in `.template/migrations/`, never inline in the changelog.
