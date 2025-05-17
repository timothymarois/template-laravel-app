<template>
    <LayoutApp title="Settings" pageTitle="Theme" :pageTabs="pageTabs" :pageNavItems="pageNavItems">
        <div class="space-y-4">
            <Card>
                <template #content>
                    <div>This is my settings page</div>
                </template>
            </Card>
            <Card >
                <template #header>
                    <div class="font-semibold text-gray-900 dark:text-gray-100 text-md flex items-center space-x-2">
                        <div>Editor</div>
                    </div>
                </template>
                <template #content>
                    <div class="border border-surface-300 dark:border-surface-700 rounded-md">
                        <AtlasEditor
                            :content="editContent"
                            @save="editContent = $event"
                        />
                    </div>
                </template>
            </Card>
            <Card>
                <template #header>
                    <div class="font-semibold text-gray-900 dark:text-gray-100 text-md flex items-center space-x-2">
                        <div>View content</div>
                    </div>
                </template>
                <template #content>
                    <div>
                        <AtlasContentViewer :content="editContent" />
                    </div>
                </template>
            </Card>
            <Card>
                <template #header>
                    <div class="font-semibold text-gray-900 dark:text-gray-100 text-md flex items-center space-x-2">
                        <div>View content (as HTML)</div>
                    </div>
                </template>
                <template #content>
                    <div>
                        <Textarea
                            v-model="editContent"
                            readonly
                            fluid
                            rows="10"
                        />
                    </div>
                </template>
            </Card>
        </div>
    </LayoutApp>
</template>

<script setup>
import AtlasEditor from '@atlas/components/Editor/Editor.vue';
import AtlasContentViewer from '@atlas/components/Editor/Content.vue';

const editContent = ref('');

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
