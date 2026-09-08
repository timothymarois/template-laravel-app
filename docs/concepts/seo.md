# SEO

What is server-rendered, what is not, and the two settings a fork must get right.

## The head has one owner

`resources/js/components/app/SeoHead.vue` renders every head tag. `AppLayout` and `SiteLayout` both
delegate to it, and pass through `title`, `description`, `ogImage`, `ogImageAlt`, `siteName` and
`robots`. It used to be a copy-pasted block in each layout; every fix had to be made twice.

Defaults come from `config/seo.php`, shared to every page by `HandleInertiaRequests` as `seo` — so a
fork fills in one file rather than every page.

## Absolute URLs come from the server, not the browser

`canonical`, `og:url` and `og:image` are built from the **`appUrl` shared prop** (`config('app.url')`),
never from `window.location.origin`. There is no `window` in the SSR bundle, and the SSR render is
exactly the one a crawler or a social scraper reads — building from the browser meant `og:url` shipped
empty and a relative `og:image` shipped unresolvable.

**`APP_URL` must therefore be correct in production.** It is not decoration: it is what every canonical
and social URL is built from, and it is also what `sitemap:generate` writes into `<loc>`.

## The title

Emitted once, by `@inertiaHead`, from the `title` callback in `resources/js/app.js` and
`resources/js/ssr.js` — which appends `VITE_APP_NAME`. The Blade root deliberately has **no** `<title>`:
`@inertiaHead` renders after it and does not de-duplicate, so a hardcoded one won every page.

## Keeping an environment out of the index

`SEO_INDEXABLE=false` sends `X-Robots-Tag: noindex, nofollow` from `SecurityHeaders` on every response.
It defaults to true only in production.

Do **not** use a `robots.txt` `Disallow` for this. That stops crawling, not indexing — a page linked
from anywhere still appears in results, just without a description. `robots.txt` is for crawl budget;
`X-Robots-Tag` and the `robots` meta are for indexing.

Auth and error pages ship `robots="noindex, nofollow"` because they are thin and have no search value.

## The sitemap

`php artisan sitemap:generate` writes `public/sitemap.xml`, which is git-ignored — so **it does not
exist on a fresh deploy** unless something generates it. Schedule it, or add it to the post-deploy
script, and set the `Sitemap:` line in `public/robots.txt` to your domain.

Utility endpoints are excluded by prefix and by route name: `/health` answers 503 whenever a check
fails, so listing it would put a 5xx URL in the sitemap; `/release` is JSON; `_inertia` is the dev-tools
route and appears whenever the command is run locally.

## How it fails

| Symptom | Cause |
|---|---|
| `og:url` / canonical empty, or `og:image` relative | `APP_URL` is unset or wrong. Everything absolute is built from it. |
| Social preview shows a bare card with no image | No `ogImage` on the page and no `SEO_IMAGE` default. |
| Every page has the same title | Something reintroduced a `<title>` in the Blade root; it wins over `@inertiaHead`. |
| Staging shows up in search results | `SEO_INDEXABLE` is true there, or somebody used a `robots.txt` Disallow, which does not prevent indexing. |
| A crawler sees an empty page | The `inertia-ssr` process is not running. `config/inertia.php` sets `throw_on_error => false`, so the app silently falls back to client-only rendering — nothing alerts on this. |
| `/sitemap.xml` 404s in production | It is git-ignored and generated. Nothing generates it by default. |

## Verify a deployment

```sh
curl -sS https://your-host/ | grep -icE '<title'            # expect 1
curl -sS https://your-host/ | grep -oE 'rel="canonical"[^>]*'
curl -sS https://your-host/ | grep -oE 'og:(url|image)"[^>]*'   # expect absolute https URLs
curl -sS https://your-host/ | grep -c '<h1'                 # >0 proves SSR ran
curl -sSI https://your-host/ | grep -i x-robots-tag         # expect nothing in production
```
