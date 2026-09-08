import { beforeEach, describe, expect, it, vi } from 'vitest';
import { defineComponent, nextTick, reactive } from 'vue';
import { mount } from '@vue/test-utils';

const routerGet = vi.fn();
const routerOn = vi.fn(() => vi.fn());
const pageState = { url: '/admin/users' };

vi.mock('@inertiajs/vue3', () => ({
    // useForm is faked as a plain reactive bag: the composable only reads and writes
    // fields on it, so this keeps the test about the composable rather than Inertia.
    useForm: (initial) => reactive({ ...initial, post: vi.fn() }),
    router: {
        get: (...args) => routerGet(...args),
        post: vi.fn(),
        on: (...args) => routerOn(...args),
    },
    usePage: () => pageState,
}));

import { useDataTableOptions } from '@/composables/inertia/useDataTableOptions';

/** Mounts the composable so onBeforeUnmount has an owning instance. */
function mountComposable(routeConfig = '/admin/users', options = {}, config = {}) {
    let api;
    const wrapper = mount(
        defineComponent({
            setup() {
                api = useDataTableOptions(routeConfig, options, config);

                return () => null;
            },
        }),
    );

    return { api, wrapper };
}

beforeEach(() => {
    vi.clearAllMocks();
    globalThis.route = vi.fn((name, params) => `/resolved/${name}${params ? `/${JSON.stringify(params)}` : ''}`);
});

describe('useDataTableOptions — defaults', () => {
    it('defaults sortField to id, not a guessed column name', () => {
        // A default naming a column the model lacks is a 500 on first load.
        const { api } = mountComposable();

        expect(api.sortField.value).toBe('id');
        expect(api.perPage.value).toBe(15);
        expect(api.sortOrder.value).toBe(1);
        expect(api.search.value).toBe('');
    });

    it('lets caller options override the defaults', () => {
        const { api } = mountComposable('/x', { perPage: 50, sortField: 'name', sortOrder: -1 });

        expect(api.perPage.value).toBe(50);
        expect(api.sortField.value).toBe('name');
        expect(api.sortOrder.value).toBe(-1);
    });

    it('starts with an empty selection', () => {
        const { api } = mountComposable();

        expect(api.selectAll.value).toBe(false);
        expect(api.selected.value).toEqual([]);
    });
});

describe('useDataTableOptions — filters coercion', () => {
    it('replaces an array filters value with an object', () => {
        // PHP serializes an empty `filters` as JSON `[]`. Left as an Array, a later
        // `filters.foo = 1` adds a non-numeric prop that JSON.stringify drops, so the
        // request silently loses every filter.
        const { api } = mountComposable('/x', { filters: [] });

        expect(Array.isArray(api.filters.value)).toBe(false);
        expect(api.filters.value).toEqual({});
    });

    it('keeps a genuine filters object', () => {
        const { api } = mountComposable('/x', { filters: { status: 'active' } });

        expect(api.filters.value).toEqual({ status: 'active' });
    });

    it.each([null, undefined, 'nope', 42])('falls back to {} for %s', (value) => {
        const { api } = mountComposable('/x', { filters: value });

        expect(api.filters.value).toEqual({});
    });

    it('survives a mutation after coercion', () => {
        const { api } = mountComposable('/x', { filters: [] });

        api.filters.value.status = 'active';

        expect(JSON.parse(JSON.stringify(api.filters.value))).toEqual({ status: 'active' });
    });
});

describe('useDataTableOptions — selection', () => {
    it('resetSelection clears both fields', () => {
        const { api } = mountComposable();

        api.selectAll.value = true;
        api.selected.value = [{ id: 1 }];
        api.resetSelection();

        expect(api.selectAll.value).toBe(false);
        expect(api.selected.value).toEqual([]);
    });

    it('clears the selection when the search term changes', async () => {
        // Search narrows the visible set, so a prior select-all must not bulk-act on
        // a scope the user can no longer see.
        const { api } = mountComposable();

        api.selected.value = [{ id: 1 }];
        api.search.value = 'ada';
        await nextTick();

        expect(api.selected.value).toEqual([]);
    });

    it('clears the selection when a filter changes', async () => {
        const { api } = mountComposable('/x', { filters: { status: 'active' } });

        api.selected.value = [{ id: 1 }];
        api.filters.value.status = 'archived';
        await nextTick();

        expect(api.selected.value).toEqual([]);
    });
});

describe('useDataTableOptions — fetching', () => {
    it('always requests the options partial', async () => {
        const { api } = mountComposable('/admin/users', {}, { only: ['users'] });

        api.fetchData();
        await nextTick();

        expect(routerGet).toHaveBeenCalledTimes(1);
        expect(routerGet.mock.calls[0][2].only).toEqual(['options', 'users']);
    });

    it('does not duplicate options when the caller already asked for it', () => {
        const { api } = mountComposable('/admin/users', {}, { only: ['options', 'users'] });

        api.fetchData();

        expect(routerGet.mock.calls[0][2].only).toEqual(['options', 'users']);
    });

    it('sends every query field the server needs', () => {
        const { api } = mountComposable('/admin/users', { filters: { status: 'active' } });

        api.fetchData();

        expect(routerGet.mock.calls[0][1]).toMatchObject({
            search: '',
            perPage: 15,
            sortField: 'id',
            sortOrder: 1,
            filters: { status: 'active' },
            viewFields: [],
        });
    });

    it('refetches when perPage, sortField or sortOrder change', async () => {
        const { api } = mountComposable();

        api.perPage.value = 50;
        await nextTick();

        expect(routerGet).toHaveBeenCalledTimes(1);
    });

    it('debounces a search change rather than firing per keystroke', async () => {
        vi.useFakeTimers();
        const { api } = mountComposable();

        api.search.value = 'a';
        await nextTick();
        api.search.value = 'ad';
        await nextTick();
        api.search.value = 'ada';
        await nextTick();

        expect(routerGet).not.toHaveBeenCalled();
        vi.advanceTimersByTime(300);
        expect(routerGet).toHaveBeenCalledTimes(1);
        vi.useRealTimers();
    });

    it('posts instead of getting when method is post', () => {
        const { api } = mountComposable('/admin/users', {}, { method: 'post' });

        api.fetchData();

        expect(routerGet).not.toHaveBeenCalled();
        expect(api.form.post).toHaveBeenCalledTimes(1);
    });
});

describe('useDataTableOptions — route resolution', () => {
    it.each(['/admin/users', 'https://example.test/admin/users'])('uses %s verbatim', (url) => {
        const { api } = mountComposable(url);

        api.fetchData();

        expect(routerGet.mock.calls[0][0]).toBe(url);
    });

    it('resolves a bare name through Ziggy', () => {
        const { api } = mountComposable('admin.users.index');

        api.fetchData();

        expect(globalThis.route).toHaveBeenCalledWith('admin.users.index');
        expect(routerGet.mock.calls[0][0]).toBe('/resolved/admin.users.index');
    });

    it('resolves a name and params object through Ziggy', () => {
        const { api } = mountComposable({ name: 'admin.users.show', params: { user: 3 } });

        api.fetchData();

        expect(globalThis.route).toHaveBeenCalledWith('admin.users.show', { user: 3 });
    });

    it('throws on an unusable route configuration', () => {
        const { api } = mountComposable({});

        expect(() => api.fetchData()).toThrow(/Invalid route configuration/);
    });
});

describe('useDataTableOptions — listener lifetime', () => {
    it('deregisters its router listener on unmount', () => {
        // router.on returns a deregistration callback; without calling it every table
        // in a long-lived SPA session leaks a listener.
        const unsubscribe = vi.fn();
        routerOn.mockReturnValueOnce(unsubscribe);

        const { wrapper } = mountComposable();
        wrapper.unmount();

        expect(unsubscribe).toHaveBeenCalledTimes(1);
    });
});
