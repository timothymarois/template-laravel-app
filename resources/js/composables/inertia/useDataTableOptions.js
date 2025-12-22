import { reactive, toRefs, watch, onBeforeUnmount } from 'vue';
import debounce from 'lodash/debounce';
import { router, useForm, usePage } from '@inertiajs/vue3';

const internalOptions = reactive({
    selectAll: false,
    selected: [],
});

const resetSelectionOnPathChange = (state) => {
    if (typeof window === 'undefined') return;

    const page = usePage();
    const currentPath = new URL(page.url, window.location.origin).pathname;
    let nextPath = null;

    router.on('before', (event) => {
        nextPath = new URL(event.detail.visit.url, window.location.origin).pathname;
    });

    onBeforeUnmount(() => {
        if (nextPath && nextPath !== currentPath) {
            state.selectAll = false;
            state.selected = [];
        }
    });
};

const resolveRoute = (routeConfig) => {
    if (typeof routeConfig === 'string') {
        if (routeConfig.startsWith('http') || routeConfig.startsWith('/')) {
            return routeConfig;
        }
        return route(routeConfig);
    }

    if (typeof routeConfig === 'object' && routeConfig.name) {
        return route(routeConfig.name, routeConfig.params || {});
    }

    throw new Error('Invalid route configuration provided to useDataTableOptions.');
};

export function useDataTableOptions(routeConfig, options = {}, config = {}) {

    const {
        only = [],
        preserveState = true,
        preserveScroll = false,
        replace = false,
        debounceMs = 250,
        formOptions = {},
        method = 'get',
    } = config;

    const mergedOnly = only.includes('options') ? only : ['options', ...only];

    const form = useForm({
        search: '',
        perPage: 15,
        sortField: 'name',
        sortOrder: 1,
        filters: {},
        viewFields: [],
        ...options
    });

    const fetchData = () => {
        const url = resolveRoute(routeConfig);
        if (method === 'post') {
            form.post(url, {
                only: mergedOnly,
                preserveScroll,
                preserveState,
                replace,
                ...formOptions
            });
        } else {
            router.get(url, {
                search: form.search,
                perPage: form.perPage,
                sortField: form.sortField,
                sortOrder: form.sortOrder,
                filters: form.filters,
                viewFields: form.viewFields,
                ...formOptions,
            }, {
                only: mergedOnly,
                preserveScroll,
                preserveState,
                replace,
            });
        }
    };

    const debouncedUpdate = debounce(fetchData, debounceMs);

    const resetSelection = () => {
        internalOptions.selectAll = false;
        internalOptions.selected = [];
    };

    watch(() => form.search, debouncedUpdate);
    watch(() => [form.perPage, form.sortField, form.sortOrder], fetchData);
    watch(() => form.viewFields, fetchData, { deep: true });

    watch(() => form.filters, () => {
        fetchData();
        resetSelection();
    }, { deep: true });

    resetSelectionOnPathChange(internalOptions);

    return {
        ...toRefs(form),
        ...toRefs(internalOptions),
        fetchData,
        resetSelection,
        form,
    };
}
