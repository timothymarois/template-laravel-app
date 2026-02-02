import { defineConfig } from 'vitepress';

export default defineConfig({
    title: 'Laravel Vue Starter',
    description: 'Production-grade Laravel + Vue + Inertia starter kit documentation',

    head: [
        ['link', { rel: 'icon', type: 'image/svg+xml', href: '/logo.svg' }],
    ],

    themeConfig: {
        logo: '/logo.svg',

        nav: [
            { text: 'Home', link: '/' },
            { text: 'Docs', link: '/getting-started/introduction' },
            { text: 'Guidelines', link: '/guidelines/laravel' },
        ],

        sidebar: [
            {
                text: 'Getting Started',
                items: [
                    { text: 'Introduction', link: '/getting-started/introduction' },
                    { text: 'Installation', link: '/getting-started/installation' },
                    { text: 'Configuration', link: '/getting-started/configuration' },
                ],
            },
            {
                text: 'Architecture',
                items: [
                    { text: 'Overview', link: '/architecture/overview' },
                    { text: 'Tech Stack', link: '/architecture/tech-stack' },
                    { text: 'Components', link: '/architecture/components' },
                ],
            },
            {
                text: 'Guidelines',
                items: [
                    { text: 'Laravel', link: '/guidelines/laravel' },
                    { text: 'Vue', link: '/guidelines/vue' },
                    { text: 'Testing', link: '/guidelines/testing' },
                ],
            },
        ],

        socialLinks: [
            { icon: 'github', link: 'https://github.com/timothymarois/template-laravel-app' },
        ],

        search: {
            provider: 'local',
        },

        footer: {
            message: 'Built with VitePress',
        },
    },
});
