+++
title = "start:fresh"
subtitle = "Clear caches and refresh the database"
status = "approved"
goals = false
intent = """
start:fresh exists so that a developer gets back to a clean application in one command: every cache
emptied and the database rebuilt from its migrations. It must be impossible to run against production.
"""

[[infobox]]
group = "Identity"
rows = [
  { label = "Command", value = "start:fresh", cite = "signature" },
  { label = "Options", value = "--non-interactive", cite = "signature" },
]

[[infobox]]
group = "Rules"
rows = [
  { label = "Production", value = "refused", cite = "production" },
  { label = "Database", value = "dropped and migrated again", cite = "migrate" },
]

[[infobox]]
group = "Exit codes"
rows = [
  { label = "Success", value = "0", cite = "exit" },
  { label = "Refused", value = "1", cite = "exit" },
]
+++

`start:fresh` empties every cache, then drops every table and runs the migrations again.[^flow] It refuses
to run when the application is in production.[^production] Every sample below is copied from the code,
because running the command empties the database it is pointed at.[^migrate]

## Usage

The option `--non-interactive` is declared and read by nothing: the command asks no question either way.[^signature]
```sh
start:fresh [--non-interactive]
php artisan start:fresh
```

## Output

The caches go first: the cache store is flushed, Horizon's cache is cleared, and the application, config,
route and view caches are cleared.[^caches] Then the database is rebuilt with `migrate:fresh --force`, whose
own output appears between the lines below.[^migrate]
```text
Application cache has been cleared.
Refreshing the database...
Database refresh completed successfully!
```

## Exit codes

| Code | Condition | Message |
|---|---|---|
| `0` | the caches were cleared and the database rebuilt[^exit] | `Database refresh completed successfully!` |
| `1` | the application is in production[^production] | `Can not execute this command in production!` |
| `1` | the migration raised an error[^failure] | `Failed to refresh the database: ` and the error's message |

[^signature]: `app/Console/Commands/StartFresh.php` — `$signature` declares `start:fresh` with
    `--non-interactive`, and no method reads the option.
[^flow]: `app/Console/Commands/StartFresh.php` — `handle()` calls `clearCaches()`, then
    `refreshDatabase()`, then prints `Database refresh completed successfully!`.
[^production]: `app/Console/Commands/StartFresh.php` — `handle()` prints
    `Can not execute this command in production!` and returns `Command::FAILURE` when
    `app()->isProduction()`.
[^caches]: `app/Console/Commands/StartFresh.php` — `clearCaches()` calls `Cache::flush()`, `horizon:clear`
    when Horizon is installed, `cache:clear`, `config:clear`, `route:clear` and `view:clear`, then prints
    `Application cache has been cleared.`
[^migrate]: `app/Console/Commands/StartFresh.php` — `refreshDatabase()` prints `Refreshing the database...`
    and calls `migrate:fresh` with `--force`.
[^failure]: `app/Console/Commands/StartFresh.php` — `refreshDatabase()` catches the exception, prints
    `Failed to refresh the database: ` with its message, and `handle()` returns `Command::FAILURE`.
[^exit]: `app/Console/Commands/StartFresh.php` — `handle()` returns `Command::SUCCESS` after the refresh
    and `Command::FAILURE` in production or after a failed refresh.
