import { defineConfig } from 'vite'
import laravel from 'laravel-vite-plugin'
import vue from '@vitejs/plugin-vue'

import AutoImport from 'unplugin-auto-import/vite'
import Components from 'unplugin-vue-components/vite';
import { PrimeVueResolver } from 'unplugin-vue-components/resolvers'

export default defineConfig({
    plugins: [
        laravel({
            input: 'resources/js/app.js',
            refresh: true,
        }),
        vue({
            template: {
                transformAssetUrls: {
                    base: null,
                    includeAbsolute: false,
                },
            },
        }),
        Components({
            extensions: ['vue','svg'],
            directoryAsNamespace: true,
            collapseSamePrefixes: true,
            dirs: [
                './resources/js/components/',
            ],
            imports: [
                {
                    '@inertiajs/vue3': ['Head', 'Link'],
                },
            ],
            dts: true,
            deep: true,
            resolvers: [
                PrimeVueResolver()
            ]
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
                    '@inertiajs/vue3': [
                        'usePage',
                        'router',
                        'useForm',
                    ],
                    '@vueuse/core': [
						'useStorage',
					],
                },
            ],
            dirs: [
                './resources/js/composables/',
                './resources/js/utils/',
            ],
        }),
    ],
    build: {
        sourcemap: true,
    },
    resolve: {
        alias: {
            '@': '/',
        },
    },
})
