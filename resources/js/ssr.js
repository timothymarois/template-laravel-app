import { createInertiaApp } from '@inertiajs/vue3';
import createServer from '@inertiajs/vue3/server';
import { renderToString } from '@vue/server-renderer';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { setupApp } from './setup';

const appName = import.meta.env.VITE_APP_NAME || 'Laravel';

const PORT = Number(process.env.INERTIA_SSR_PORT || '13714');
const HOST = process.env.INERTIA_SSR_HOST || '127.0.0.1';

createServer(
    (page) =>
        createInertiaApp({
            page,
            render: renderToString,
            resolve: (name) => resolvePageComponent(`./pages/${name}.vue`, import.meta.glob('./pages/**/*.vue')),
            title: (title) => (title ? `${title} — ${appName}` : appName),
            setup: async ({ App, props, plugin }) => {
                return await setupApp({ App, props, plugin, isServer: true });
            },
        }),
    PORT,
    HOST
);
