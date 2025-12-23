<template>
    <LayoutApp
        title="Components - Input"
        pageTitle="Forms"
        :pageNavItems="sideNavItems"
        :pageTabs="formsTabs"
    >
        <div class="space-y-6">
            <Card>
                <CardHeader>
                    <CardTitle>Input</CardTitle>
                    <CardDescription>Text input fields with various configurations</CardDescription>
                </CardHeader>
                <CardContent class="space-y-6">
                    <div>
                        <h4 class="text-sm font-medium mb-3">States</h4>
                        <div class="flex items-end gap-4">
                            <div class="w-48">
                                <div class="text-xs text-muted-foreground mb-1">Empty</div>
                                <Input placeholder="Empty input" fluid />
                            </div>
                            <div class="w-48">
                                <div class="text-xs text-muted-foreground mb-1">With Value</div>
                                <Input modelValue="Hello world" fluid />
                            </div>
                            <div class="w-48">
                                <div class="text-xs text-muted-foreground mb-1">Invalid</div>
                                <Input placeholder="Invalid" invalid fluid />
                            </div>
                            <div class="w-48 opacity-50">
                                <div class="text-xs text-muted-foreground mb-1">Disabled</div>
                                <Input placeholder="Disabled" disabled fluid />
                            </div>
                            <div class="w-48 opacity-50">
                                <div class="text-xs text-muted-foreground mb-1">Disabled with Value</div>
                                <Input modelValue="Can't edit" disabled fluid />
                            </div>
                        </div>
                    </div>

                    <div>
                        <h4 class="text-sm font-medium mb-3">Features</h4>
                        <div class="flex items-end gap-4">
                            <div class="w-64">
                                <div class="text-xs text-muted-foreground mb-1">Clearable</div>
                                <Input v-model="clearableValue" placeholder="Type to clear..." clearable fluid />
                            </div>
                            <div class="flex-1">
                                <div class="text-xs text-muted-foreground mb-1">Fluid (full width)</div>
                                <Input placeholder="Fluid width input" fluid />
                            </div>
                        </div>
                    </div>
                </CardContent>
            </Card>

            <Card>
                <CardHeader>
                    <CardTitle>Input Groups</CardTitle>
                    <CardDescription>Common patterns for grouping inputs with other elements</CardDescription>
                </CardHeader>
                <CardContent class="space-y-6">
                    <div>
                        <h4 class="text-sm font-medium mb-3">Search with Button</h4>
                        <div class="flex gap-0">
                            <Input v-model="searchValue" placeholder="Search..." class="rounded-r-none" />
                            <Button class="rounded-l-none">Search</Button>
                        </div>
                    </div>

                    <div>
                        <h4 class="text-sm font-medium mb-3">Input with Select Suffix</h4>
                        <div class="flex gap-0 w-72">
                            <Input v-model="amountValue" type="number" placeholder="0.00" class="rounded-r-none" />
                            <Select v-model="currencyValue" :options="currencyOptions" class="w-24 rounded-l-none border-l-0" />
                        </div>
                    </div>

                    <div>
                        <h4 class="text-sm font-medium mb-3">Multiple Inputs in Row</h4>
                        <div class="flex gap-4">
                            <div class="flex-1">
                                <Input placeholder="First name" fluid />
                            </div>
                            <div class="flex-1">
                                <Input placeholder="Last name" fluid />
                            </div>
                        </div>
                    </div>

                    <div>
                        <h4 class="text-sm font-medium mb-3">Filter Bar Pattern</h4>
                        <div class="flex items-center gap-2 p-3 bg-muted/30 rounded-lg">
                            <Input v-model="filterSearch" placeholder="Search..." clearable class="w-64" />
                            <Select v-model="filterStatus" :options="statusOptions" placeholder="Status" class="w-32" />
                            <Select v-model="filterRole" :options="roleOptions" placeholder="Role" class="w-32" />
                            <Button variant="outline" size="sm">
                                <Filter class="size-4 mr-1" />
                                Apply
                            </Button>
                            <Button variant="ghost" size="sm">Reset</Button>
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
import { Input, Select } from '@/components/ui/form';
import { Button } from '@/components/ui/button';
import { Filter } from 'lucide-vue-next';
import { useShowcaseNav } from '../_composables/useShowcaseNav';

const { sideNavItems, formsTabs } = useShowcaseNav();

const clearableValue = ref('Type something here');
const searchValue = ref('');
const amountValue = ref('');
const currencyValue = ref('USD');
const filterSearch = ref('');
const filterStatus = ref('');
const filterRole = ref('');

const currencyOptions = [
    { label: 'USD', value: 'USD' },
    { label: 'EUR', value: 'EUR' },
    { label: 'GBP', value: 'GBP' },
];

const statusOptions = [
    { label: 'Active', value: 'active' },
    { label: 'Pending', value: 'pending' },
    { label: 'Inactive', value: 'inactive' },
];

const roleOptions = [
    { label: 'Admin', value: 'admin' },
    { label: 'Editor', value: 'editor' },
    { label: 'User', value: 'user' },
];
</script>
