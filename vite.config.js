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
        sourcemap: true,
    },
    resolve: {
        dedupe: ['vue'],
        alias: {
            '@': path.resolve(__dirname, 'resources/js'),
            '@components': path.resolve(__dirname, 'resources/js/components'),
            'primevue': path.resolve(__dirname, 'node_modules/primevue'),
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
});
