+++
title = "SEO"
subtitle = "the document title, canonical and social tags, the indexing switch and robots"
status = "approved"
goals = false
intent = """
SEO exists so that a crawler or a social scraper reading the server-rendered page finds one title, an
absolute canonical address and an absolute preview image, and so that a staging deployment stays out
of the search index. A page sets only its own title and description; every absolute address comes
from the server.
"""

[[infobox]]
group = "Identity"
rows = [
  { label = "Settings", value = "SEO_SITE_NAME, SEO_DESCRIPTION, SEO_IMAGE, SEO_INDEXABLE", cite = "defaults" },
  { label = "Base address", value = "APP_URL", cite = "appurl" },
]

[[infobox]]
group = "Rules"
rows = [
  { label = "Title", value = "PAGE — APP_NAME", cite = "title" },
  { label = "Indexing", value = "production only, unless SEO_INDEXABLE says otherwise", cite = "indexable" },
  { label = "Sign-in and error pages", value = "never indexed", cite = "noindex-pages" },
]
+++

Every page's head is written by the server from the page's own title and description and the site's
defaults, so the render a crawler reads carries the same tags a browser shows.[^head] The file crawlers
fetch to find every page is described on [Sitemap](seo/sitemap.md).

## Title

A page titled `Login` is shown as `Login — APP_NAME`, and a page with no title as the application name
alone.[^title] The head carries one `<title>`, never two.[^title]

## Addresses

The canonical address and `og:url` are the page's path appended to `APP_URL`, so **`APP_URL` must be the
public address** of the deployment.[^appurl] A preview image given as a path is prefixed with the same
address; one given as a full `http` address is used as it is.[^image] With an image the Twitter card is
`summary_large_image`; without one it is `summary`.[^image]

## Defaults

`SEO_SITE_NAME` is the site name in `og:site_name` and falls back to `APP_NAME`; `SEO_DESCRIPTION` and
`SEO_IMAGE` fill in for a page that sets neither.[^defaults] A page that sets its own description or
image wins over the default.[^head]

## Indexing

Crawlers are allowed to index a deployment only in production, unless `SEO_INDEXABLE` says
otherwise.[^indexable] Everywhere else every response carries `X-Robots-Tag: noindex, nofollow`, which
is what keeps a staging site out of results.[^indexable] A `Disallow` line in `robots.txt` does not do
that: a page a crawler cannot fetch cannot show it the rule, and still appears when another page links
to it.[^google]

The sign-in, registration and password pages carry `noindex, nofollow` and the error pages `noindex`,
whatever the deployment.[^noindex-pages] `robots.txt` asks crawlers to stay out of `/admin`, `/api/`
and `/horizon`, and its `Sitemap:` line is left for the production domain.[^robots]

[^head]: `resources/js/components/app/SeoHead.vue` — renders the canonical link, `robots`,
    `description`, the `og:*` and `twitter:*` tags from its props, falling back to the shared `seo`
    defaults for description and site name; `resources/js/components/site/layout/SiteLayout.vue` and
    `resources/js/components/app/layout/AppLayout.vue` — both pass their props to it.
[^title]: `resources/js/app.js` and `resources/js/ssr.js` — the `title` callback returns
    `` `${title} — ${appName}` `` or `appName`, read from `VITE_APP_NAME`;
    `resources/views/app.blade.php` — carries no `<title>`, leaving it to `@inertiaHead`.
[^appurl]: `app/Http/Middleware/HandleInertiaRequests.php` — `share()` shares `appUrl` as
    `config('app.url')` without a trailing slash; `resources/js/components/app/SeoHead.vue` —
    `canonicalUrl` is `appUrl` plus the page's URL, and `og:url` uses it.
[^image]: `resources/js/components/app/SeoHead.vue` — `absoluteOgImage` keeps an image starting `http`
    and prefixes any other with `appUrl`; `twitter:card` is `summary_large_image` when an image
    resolves and `summary` otherwise.
[^defaults]: `config/seo.php` — `site_name` reads `SEO_SITE_NAME` then `APP_NAME`, `description` reads
    `SEO_DESCRIPTION`, `image` reads `SEO_IMAGE`; `app/Http/Middleware/HandleInertiaRequests.php` —
    `share()` shares them as `seo`.
[^indexable]: `config/seo.php` — `indexable` reads `SEO_INDEXABLE`, and otherwise is true only when
    `APP_ENV` is `production`; `app/Http/Middleware/SecurityHeaders.php` — `handle()` sets
    `X-Robots-Tag: noindex, nofollow` when `seo.indexable` is false.
[^google]: Google Search Central — [Block Search indexing with noindex](https://developers.google.com/search/docs/crawling-indexing/block-indexing):
    `noindex` is set with a meta tag or an HTTP response header, and a page blocked by `robots.txt`
    never shows the crawler the rule and can still appear in results when other pages link to it.
[^noindex-pages]: `resources/js/pages/Login.vue`, `Register.vue`, `ForgotPassword.vue`,
    `ResetPassword.vue` — each passes `robots="noindex, nofollow"`; `resources/js/pages/errors/404.vue`,
    `500.vue`, `503.vue` — each passes `robots="noindex"`.
[^robots]: `public/robots.txt` — `Disallow: /admin`, `Disallow: /api/`, `Disallow: /horizon`, and a
    commented `Sitemap:` line naming `https://example.com/sitemap.xml`.
