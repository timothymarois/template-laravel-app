# Migrating a fork to template v5.3.1

v5.3.1 makes the `storage/framework/testing/.gitignore` placeholder an explicit
fork sync item. The template already tracks the file; forks that missed it can
end up with `php artisan app:ensure-storage` creating an untracked setup file.

## Prerequisites

- Fork is on template **v5.3.0**. Check: `jq -r .version template-manifest.json` -> `5.3.0`.
- `php artisan app:ensure-storage` exists in the fork.

---

## Part A - Add the missing storage placeholder

Create `storage/framework/testing/.gitignore` if it is missing:

```gitignore
*
!.gitignore
```

This matches the ignore-all placeholders used by the other runtime storage
directories and keeps test artifacts ignored while preserving the directory in
git.

## Verify

```sh
php artisan app:ensure-storage
git status --short storage/framework/testing
```

The status command should print nothing.

## Finally

Bump the fork's `template-manifest.json` -> `"version": "5.3.1"`. Don't copy this
changelog into the fork; the manifest `version` is the record.
