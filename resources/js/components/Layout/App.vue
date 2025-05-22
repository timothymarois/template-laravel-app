<template>
    <Head :title="title" />
    <LayoutApp
        :isSideNav="false"
        :pageTitle="pageTitle"
        :pageTabs="pageTabs"
        :pageNavItems="pageNavItems"
        :pageUrl="$page.url"
        :sideBarItems="sideBarItems"
        :topBarItems="topBarItems"
        :linkComponent="'Link'"
    >
        <template #navActions>
            <NavOptionDarkToggle />
            <ProfileMenu
                :user="user"
                :items="profileMenuItems"
                :avatar-only="true"
                headerLink="/"
                linkComponent="Link"
            />
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
            <ModalEditUser />
            <ModalDeleteUser />
            <ModalTest />
            <ModalBlank />
        </template>
    </LayoutApp>
</template>

<script setup>
import { IconBook, IconUser, IconColorFilter } from '@tabler/icons-vue';
import LayoutApp from '@atlas/components/App/Layout/App.vue';
import ProfileMenu from '@atlas/components/App/Nav/ProfileMenu.vue';

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
    }
});

const profileMenuItems = computed(() => [
    { separator: true },
    { label: 'Company Name', icon: 'pi pi-building-columns', href: '/' },
    { separator: true },
    { label: 'Billing & Plan', icon: 'pi pi-credit-card', href: '/' },
    { label: 'Manage Access', icon: 'pi pi-users', href: '/' },
    { label: 'Integrations', icon: 'pi pi-objects-column', href: '/' },
    { label: 'Settings', icon: 'pi pi-cog', href: '/' },
    { label: 'Status page', icon: 'pi pi-cog', href: 'https://google.com', external: true },
    { separator: true },
    { label: 'Logout', icon: 'pi pi-sign-out', href: '/logout' }
]);

const topBarItems = computed(() => [
    { href: '/', label: 'Home' },
    { href: '/admin/users', label: 'Users' },
    {
        href: '/theme', label: 'Overview',
        children: [
            { href: '/theme', label: 'Theme' },
            { href: '/theme/settings', label: 'Settings' }
        ]
    },
    {
        href: '/components',
        label: 'Components',
        children: [
            { href: '/components/buttons', label: 'Buttons' },
            { href: '/components/forms', label: 'Forms' }
        ]
    }
]);

const sideBarItems = computed(() => [
    {
        children: [
            { label: 'Users', href: '/admin/users', parent: null, icon: IconUser, activeIcon: IconUser },
            { label: 'Theme', href: '/theme', parent: '/theme', icon: IconColorFilter, activeIcon: IconColorFilter }
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
