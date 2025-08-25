import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';

import path from 'path';
import tailwindcss from '@tailwindcss/vite';

import AutoImport from 'unplugin-auto-import/vite';

export default defineConfig({
    plugins: [
        vue(),
        tailwindcss(),
        laravel({
            input: 'resources/js/app.js',
            ssr: 'resources/js/ssr.js',
            refresh: true,
        }),
        AutoImport({
            vueTemplate: true,
            defaultExportByFilename: true,
            dts: true,
            include: [
                /\.vue$/,
                /\.vue\?vue/,
                /\.js$/
            ],
            imports: [
                'vue',
                {
                    '@inertiajs/vue3': ['usePage', 'useForm', 'router'],
                    'primevue': ['useToast'],
                    '@atlas/ui/composables': ['useModal', 'useScroll'],
                    '@atlas/ui/composables/inertia': ['usePageProp', 'useFormSubmit', 'useDataTableOptions'],
                },
            ],
            dirs: [
                './resources/js/composables/',
                './resources/js/utils/'
            ],
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
            '@atlas/ui': path.resolve(__dirname, 'node_modules/@tmarois/atlas-ui/src'),
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
        noExternal: ['@tmarois/atlas-ui'],
    },
});
