<template>
    <LayoutApp
        title="Components - Table Actions"
        pageTitle="Actions"
        :pageSidebarItems="sidebarItems"
        pageSidebarTitle="Components"
    >
        <div class="space-y-4">
            <Card>
                <CardHeader>
                    <CardTitle>Table Actions</CardTitle>
                    <CardDescription>Bulk action bar that appears when items are selected</CardDescription>
                </CardHeader>
                <CardContent class="space-y-4">
                    <div>
                        <h4 class="text-sm font-medium mb-3">Basic Actions</h4>
                        <div class="flex items-center gap-4">
                            <TableActions
                                :selectedCount="3"
                                :menuItems="basicTableActions"
                                @action="handleAction"
                            />
                        </div>
                    </div>

                    <div>
                        <h4 class="text-sm font-medium mb-3">With Dropdown Menu</h4>
                        <div class="flex items-center gap-4">
                            <TableActions
                                :selectedCount="selectedCount"
                                :menuItems="fullTableActions"
                                @action="handleAction"
                            />
                        </div>
                        <div class="flex items-center gap-2 mt-3">
                            <Button variant="outline" @click="selectedCount = Math.min(selectedCount + 1, 10)">
                                Add Selection
                            </Button>
                            <Button variant="outline" @click="selectedCount = Math.max(selectedCount - 1, 1)">
                                Remove Selection
                            </Button>
                            <span class="text-sm text-muted-foreground">Selected: {{ selectedCount }}</span>
                        </div>
                    </div>
                </CardContent>
            </Card>
        </div>
    </LayoutApp>
</template>

<script setup lang="ts">
import { ref } from 'vue';
import { AppLayout as LayoutApp } from '@/components/app';
import { Card, CardHeader, CardTitle, CardDescription, CardContent } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { TableActions } from '@/components/ui/data-table';
import { useShowcaseNav } from '../_composables/useShowcaseNav';

const { sidebarItems } = useShowcaseNav();

const selectedCount = ref(3);

const basicTableActions = [
    { label: 'Delete', action: 'delete' },
    { label: 'Archive', action: 'archive' },
];

const fullTableActions = [
    { label: 'Delete', action: 'delete' },
    {
        label: 'More',
        children: [
            { label: 'Archive', action: 'archive' },
            { label: 'Export', action: 'export' },
            { separator: true },
            { label: 'Disable', action: 'disable', disabled: true },
        ],
    },
];

const handleAction = (action: string) => {
    if (action === 'clear') {
        selectedCount.value = 1;
    }
};
</script>
