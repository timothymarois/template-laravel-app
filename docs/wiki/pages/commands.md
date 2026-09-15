+++
title = "Commands"
subtitle = "the five artisan commands the template adds and the two release scripts"
status = "approved"
goals = false
intent = """
The commands page exists so that an operator at a terminal finds every artisan command the template adds,
and what each one is for, on one screen, with a pointer to each release script. The framework's own
commands are not listed here.
"""

[family]
headings = ["Usage", "Options", "Arguments", "Output", "Exit codes"]
labels = [
  "Command", "Use", "Options", "Run by",
  "Generated password", "Default role", "Default ability", "Default lifetime", "Longest lifetime", "Folder mode",
  "Changes", "Production",
  "Success", "Refused",
]
table = ["Use", "Options", "Changes"]

[[infobox]]
group = "Identity"
rows = [
  { label = "Runner", value = "php artisan", cite = "artisan" },
  { label = "Artisan commands", value = "5", cite = "commands" },
  { label = "Release scripts", value = "2", cite = "scripts" },
]
+++

Five commands sit beside the framework's own, each in its own class under the application's console
commands folder.[^commands] Every one runs through `php artisan`, and `php artisan list` shows them with
the framework's.[^artisan] Each command's page covers its usage, options, arguments, output and exit
codes, in that order.[^commands]

{family-table}

The two release scripts run from the `scripts/` folder rather than through `php artisan`.[^scripts] Each
is documented as a step of the release:

- [Preparation](releases/01-preparation.md): `scripts/prepare-production-release`
- [Publication](releases/03-publication.md): `scripts/publish-production-release`

[^commands]: `app/Console/Commands/` — `CreateUser`, `CreateApiKey`, `GenerateSitemap`, `StartFresh` and
    `EnsureStorage`, each a class extending the framework's `Command` and declaring its usage in
    `$signature`.
[^artisan]: `artisan` — the console entry point at the repository root, which boots the application and
    runs the command named on the command line.
[^scripts]: `scripts/prepare-production-release` and `scripts/publish-production-release` — two Bash
    scripts, each run directly by path.
