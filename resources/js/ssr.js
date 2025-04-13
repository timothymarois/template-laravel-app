import { createInertiaApp } from '@inertiajs/vue3';
import createServer from '@inertiajs/vue3/server';
import { renderToString } from '@vue/server-renderer';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { setupApp } from '../../resources/js/setup';

createServer((page) =>
    createInertiaApp({
        page,
        render: renderToString,
        resolve: (name) => resolvePageComponent(`./pages/${name}.vue`, import.meta.glob('../../resources/js/pages/**/*.vue')),
        title: title => title ?? '',
        setup: async ({ App, props, plugin }) => {
            return await setupApp({ App, props, plugin, isServer: true });
        },
    })
);
