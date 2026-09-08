import { usePage } from '@inertiajs/vue3';

const toPath = (value: string): string => {
    // Strip an origin by string rather than with `new URL()`: Ziggy's route()
    // returns absolute URLs, and the SSR bundle has no `document` to resolve
    // against.
    const withoutOrigin = value.replace(/^[a-z][a-z0-9+.-]*:\/\/[^/]+/i, '');
    const path = withoutOrigin.split('?')[0].split('#')[0];

    if (path === '') return '/';

    return path.startsWith('/') ? path : `/${path}`;
};

/**
 * Determine whether the current Inertia page is active.
 *
 * Runs identically on the server and the client. It previously bailed out to
 * `false` during SSR because it built a `URL` against `document.baseURI`, which
 * does not exist in the SSR bundle — so every nav item rendered inactive in the
 * server HTML and then flipped on hydration, a visible flash and a class
 * mismatch on every page. `page.url` is already root-relative, so no `URL`
 * construction is needed at all.
 *
 * @param itemPath - Path to compare against the current page URL.
 * @param itemParent - Optional parent path that overrides itemPath.
 * @param eq - When true, requires an exact match instead of a "startsWith" comparison.
 * @returns True if the current page matches the given path.
 */
export const isPageActive = (
    itemPath: string,
    itemParent?: string,
    eq = false
): boolean => {
    const page = usePage();
    const path = itemParent ?? itemPath;

    const currentPath = toPath(page.url ?? '/');
    const routePath = toPath(path);

    return eq ? currentPath === routePath : currentPath.startsWith(routePath);
};

export default isPageActive;
