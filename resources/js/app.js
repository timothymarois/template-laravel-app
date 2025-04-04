import './bootstrap';
import '../css/app.css'
import 'primeicons/primeicons.css'

import { createApp, h } from 'vue'
import { createInertiaApp, Link, Head } from '@inertiajs/vue3'
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';

import StyleClass from "primevue/styleclass";
import PrimeVue from 'primevue/config';
import ToastService from 'primevue/toastservice';

createInertiaApp({
    resolve: (name) => resolvePageComponent(`./pages/${name}.vue`, import.meta.glob('./pages/**/*.vue')),
    title: title => title ? `${title}` : '',
    setup({ el, App, props, plugin }) {

        const app = createApp({ render: () => h(App, props) })
            .use(plugin)
            .use(PrimeVue, {
                unstyled: true
            })
            .use(ToastService)
            .component('Link', Link)
            .component('Head', Head);

        app.directive("styleclass", StyleClass);
        app.config.globalProperties.$route = window.route = route;
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
