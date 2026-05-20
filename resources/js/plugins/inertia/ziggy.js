import { route as ziggyRoute } from 'ziggy';

// Bundles Ziggy's `route()` helper using the route config loaded from
// `globalThis.Ziggy` (set in `setup.js` from the generated `./ziggy` file).
// Because both client and SSR bundles include the library and config, route()
// produces real URLs in BOTH environments — server-rendered links carry full
// hrefs for crawlers, not empty placeholders.
//
// Exposed three ways so callers can use whichever ergonomic suits them:
//   - `globalThis.route(...)`  → bare `route(name, params)` in module-level
//                                 scripts (composables, computeds, utils).
//   - `inject('route')`         → composition-API injection.
//   - `$route` global property  → `$route(name, params)` in Vue templates.
//
// `absolute` defaults to `false` so callers always get a relative path; the
// browser resolves it against the current origin. This makes the bundle
// portable across hosts and avoids leaking the build-time `Ziggy.url`.
export default {
    install(app) {
        const fn = (name, params, absolute = false, config) =>
            ziggyRoute(name, params, absolute, config ?? globalThis.Ziggy);
        globalThis.route = fn;
        app.provide('route', fn);
        app.config.globalProperties.$route = fn;
    },
};
