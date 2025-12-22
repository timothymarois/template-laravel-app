import { h, createSSRApp, createApp } from 'vue';
import { Link, Head } from '@inertiajs/vue3';
import ZiggyPlugin from './plugins/inertia/ziggy';

export async function setupApp({ App, props, plugin, isServer = false }) {
    const app = isServer
        ? createSSRApp({ render: () => h(App, props) })
        : createApp({ render: () => h(App, props) });

    app.use(plugin);

    // eslint-disable-next-line vue/no-reserved-component-names
    app.component('Link', Link);
    // eslint-disable-next-line vue/no-reserved-component-names
    app.component('Head', Head);

    app.use(ZiggyPlugin);

    return app;
}
