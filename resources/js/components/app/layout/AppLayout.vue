<template>
    <Head :title="title">
        <!-- Standard meta -->
        <meta v-if="description" name="description" :content="description" />

        <!-- Open Graph (Facebook, LinkedIn, etc.) -->
        <meta v-if="title" property="og:title" :content="title" />
        <meta v-if="description" property="og:description" :content="description" />
        <meta v-if="ogImage" property="og:image" :content="absoluteOgImage" />
        <meta v-if="ogImage" property="og:image:alt" :content="ogImageAlt || title" />
        <meta property="og:type" content="website" />
        <meta property="og:url" :content="canonicalUrl" />
        <meta v-if="siteName" property="og:site_name" :content="siteName" />

        <!-- Twitter/X -->
        <meta name="twitter:card" :content="ogImage ? 'summary_large_image' : 'summary'" />
        <meta v-if="title" name="twitter:title" :content="title" />
        <meta v-if="description" name="twitter:description" :content="description" />
        <meta v-if="ogImage" name="twitter:image" :content="absoluteOgImage" />
        <meta v-if="ogImage" name="twitter:image:alt" :content="ogImageAlt || title" />
    </Head>
    <AppShell
        :isSideNav="true"
        :pageTitle="pageTitle"
        :pageTabs="pageTabs"
        :pageNavItems="pageNavItems"
        :pageSidebarItems="pageSidebarItems"
        :pageSidebarTitle="pageSidebarTitle"
        :pageUrl="$page.url"
        :sideBarItems="sideBarItems"
        :topBarItems="topBarItems"
        :linkComponent="'Link'"
        sideBarActiveClass="!bg-white !text-black dark:!bg-zinc-800 dark:!text-white"
        :noScroll="noScroll"
        :containerClass="containerClass"
        widthClass="w-full"
    >
        <template #navActions>
            <ModeToggle />
            <ProfileMenu
                :user="user"
                :items="profileMenuItems"
                :avatar-only="true"
                headerLink="/"
                linkComponent="Link"
            />
        </template>
        <!-- <template #appTopBar>
            <div class="px-4 w-full flex items-center justify-between">
                <div class="w-[400px]">
                    <InputText placeholder="Search for contacts" size="small" fluid />
                </div>
                <div>
                    <ProfileMenu
                        :user="user"
                        :items="profileMenuItems"
                        :avatar-only="true"
                        headerLink="/"
                        linkComponent="Link"
                    />
                </div>
            </div>
        </template> -->
        <template #pageSideContent>
            <slot name="pageSideContent" />
        </template>
        <template #headerTitle>
            <slot name="headerTitle" />
        </template>
        <template #headerAction>
            <slot name="headerAction" />
        </template>
        <template #default>
            <slot />
        </template>
        <template #footer>
            <slot name="footer" />
        </template>
        <template #footerAction>
            <slot name="footerAction" />
        </template>
        <template #modals>
            <EditUserModal />
            <DeleteUserModal />
        </template>
    </AppShell>
</template>

<script setup>
import { computed } from 'vue';
import { Head, usePage } from '@inertiajs/vue3';
import { IconUser, IconColorFilter, IconHome } from '@tabler/icons-vue';
import { Gauge, ScrollText, LogOut } from 'lucide-vue-next';
import AppShell from './AppShell.vue';
import ProfileMenu from '../navigation/ProfileMenu.vue';
import ModeToggle from '../navigation/ModeToggle.vue';
import EditUserModal from '../modals/EditUserModal.vue';
import DeleteUserModal from '../modals/DeleteUserModal.vue';

const page = usePage();
const user = page.props.user;

const props = defineProps({
    title: {
        type: String,
        default: 'Home'
    },
    description: {
        type: String,
        default: ''
    },
    ogImage: {
        type: String,
        default: ''
    },
    ogImageAlt: {
        type: String,
        default: ''
    },
    siteName: {
        type: String,
        default: ''
    },
    pageTitle: {
        type: String,
        default: 'Home'
    },
    pageTabs: {
        type: Array,
        default: () => []
    },
    pageNavItems: {
        type: Array,
        default: () => [],
    },
    pageSidebarItems: {
        type: Array,
        default: () => [],
    },
    pageSidebarTitle: {
        type: String,
        default: '',
    },
    containerClass: {
        type: String,
        default: 'm-auto p-4'
    },
    noScroll: {
        type: Boolean,
        default: false
    }
});

const canonicalUrl = computed(() => {
    if (typeof window === 'undefined') return '';
    return window.location.origin + page.url;
});

const absoluteOgImage = computed(() => {
    if (!props.ogImage) return '';
    if (props.ogImage.startsWith('http')) return props.ogImage;
    if (typeof window === 'undefined') return props.ogImage;
    return window.location.origin + (props.ogImage.startsWith('/') ? '' : '/') + props.ogImage;
});

const profileMenuItems = computed(() => [
    { separator: true },
    { label: 'Horizon', icon: Gauge, href: '/horizon', external: true },
    { label: 'Log Viewer', icon: ScrollText, href: '/log-viewer', external: true },
    { separator: true },
    { label: 'Logout', icon: LogOut, href: '/logout' }
]);

const topBarItems = computed(() => [
    { href: '/admin', label: 'Home' },
    { href: '/admin/users', label: 'Users', parent: null },
    {
        label: 'Overview',
        children: [
            { href: '/admin/theme', label: 'Theme' },
            { href: '/admin/theme/settings', label: 'Settings' }
        ]
    },
    {
        label: 'Components',
        children: [
            { href: '/admin/components', label: 'Overview' },
            { href: '/admin/components/forms', label: 'Forms' },
            { href: '/admin/components/actions', label: 'Actions' },
            { href: '/admin/components/display', label: 'Display' },
            { href: '/admin/components/data', label: 'Data' }
        ]
    }
]);

const sideBarItems = computed(() => [
    {
        children: [
            { label: 'Home', href: '/admin', exact: true, icon: IconHome, activeIcon: IconHome },
            { label: 'Users', href: '/admin/users', parent: null, icon: IconUser, activeIcon: IconUser },
            { label: 'Components', href: '/admin/components', parent: '/admin/components', icon: IconColorFilter, activeIcon: IconColorFilter, count: 24 }
        ],
    },
]);
</script>
