import { h, createSSRApp, createApp } from 'vue';
import { Link, Head } from '@inertiajs/vue3';
import PrimeVue from 'primevue/config';
import StyleClass from 'primevue/styleclass';
import ZiggyPlugin from './plugins/ziggy';

export async function setupApp({ App, props, plugin, isServer = false }) {
    const app = isServer
        ? createSSRApp({ render: () => h(App, props) })
        : createApp({ render: () => h(App, props) });

    app.use(plugin);
    app.use(PrimeVue, { unstyled: true });

    app.component('Link', Link);
    app.component('Head', Head);

    app.directive('styleclass', StyleClass);
    app.use(ZiggyPlugin);

    if (!isServer) {
        const { default: ToastService } = await import('primevue/toastservice');
        app.use(ToastService);
        app.config.globalProperties.$route = window.route = route;
    }

    return app;
}
