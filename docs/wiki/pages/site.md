+++
title = "Public site"
subtitle = "the home page, the error pages, server rendering, response headers, colour scheme and analytics"
status = "approved"
goals = false
intent = """
The public site exists so that a visitor, and a crawler, receives a complete page from the server on
the first request, in the site's own layout even when the page is missing or the application is down.
The headers every response carries keep the site out of other sites' frames and out of the search
index where it should not be found.
"""

[[infobox]]
group = "Identity"
rows = [
  { label = "Home page", value = "/", cite = "home" },
  { label = "Error pages", value = "404, 500, 503", cite = "errors" },
  { label = "Renderer", value = "inertia:start-ssr on port 13714", cite = "ssr" },
]

[[infobox]]
group = "Rules"
rows = [
  { label = "Failed render", value = "the browser renders instead", cite = "fallback" },
  { label = "Framing", value = "same origin only", cite = "headers" },
  { label = "Analytics", value = "only with GOOGLE_ANALYTICS_ID", cite = "analytics" },
]
+++

The public site is the home page and the three error pages, rendered on the server before the browser
takes over.[^home][^ssr] What a crawler reads in the head of each page is described on [SEO](seo.md).

## Pages

The address `/` shows the home page.[^home] A missing address answers `404` with the page titled
`Page Not Found`, a failure answers `500` with `Server Error`, and maintenance answers `503` with
`Service Unavailable`; each keeps the site's layout and the first two offer a `Go Home` button.[^errors]
A request under `/api/` gets the bare status instead of a page.[^errors] The indexing of the three error
pages is described on [SEO](seo.md).

## Server rendering

Each page is rendered by a Node process the container runs alongside the web server, reached on port
13714.[^ssr] When that render fails, the browser renders the page itself and the failure is not
raised, which Inertia recommends for production.[^fallback] `INERTIA_SSR_ENABLED=false` switches the
server render off.[^ssr]

## Headers

Every response carries `X-Frame-Options: SAMEORIGIN`, `X-Content-Type-Options: nosniff`,
`Referrer-Policy: strict-origin-when-cross-origin` and a `Permissions-Policy` that refuses the camera,
microphone and geolocation.[^headers] In production it also carries
`Strict-Transport-Security` for a year, including subdomains.[^headers] A deployment that must stay
out of the search index adds `X-Robots-Tag`, described on [SEO](seo.md).

## Colour scheme

Before the page paints, a script applies the visitor's saved scheme, or the system's dark preference
when nothing is saved, so a dark page never flashes light.[^scheme]

## Analytics and sockets

The Google tag is emitted only when `GOOGLE_ANALYTICS_ID` holds a measurement id; empty, no script
is sent.[^analytics] The browser opens a WebSocket connection to Reverb only when
`VITE_REVERB_ENABLED` is `true` at build time.[^reverb]

[^home]: `routes/web.php` — the `home` route; `app/Http/Controllers/PageController.php` — `home()`
    renders the `Index` page.
[^errors]: `bootstrap/app.php` — `withExceptions()` renders `errors/404`, `errors/500` and
    `errors/503` with that status, and returns the plain response for a request matching `api/*`;
    `resources/js/pages/errors/404.vue`, `500.vue`, `503.vue` — each sets its title on the site layout,
    and the first two link `/` with the label `Go Home`.
[^ssr]: `config/inertia.php` — `ssr.enabled` reads `INERTIA_SSR_ENABLED`, true when unset, and
    `ssr.url` defaults to `http://127.0.0.1:13714`; `docker/config/supervisord.conf` — the
    `inertia-ssr` program runs `artisan inertia:start-ssr`; `resources/js/ssr.js` — the server
    listens on `INERTIA_SSR_PORT`, `13714` when unset.
[^fallback]: `config/inertia.php` — `ssr.throw_on_error` reads `INERTIA_SSR_THROW_ON_ERROR`, false
    when unset; Inertia — [Server-side rendering](https://inertiajs.com/server-side-rendering): when
    SSR rendering fails Inertia falls back to client-side rendering, and `throw_on_error` is not
    recommended for production.
[^headers]: `app/Http/Middleware/SecurityHeaders.php` — `handle()` sets the four headers on every
    response and `Strict-Transport-Security: max-age=31536000; includeSubDomains` when the
    application is in production; `bootstrap/app.php` — `withMiddleware()` appends it to the `web`
    group.
[^scheme]: `resources/views/app.blade.php` — the inline script reads `vueuse-color-scheme` from local
    storage and `prefers-color-scheme: dark`, and adds the `dark` class before the page paints.
[^analytics]: `config/services.php` — `google_analytics.measurement_id` reads `GOOGLE_ANALYTICS_ID`;
    `resources/views/app.blade.php` — the `gtag.js` block is inside `@if ($gaId = …)`.
[^reverb]: `resources/js/bootstrap.js` — Echo and Pusher are imported only when
    `import.meta.env.VITE_REVERB_ENABLED === 'true'`.
