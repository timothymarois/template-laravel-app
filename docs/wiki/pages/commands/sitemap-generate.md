+++
title = "sitemap:generate"
subtitle = "Generate the sitemap for public pages"
status = "approved"
goals = false
intent = """
sitemap:generate exists so that a search engine is handed every public page and none of the pages that
need a sign-in, answer in JSON or fail on purpose. The file it writes is not kept in version control, so
a deployment runs it rather than shipping it.
"""

[[infobox]]
group = "Identity"
rows = [
  { label = "Command", value = "sitemap:generate", cite = "signature" },
  { label = "Use", value = "writes public/sitemap.xml from the public routes", cite = "file" },
  { label = "Options", value = "none", cite = "signature" },
]

[[infobox]]
group = "Rules"
rows = [
  { label = "Changes", value = "public/sitemap.xml", cite = "file" },
]

[[infobox]]
group = "Exit codes"
rows = [
  { label = "Success", value = "0", cite = "exit" },
]
+++

`sitemap:generate` writes `public/sitemap.xml` from the routes a visitor can open without signing
in.[^file][^routes] Which routes stay out, and why the file is absent on a fresh deploy, is described on
[Sitemap](../seo/sitemap.md).

## Usage

The command takes no option of its own.[^signature]
```sh
sitemap:generate
php artisan sitemap:generate
```

## Output

The command prints each address it adds, then where it wrote the file.[^output] From a real run on a
development machine, where the home page is the only public route:[^output]
```text
Generating sitemap...
  Added: https://template-laravel-app.test
Sitemap generated at: /Users/marois/Development/Personal/Herd/template-laravel-app/public/sitemap.xml
```

The pages the sitemap lists, and the priority each one carries, are described on [Sitemap](../seo/sitemap.md).

## Exit codes

| Code | Condition | Message |
|---|---|---|
| `0` | the file was written[^exit] | `Sitemap generated at: ` and the file's path |

[^signature]: `app/Console/Commands/GenerateSitemap.php` — `$signature` is `sitemap:generate`, with no
    options or arguments.
[^file]: `app/Console/Commands/GenerateSitemap.php` — `handle()` writes to `public_path('sitemap.xml')`;
    `.gitignore` — lists `/public/sitemap.xml`.
[^routes]: `app/Console/Commands/GenerateSitemap.php` — `addStaticRoutes()` keeps the routes
    `shouldIncludeRoute()` accepts: `GET` routes carrying neither `auth`, `auth:sanctum` nor `guest`
    middleware, outside `$excludePrefixes` and `$excludeNames`.
[^output]: `app/Console/Commands/GenerateSitemap.php` — `handle()` prints `Generating sitemap...` and
    `Sitemap generated at: {$path}`; `addStaticRoutes()` prints `  Added: {$url}` for each route.
[^exit]: `app/Console/Commands/GenerateSitemap.php` — `handle()` returns `Command::SUCCESS` and has no
    other return.
