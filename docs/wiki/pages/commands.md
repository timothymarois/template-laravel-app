+++
title = "Commands"
subtitle = "the five artisan commands the template adds"
status = "approved"
goals = false
intent = """
The commands page exists so that an operator at a terminal finds every command the template adds, and
what each one is for, on one screen. The framework's own commands are not listed here.
"""

[[infobox]]
group = "Identity"
rows = [
  { label = "Runner", value = "php artisan", cite = "artisan" },
  { label = "Commands", value = "5", cite = "commands" },
]
+++

Five commands sit beside the framework's own, each in its own class under the application's console
commands folder.[^commands] Every one runs through `php artisan`, and `php artisan list` shows them with
the framework's.[^artisan]

| Command | Use |
|---|---|
| [`user:create`](commands/user-create.md) | creates an account from the terminal, as an operator with `--admin`[^user] |
| [`api-key:create`](commands/api-key-create.md) | issues an API key where there is no browser, printing it once[^key] |
| [`sitemap:generate`](commands/sitemap-generate.md) | writes `public/sitemap.xml` from the public routes[^sitemap] |
| [`start:fresh`](commands/start-fresh.md) | clears every cache and rebuilds the database, outside production only[^fresh] |
| [`app:ensure-storage`](commands/app-ensure-storage.md) | creates any missing storage folder, on every container start[^storage] |

[^commands]: `app/Console/Commands/` — `CreateUser`, `CreateApiKey`, `GenerateSitemap`, `StartFresh` and
    `EnsureStorage`, each a class extending the framework's `Command`.
[^artisan]: `artisan` — the console entry point at the repository root, which boots the application and
    runs the command named on the command line.
[^user]: `app/Console/Commands/CreateUser.php` — `$signature` declares `user:create` with `--admin`.
[^key]: `app/Console/Commands/CreateApiKey.php` — `$signature` declares `api-key:create`, and `handle()`
    prints the plaintext once.
[^sitemap]: `app/Console/Commands/GenerateSitemap.php` — `handle()` writes the file to `public_path('sitemap.xml')`.
[^fresh]: `app/Console/Commands/StartFresh.php` — `handle()` refuses in production, then `clearCaches()`
    and `refreshDatabase()`.
[^storage]: `app/Console/Commands/EnsureStorage.php` — `handle()`; `docker/deploy/entrypoint.sh` — runs
    `php artisan app:ensure-storage` at container start.
