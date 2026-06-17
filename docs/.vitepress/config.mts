import { defineConfig } from 'vitepress';

export default defineConfig({
    title: 'Laravel Vue Starter',
    description: 'Production-grade Laravel + Vue + Inertia starter kit documentation',

    // The guides intentionally link to the template's lineage docs under the
    // hidden `.template/` dir (changelog + migration guides). Those files live
    // outside the VitePress site root — they resolve when browsing the repo on
    // GitHub but are not built pages, so exclude only those from dead-link checks.
    ignoreDeadLinks: [/\.template\//],

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
                    { text: 'Logging', link: '/guidelines/logging' },
                    { text: 'Health Checks', link: '/guidelines/health-checks' },
                    { text: 'Tenancy — Using', link: '/guidelines/tenancy-using' },
                    { text: 'Tenancy — Migrating', link: '/guidelines/tenancy-migrating' },
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
