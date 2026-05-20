import _ from 'lodash';
window._ = _;

import axios from 'axios';
window.axios = axios;

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

// Send WebSocket id with requests so broadcast(...)->toOthers() can identify
// and exclude the sender. No-op when Echo isn't initialized.
window.axios.interceptors.request.use((config) => {
    if (window.Echo?.socketId()) {
        config.headers['X-Socket-Id'] = window.Echo.socketId();
    }
    return config;
});

/**
 * Echo exposes an expressive API for subscribing to channels and listening
 * for events that are broadcast by Laravel. Echo and event broadcasting
 * allow your team to quickly build robust real-time web applications.
 *
 * Set VITE_REVERB_ENABLED=true in .env to enable WebSocket connections.
 */

if (import.meta.env.VITE_REVERB_ENABLED === 'true') {
    import('pusher-js').then((Pusher) => {
        window.Pusher = Pusher.default;

        import('laravel-echo').then((Echo) => {
            window.Echo = new Echo.default({
                broadcaster: 'reverb',
                key: import.meta.env.VITE_REVERB_APP_KEY,
                wsHost: import.meta.env.VITE_REVERB_HOST,
                wsPort: import.meta.env.VITE_REVERB_PORT ?? 80,
                wssPort: import.meta.env.VITE_REVERB_PORT ?? 443,
                forceTLS: (import.meta.env.VITE_REVERB_SCHEME ?? 'https') === 'https',
                enabledTransports: ['ws', 'wss'],
            });
        });
    });
}
