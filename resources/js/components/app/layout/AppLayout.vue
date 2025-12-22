<template>
    <Head :title="title" />
    <AppShell
        :isSideNav="true"
        :pageTitle="pageTitle"
        :pageTabs="pageTabs"
        :pageNavItems="pageNavItems"
        :pageUrl="$page.url"
        :sideBarItems="sideBarItems"
        :topBarItems="topBarItems"
        :linkComponent="'Link'"
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
import { IconBook, IconUser, IconColorFilter } from '@tabler/icons-vue';
import { Building2, CreditCard, Users, LayoutGrid, Settings, Activity, LogOut } from 'lucide-vue-next';
import AppShell from './AppShell.vue';
import ProfileMenu from '../navigation/ProfileMenu.vue';
import { ModeToggle } from '@/components/ui/mode-toggle';
import EditUserModal from '../modals/EditUserModal.vue';
import DeleteUserModal from '../modals/DeleteUserModal.vue';

const user = usePage().props.user;

const props = defineProps({
    title : {
        type: String,
        default: 'Home'
    },
    pageTitle : {
        type: String,
        default: 'Home'
    },
    pageTabs : {
        type: Array,
        default: () => []
    },
    pageNavItems: {
        type: Array,
        default: () => [],
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
    { label: 'Company Name', icon: Building2, href: '/' },
    { separator: true },
    { label: 'Billing & Plan', icon: CreditCard, href: '/' },
    { label: 'Manage Access', icon: Users, href: '/' },
    { label: 'Integrations', icon: LayoutGrid, href: '/' },
    { label: 'Settings', icon: Settings, href: '/' },
    { label: 'Status page', icon: Activity, href: 'https://google.com', external: true },
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
            { label: 'Users', href: '/admin/users', parent: null, icon: IconUser, activeIcon: IconUser },
            { label: 'Components', href: '/admin/components', parent: '/admin/components', icon: IconColorFilter, activeIcon: IconColorFilter }
        ],
    },
    {
        children: [
            { label: 'Inbox', href: '/lb', parent: '/test', icon: IconBook, activeIcon: IconBook, count: 5},
            { label: 'Contacts', href: '/lb', parent: '/test', icon: IconBook, activeIcon: IconBook },
        ],
    },
    {
        children: [
            { label: 'Campaigns', href: '/ff', parent: null, icon: IconBook, activeIcon: IconBook },
            { label: 'Workflows', href: '/ff', parent: null, icon: IconBook, activeIcon: IconBook },
            { label: 'Analytics', href: '/ff', parent: null, icon: IconBook, activeIcon: IconBook },
            { label: 'More', href: null, icon: IconBook, activeIcon: IconBook },
        ],
    },
]);
</script>
