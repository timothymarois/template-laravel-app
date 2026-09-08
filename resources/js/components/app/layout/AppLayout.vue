<template>
    <SeoHead
        :title="title"
        :description="description"
        :ogImage="ogImage"
        :ogImageAlt="ogImageAlt"
        :siteName="siteName"
        :robots="robots"
    />
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
import SeoHead from '../SeoHead.vue';
import { computed } from 'vue';
import { usePage } from '@inertiajs/vue3';
import { House, User, Palette, Gauge, LogOut } from 'lucide-vue-next';
import AppShell from './AppShell.vue';
import ProfileMenu from '../navigation/ProfileMenu.vue';
import ModeToggle from '../navigation/ModeToggle.vue';
import EditUserModal from '../modals/EditUserModal.vue';
import DeleteUserModal from '../modals/DeleteUserModal.vue';

const page = usePage();
// Computed, not a plain read: a partial reload or an impersonation swap changes
// the shared user under a mounted layout, and a by-value read keeps the old one.
const user = computed(() => page.props.user);

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
    robots: {
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



const profileMenuItems = computed(() => [
    { separator: true },
    { label: 'Horizon', icon: Gauge, href: '/horizon', external: true },
    { separator: true },
    { label: 'Logout', icon: LogOut, href: '/logout', method: 'post' }
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
            { label: 'Home', href: '/admin', exact: true, icon: House, activeIcon: House },
            { label: 'Users', href: '/admin/users', parent: null, icon: User, activeIcon: User },
            { label: 'Components', href: '/admin/components', parent: '/admin/components', icon: Palette, activeIcon: Palette, count: 24 }
        ],
    },
]);
</script>
