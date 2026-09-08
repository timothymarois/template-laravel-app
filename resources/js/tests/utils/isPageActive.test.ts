import { describe, it, expect, vi } from 'vitest';

const mockUrl = vi.hoisted(() => ({ value: '/' }));

vi.mock('@inertiajs/vue3', () => ({
    usePage: () => ({ get url() { return mockUrl.value; } }),
}));

const { isPageActive } = await import('@/utils/vue/inertia/isPageActive');

const at = (url: string) => { mockUrl.value = url; };

describe('isPageActive', () => {
    it('matches a prefix by default and exactly with eq', () => {
        at('/admin/users');

        expect(isPageActive('/admin')).toBe(true);
        expect(isPageActive('/admin', undefined, true)).toBe(false);
        expect(isPageActive('/admin/users', undefined, true)).toBe(true);
    });

    it('ignores the query string on both sides', () => {
        at('/admin/users?page=2&sort=name');

        expect(isPageActive('/admin/users', undefined, true)).toBe(true);
        expect(isPageActive('/admin/users?page=1', undefined, true)).toBe(true);
    });

    it('accepts an absolute href, as Ziggy route() returns', () => {
        at('/admin/users');

        expect(isPageActive('https://example.test/admin/users', undefined, true)).toBe(true);
        expect(isPageActive('http://other.test/admin/users', undefined, true)).toBe(true);
    });

    it('normalises a href with no leading slash', () => {
        at('/admin');

        expect(isPageActive('admin', undefined, true)).toBe(true);
    });

    it('prefers the parent path when given one', () => {
        at('/admin/components/forms');

        expect(isPageActive('/admin/components/forms', '/admin/components')).toBe(true);
        expect(isPageActive('/nowhere', '/admin/components')).toBe(true);
    });

    it('does not depend on document, so it works under SSR', () => {
        // The previous implementation built a URL against document.baseURI and
        // bailed to false on the server, so every nav item rendered inactive in
        // the SSR HTML and flipped on hydration.
        at('/admin');
        const doc = globalThis.document;

        try {
            // @ts-expect-error - deliberately simulating the SSR global scope.
            delete globalThis.document;
            expect(isPageActive('/admin', undefined, true)).toBe(true);
        } finally {
            globalThis.document = doc;
        }
    });

    it('treats a bare origin as the root path', () => {
        at('/');

        expect(isPageActive('https://example.test', undefined, true)).toBe(true);
    });
});
