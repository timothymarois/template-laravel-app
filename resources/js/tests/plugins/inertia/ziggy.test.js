import { afterEach, beforeEach, describe, expect, it } from 'vitest';
import { defineComponent, h, inject } from 'vue';
import { mount } from '@vue/test-utils';
import { Ziggy } from '@/ziggy';
import ZiggyPlugin from '@/plugins/inertia/ziggy';

// These pin the contract the whole app relies on: route() must exist in three
// places, must return a RELATIVE path, and must fail loudly with the route name
// when the generated route list is stale.

const mountWithPlugin = (setup) =>
    mount(defineComponent({ setup, render: () => null }), {
        global: { plugins: [ZiggyPlugin] },
    });

beforeEach(() => {
    globalThis.Ziggy = Ziggy;
});

afterEach(() => {
    delete globalThis.route;
    delete globalThis.Ziggy;
});

describe('ziggy plugin', () => {
    it('exposes route() on globalThis for module-level scripts', () => {
        mountWithPlugin(() => {});

        expect(typeof globalThis.route).toBe('function');
        expect(globalThis.route('login')).toBe('/login');
    });

    it('exposes route via provide/inject', () => {
        let injected;
        mountWithPlugin(() => {
            injected = inject('route');
        });

        expect(typeof injected).toBe('function');
        expect(injected('login')).toBe('/login');
    });

    it('exposes $route as a template global', () => {
        const wrapper = mount(
            defineComponent({ render() { return h('a', { href: this.$route('login') }, 'x'); } }),
            { global: { plugins: [ZiggyPlugin] } },
        );

        expect(wrapper.find('a').attributes('href')).toBe('/login');
    });

    it('returns a relative path by default so the bundle is host-portable', () => {
        // Ziggy itself defaults to absolute:true. The plugin flips it, which is what
        // keeps a built bundle from leaking the build-time host into every href.
        mountWithPlugin(() => {});

        expect(globalThis.route('login')).not.toMatch(/^https?:\/\//);
    });

    it('still returns an absolute URL when explicitly asked', () => {
        mountWithPlugin(() => {});

        expect(globalThis.route('login', undefined, true)).toMatch(/^https?:\/\//);
    });

    it('substitutes route parameters', () => {
        mountWithPlugin(() => {});

        expect(globalThis.route('admin.users.show', 7)).toBe('/admin/users/7');
    });

    it('names the missing route when the generated list is stale', () => {
        // This is the message an agent sees after adding a route without running
        // `php artisan ziggy:generate`. It must name the route, or the cause is a guess.
        mountWithPlugin(() => {});

        expect(() => globalThis.route('route.added.but.not.generated')).toThrow(
            /route 'route\.added\.but\.not\.generated' is not in the route list/,
        );
    });
});
