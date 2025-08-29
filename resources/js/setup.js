import { h, createSSRApp, createApp } from 'vue';
import { Link, Head } from '@inertiajs/vue3';
import PrimeVue from 'primevue/config';
import StyleClass from 'primevue/styleclass';
import Tooltip from 'primevue/tooltip';
import ToastService from 'primevue/toastservice';
import ZiggyPlugin from './plugins/inertia/ziggy';

export async function setupApp({ App, props, plugin, isServer = false }) {
    const app = isServer
        ? createSSRApp({ render: () => h(App, props) })
        : createApp({ render: () => h(App, props) });

    app.use(plugin);
    app.use(ToastService);
    app.use(PrimeVue, { unstyled: true });

    // eslint-disable-next-line vue/no-reserved-component-names
    app.component('Link', Link);
    // eslint-disable-next-line vue/no-reserved-component-names
    app.component('Head', Head);

    app.directive('tooltip', Tooltip);
    app.directive('styleclass', StyleClass);
    app.use(ZiggyPlugin);

    return app;
}
