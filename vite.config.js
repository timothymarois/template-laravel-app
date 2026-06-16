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
        // shadcn-vue's bundled primitives push the main chunk past Vite's
        // default 500 kB advisory. Bump so a stock build stays clean.
        chunkSizeWarningLimit: 600,
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
        exclude: ['vue'],
    },
    test: {
        globals: true,
        environment: 'happy-dom',
        include: ['resources/js/tests/**/*.{test,spec}.{js,ts}'],
    },
});
