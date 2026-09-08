import './bootstrap';
import '../css/app.css';
import 'vue-sonner/style.css';

import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { setupApp } from './setup';

const appName = import.meta.env.VITE_APP_NAME || 'Laravel';

createInertiaApp({
    resolve: (name) => resolvePageComponent(`./pages/${name}.vue`, import.meta.glob('./pages/**/*.vue')),
    title: (title) => (title ? `${title} — ${appName}` : appName),
    setup: async ({ el, App, props, plugin }) => {
        const app = await setupApp({ App, props, plugin });
        app.mount(el);
        return app;
    },
    progress: {
        delay: 25,
        color: '#29d',
        includeCSS: true,
        showSpinner: true,
    },
});
