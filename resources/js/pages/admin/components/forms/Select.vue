<template>
    <LayoutApp
        title="Components - Select"
        pageTitle="Forms"
        :pageNavItems="sideNavItems"
        :pageTabs="formsTabs"
    >
        <div class="space-y-6">
            <Card>
                <CardHeader>
                    <CardTitle>Select</CardTitle>
                    <CardDescription>Dropdown selection menus</CardDescription>
                </CardHeader>
                <CardContent class="space-y-6">
                    <div>
                        <h4 class="text-sm font-medium mb-3">Basic</h4>
                        <div class="flex gap-4">
                            <div class="w-48">
                                <Select v-model="basicSelect" :options="basicOptions" placeholder="Select option" />
                            </div>
                        </div>
                    </div>

                    <div>
                        <h4 class="text-sm font-medium mb-3">Sizes</h4>
                        <div class="flex items-end gap-4">
                            <div class="w-48">
                                <div class="text-xs text-muted-foreground mb-1">Small</div>
                                <Select v-model="sizeSelect" :options="basicOptions" size="small" placeholder="Small" />
                            </div>
                            <div class="w-48">
                                <div class="text-xs text-muted-foreground mb-1">Default</div>
                                <Select v-model="sizeSelect" :options="basicOptions" placeholder="Default" />
                            </div>
                            <div class="w-48">
                                <div class="text-xs text-muted-foreground mb-1">Large</div>
                                <Select v-model="sizeSelect" :options="basicOptions" size="large" placeholder="Large" />
                            </div>
                        </div>
                    </div>
                </CardContent>
            </Card>
        </div>
    </LayoutApp>
</template>

<script setup lang="ts">
import { ref } from 'vue';
import { AdminLayout as LayoutApp } from '@/components/app';
import { Card, CardHeader, CardTitle, CardDescription, CardContent } from '@/components/ui/card';
import { Select } from '@/components/ui/form';
import { useShowcaseNav } from '../_composables/useShowcaseNav';

const { sideNavItems, formsTabs } = useShowcaseNav();

const basicSelect = ref('');
const sizeSelect = ref('');

const basicOptions = [
    { label: 'Option 1', value: 'option1' },
    { label: 'Option 2', value: 'option2' },
    { label: 'Option 3', value: 'option3' },
];
</script>
