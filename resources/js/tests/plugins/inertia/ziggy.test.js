import { afterEach, beforeEach, describe, expect, it, vi } from 'vitest';
import { defineComponent, h, inject } from 'vue';
import { mount } from '@vue/test-utils';

// `ziggy` is a Vite alias to vendor/tightenco/ziggy/src/js — delivered by Composer,
// not npm. The Vitest CI job is deliberately node-only (no PHP, no composer, no
// vendor/), so the real library cannot be imported here and is faked.
//
// That also sets the right unit boundary: URL generation belongs to Ziggy and is its
// own tested library. What this file pins is the contract the plugin owns — that the
// helper is reachable three ways, and that it forwards `absolute` and the config.
const ziggyRoute = vi.fn(() => '/login');

vi.mock('ziggy', () => ({ route: (...args) => ziggyRoute(...args) }));

import ZiggyPlugin from '@/plugins/inertia/ziggy';

const mountWithPlugin = (setup = () => {}) =>
    mount(defineComponent({ setup, render: () => null }), {
        global: { plugins: [ZiggyPlugin] },
    });

beforeEach(() => {
    vi.clearAllMocks();
    globalThis.Ziggy = { url: 'https://example.test', routes: {} };
});

afterEach(() => {
    delete globalThis.route;
    delete globalThis.Ziggy;
});

describe('ziggy plugin', () => {
    it('exposes route() on globalThis for module-level scripts', () => {
        mountWithPlugin();

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

    it('asks for a relative URL by default', () => {
        // Ziggy's own default is absolute:true. The plugin flips it, which is what keeps
        // the build-time host out of every href and makes one bundle portable across hosts.
        mountWithPlugin();

        globalThis.route('login');

        expect(ziggyRoute).toHaveBeenCalledWith('login', undefined, false, globalThis.Ziggy);
    });

    it('passes an explicit absolute request straight through', () => {
        mountWithPlugin();

        globalThis.route('login', undefined, true);

        expect(ziggyRoute).toHaveBeenCalledWith('login', undefined, true, globalThis.Ziggy);
    });

    it('forwards route parameters', () => {
        mountWithPlugin();

        globalThis.route('admin.users.show', 7);

        expect(ziggyRoute).toHaveBeenCalledWith('admin.users.show', 7, false, globalThis.Ziggy);
    });

    it('reads the config from globalThis.Ziggy at call time, not at install time', () => {
        // setup.js assigns globalThis.Ziggy; a config captured at install would go stale
        // and, under SSR, could leak one request's config into another.
        mountWithPlugin();
        const replacement = { url: 'https://other.test', routes: {} };
        globalThis.Ziggy = replacement;

        globalThis.route('login');

        expect(ziggyRoute).toHaveBeenCalledWith('login', undefined, false, replacement);
    });

    it('lets an explicit config override the global', () => {
        mountWithPlugin();
        const override = { url: 'https://override.test', routes: {} };

        globalThis.route('login', undefined, false, override);

        expect(ziggyRoute).toHaveBeenCalledWith('login', undefined, false, override);
    });
});
