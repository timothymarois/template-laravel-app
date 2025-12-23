<template>
    <LayoutApp
        title="Components - Pagination"
        pageTitle="Pagination"
        :pageSidebarItems="sidebarItems"
        pageSidebarTitle="Components"
    >
        <div class="space-y-4">
            <Card>
                <CardHeader>
                    <CardTitle>Pagination</CardTitle>
                    <CardDescription>Page navigation controls</CardDescription>
                </CardHeader>
                <CardContent class="space-y-4">
                    <div>
                        <h4 class="text-sm font-medium mb-3">Paginator (Laravel-style links)</h4>
                        <Paginator :links="paginationLinks" />
                    </div>

                    <div>
                        <h4 class="text-sm font-medium mb-3">With Per-Page Selector</h4>
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <span class="text-sm text-muted-foreground">Show</span>
                                <Select v-model="perPage" :options="perPageOptions" class="w-20" />
                                <span class="text-sm text-muted-foreground">per page</span>
                            </div>
                            <Paginator :links="paginationLinks" />
                        </div>
                    </div>

                    <div>
                        <h4 class="text-sm font-medium mb-3">Pagination States</h4>
                        <div class="space-y-4">
                            <div>
                                <p class="text-xs text-muted-foreground mb-2">First page (no previous)</p>
                                <Paginator :links="firstPageLinks" />
                            </div>
                            <div>
                                <p class="text-xs text-muted-foreground mb-2">Last page (no next)</p>
                                <Paginator :links="lastPageLinks" />
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
import { Select } from '@/components/ui/select-popover';
import { Paginator } from '@/components/ui/data-table';
import { useShowcaseNav } from '../_composables/useShowcaseNav';

const { sidebarItems } = useShowcaseNav();

const perPage = ref('10');

const perPageOptions = [
    { label: '5', value: '5' },
    { label: '10', value: '10' },
    { label: '25', value: '25' },
    { label: '50', value: '50' },
];

const paginationLinks = [
    { url: null, label: '&laquo; Previous', active: false },
    { url: '#', label: '1', active: false },
    { url: '#', label: '2', active: true },
    { url: '#', label: '3', active: false },
    { url: '#', label: '4', active: false },
    { url: '#', label: '5', active: false },
    { url: '#', label: 'Next &raquo;', active: false },
];

const firstPageLinks = [
    { url: null, label: '&laquo; Previous', active: false },
    { url: '#', label: '1', active: true },
    { url: '#', label: '2', active: false },
    { url: '#', label: '3', active: false },
    { url: '#', label: 'Next &raquo;', active: false },
];

const lastPageLinks = [
    { url: '#', label: '&laquo; Previous', active: false },
    { url: '#', label: '1', active: false },
    { url: '#', label: '2', active: false },
    { url: '#', label: '3', active: true },
    { url: null, label: 'Next &raquo;', active: false },
];
</script>
