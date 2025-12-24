import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';

import path from 'path';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    plugins: [
        vue(),
        tailwindcss(),
        laravel({
            input: 'resources/js/app.js',
            ssr: 'resources/js/ssr.js',
            refresh: true,
        }),
    ],
    build: {
        // Enable source maps only in development
        sourcemap: process.env.NODE_ENV !== 'production',
    },
    resolve: {
        dedupe: ['vue'],
        alias: {
            '@': path.resolve(__dirname, 'resources/js'),
            '@components': path.resolve(__dirname, 'resources/js/components'),
            'ziggy': path.resolve(__dirname, 'vendor/tightenco/ziggy/src/js'),
        },
    },
    optimizeDeps: {
        include: ['vuedraggable'],
        exclude: ['vue'],
    },
    ssr: {
        external: ['vuedraggable'],
    },
    test: {
        globals: true,
        environment: 'happy-dom',
        include: ['resources/js/tests/**/*.{test,spec}.{js,ts}'],
    },
});
