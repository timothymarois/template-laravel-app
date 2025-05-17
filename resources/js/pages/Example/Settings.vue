<template>
    <LayoutApp title="Settings" pageTitle="Theme" :pageTabs="pageTabs" :pageNavItems="pageNavItems">
        <div>
            <Card>
                <template #content>
                    <div>This is my settings page</div>
                </template>
            </Card>
        </div>
    </LayoutApp>
</template>

<script setup>
const pageTabs = [
    { title: 'Overview', href: '/theme' },
    { title: 'Disabled', href: '/theme/table', disabled: true },
    { title: 'Settings', href: '/theme/settings' },
];

const pageNavItems = [
    { label: 'Overview', href: '/theme' },
    { label: 'Settings', href: '/theme/settings' },
    { label: 'Account', href: '/settings/account' },
    { label: 'Security', href: '/settings/security' },
    { label: 'Notifications', href: '/settings/notifications' },
];
</script>
