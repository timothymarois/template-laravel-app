import './bootstrap';
import '../css/app.css'
import { createApp, h } from 'vue'
import { createInertiaApp, Link, Head } from '@inertiajs/vue3'
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { createPinia } from 'pinia'
import piniaPluginPersistedstate from 'pinia-plugin-persistedstate'

import PrimeVue from 'primevue/config';
import Lara from '/resources/presets/lara';
import DefaultLayout from '/resources/js/Layouts/Default.vue';

createInertiaApp({
    resolve: async (name) => {
        const page = resolvePageComponent(
            `./Pages/${name}.vue`,
            (await import.meta.glob("./Pages/**/*.vue", { eager: false }))
        );
        page.then((module) => {
            module.default.layout = module.default.layout != false ? module.default.layout || DefaultLayout : '';
        });
        return page;
    },
    title: title => title ? `${title} - Brand` : 'Brand',
    setup({ el, App, props, plugin }) {
        const pinia = createPinia()
        pinia.use(piniaPluginPersistedstate)
        createApp({ render: () => h(App, props) })
            .use(plugin)
            .use(pinia)
            .use(PrimeVue, {
                unstyled: true,
                pt: Lara
            })
            .component('Link', Link)
            .component('Head', Head)
            .mount(el)
    },
    progress: {
        delay: 25,
        color: '#29d',
        includeCSS: true,
        showSpinner: true,
    },
})
