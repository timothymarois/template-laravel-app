import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';

import path from 'path';
import tailwindcss from '@tailwindcss/vite';

import AutoImport from 'unplugin-auto-import/vite';
import Components from 'unplugin-vue-components/vite';

export default defineConfig({
    plugins: [
        vue(),
        tailwindcss(),
        laravel({
            input: 'resources/js/app.js',
            ssr: 'resources/js/ssr.js',
            refresh: true,
        }),
        Components({
            extensions: ['vue','svg'],
            directoryAsNamespace: true,
            collapseSamePrefixes: true,
            dirs: [
                './resources/js/components/',
                './node_modules/atlas-ui/src/components/Volt',
                './node_modules/atlas-ui/src/components'
            ],
            imports: [
                {
                    '@inertiajs/vue3': ['Head', 'Link'],
                },
            ],
            dts: true,
            deep: true
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
                    '@atlas/composables': ['useModal', 'useScroll'],
                    '@atlas/composables/inertia': ['usePageProp', 'useFormSubmit'],
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
        alias: {
            '@': path.resolve(__dirname, 'resources/js'),
            '@components': path.resolve(__dirname, 'resources/js/components'),
            '@atlas': path.resolve(__dirname, 'node_modules/atlas-ui/src'),
            'primevue': path.resolve(__dirname, 'node_modules/primevue'),
            'ziggy': path.resolve(__dirname, 'vendor/tightenco/ziggy/src/js'),
        },
    },
});
