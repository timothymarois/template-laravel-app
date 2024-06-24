import './bootstrap';
import '../css/app.css'
import 'primeicons/primeicons.css'
import { createApp, h } from 'vue'
import { createInertiaApp, Link, Head } from '@inertiajs/vue3'
// import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { createPinia } from 'pinia'
import { createPersistedState } from 'pinia-plugin-persistedstate'

import PrimeVue from 'primevue/config';
import Lara from '/resources/presets/lara';

createInertiaApp({
    resolve: async (name) => {

        const pages = import.meta.glob("./Pages/**/*.vue");
        const page = await pages[`./Pages/${name}.vue`]();

        let layoutName = 'Default';
        if (page.default?.props?.layout && typeof page.default.props.layout === 'string') {
            layoutName = page.default.props.layout;
        }

        const layout = await import(`./Layouts/${layoutName}.vue`);
        page.default.layout = layout.default;

        return page;
    },
    title: title => title ? `${title} - Brand` : 'Brand',
    setup({ el, App, props, plugin }) {

        const pinia = createPinia()

        pinia.use(createPersistedState({
            storage: localStorage,
        }))

        const app = createApp({ render: () => h(App, props) })
            .use(plugin)
            .use(pinia)
            .use(PrimeVue, {
                ripple: true,
                unstyled: true,
                pt: Lara,
                components: {
                    exclude: [
                        "Editor",
                        "Chart"
                    ]
                }
            })
            .component('Link', Link)
            .component('Head', Head);

        app.config.globalProperties.route = window.route = route;
        app.mount(el);
        return app;
    },
    progress: {
        delay: 25,
        color: '#29d',
        includeCSS: true,
        showSpinner: true,
    },
})
