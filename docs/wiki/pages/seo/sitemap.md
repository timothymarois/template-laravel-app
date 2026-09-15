+++
title = "Sitemap"
subtitle = "the sitemap.xml file, which addresses it lists, and when it exists"
status = "approved"
goals = false
intent = """
The sitemap exists so that a crawler learns every public page from one file, and never learns an
address that signs in, fails or answers with data instead of a page. It exists only once it has been
generated on the deployment that serves it.
"""

[[infobox]]
group = "Identity"
rows = [
  { label = "File", value = "public/sitemap.xml", cite = "write" },
  { label = "Command", value = "sitemap:generate", cite = "write" },
]

[[infobox]]
group = "Rules"
rows = [
  { label = "Listed", value = "public GET pages with a fixed address", cite = "include" },
  { label = "Left out", value = "signed-in, guest-only and utility addresses", cite = "exclude" },
  { label = "Fresh deployment", value = "no file until the command runs", cite = "absent" },
]
+++

`sitemap:generate` writes `public/sitemap.xml` from the application's routes, listing every public
page and leaving out every address a crawler should not visit.[^write] The command's options and output
are described on [sitemap:generate](../commands/sitemap-generate.md), and the tags in each page's head on
[SEO](../seo.md).

## Contents

A route is listed when it answers `GET`, needs no sign-in, is not reserved for signed-out visitors, and
has a fixed address with no placeholder in it.[^include] The home page carries priority `1.0` and every
other page `0.8`, all marked as changing weekly.[^include] Each address is built on `APP_URL`.[^include]

| Left out | Reason |
|---|---|
| `/admin…`, `/api…`, `/horizon…`, `/auth…`, `/broadcasting…`, `/sanctum…`, `/storage…` | signed-in or machine addresses[^exclude] |
| `/login`, `/register`, `/logout`, the password pages | sign-in screens, thin and useless in results[^exclude] |
| `/up`, `/health`, `/release` | `/health` answers `503` whenever a check fails, and `/release` is data[^exclude] |
| `/_inertia…`, `/_debugbar…` | development tooling present only on a developer machine[^exclude] |
| any address holding `{…}` | an address template, not a page[^include] |

With no page beyond the home page, a run on a developer machine wrote:[^run]

```xml
<?xml version="1.0" encoding="UTF-8"?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:xhtml="http://www.w3.org/1999/xhtml" xmlns:image="http://www.google.com/schemas/sitemap-image/1.1" xmlns:video="http://www.google.com/schemas/sitemap-video/1.1" xmlns:news="http://www.google.com/schemas/sitemap-news/0.9">
    <url>
    <loc>https://template-laravel-app.test</loc>
    <changefreq>weekly</changefreq>
    <priority>1.0</priority>
            </url>
</urlset>
```

## Existence

The file is not kept in version control and nothing runs the command on a schedule or at deployment,
so **a fresh deployment answers `404` at `/sitemap.xml`** until the command has run there.[^absent] A
product with pages beyond fixed routes lists them by extending the command's dynamic section.[^dynamic]

[^write]: `app/Console/Commands/GenerateSitemap.php` — `handle()` builds the sitemap and writes it to
    `public_path('sitemap.xml')`.
[^include]: `app/Console/Commands/GenerateSitemap.php` — `shouldIncludeRoute()` keeps `GET` routes
    without `auth`, `auth:sanctum` or `guest` middleware; `addStaticRoutes()` skips a URI containing
    `{`, builds each address with `url()` and sets weekly change frequency and priority `1.0` for `/`
    and `0.8` otherwise.
[^exclude]: `app/Console/Commands/GenerateSitemap.php` — `$excludePrefixes` and `$excludeNames`, read
    by `shouldIncludeRoute()`, with the comment on `health`, `release` and `_inertia`.
[^run]: `app/Console/Commands/GenerateSitemap.php` — `handle()`; the sample is the file
    `php artisan sitemap:generate` wrote on a developer machine whose `APP_URL` is
    `https://template-laravel-app.test`, so the address differs on another.
[^absent]: `.gitignore` — `/public/sitemap.xml`; `bootstrap/app.php` — `withSchedule()` schedules
    only the three `health:` commands; `docker/deploy/post-deployment.sh` — does not run
    `sitemap:generate`.
[^dynamic]: `app/Console/Commands/GenerateSitemap.php` — `addDynamicPages()` is empty apart from
    commented examples for products, posts and categories.
