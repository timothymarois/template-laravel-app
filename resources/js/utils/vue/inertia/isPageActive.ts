import { usePage } from '@inertiajs/vue3';
import { isClient } from '../../browser';

/**
 * Determine whether the current Inertia page is active.
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
    if (!isClient) return false;

    const page = usePage();
    const path = itemParent ?? itemPath;
    const currentPath = new URL(page.url, document.baseURI).pathname;
    const routePath = new URL(path, document.baseURI).pathname;

    return eq ? currentPath === routePath : currentPath.startsWith(routePath);
};

export default isPageActive;
