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
        // `ziggy` lives in vendor/ (Composer, not npm). The unit-test job runs without
        // PHP, so the specifier cannot resolve there — point it at a stub for tests only.
        alias: {
            ziggy: path.resolve(__dirname, 'resources/js/tests/stubs/ziggy.js'),
        },
        globals: true,
        environment: 'happy-dom',
        setupFiles: ['resources/js/tests/setup.js'],
        include: ['resources/js/tests/**/*.{test,spec}.{js,ts}'],
    },
});
