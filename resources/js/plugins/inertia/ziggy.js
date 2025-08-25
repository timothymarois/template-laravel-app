export default {
    install(app) {
        if (typeof window !== 'undefined' && window.route) {
            app.provide('route', window.route);
            app.config.globalProperties.$route = window.route;
        } else {
            // Provide a no-op fallback in SSR so it doesn't break
            const noop = () => '';
            app.provide('route', noop);
            app.config.globalProperties.$route = noop;
        }
    },
};
