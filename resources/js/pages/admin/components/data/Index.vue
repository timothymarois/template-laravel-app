<template>
    <LayoutApp
        title="Components - Data"
        pageTitle="Data"
        :pageSidebarItems="sidebarItems"
        pageSidebarTitle="Components"
    >
        <div class="grid grid-cols-2 gap-4">
            <Link :href="$route('admin.components.data.actions')">
                <Card class="hover:border-primary/50 transition-colors cursor-pointer h-full">
                    <CardHeader>
                        <div class="flex items-center gap-3">
                            <div class="p-2 bg-primary/10 rounded-lg">
                                <MousePointerClick class="size-5 text-primary" />
                            </div>
                            <CardTitle class="text-base">Actions</CardTitle>
                        </div>
                        <CardDescription>Bulk actions and table action bars</CardDescription>
                    </CardHeader>
                </Card>
            </Link>

            <Link :href="$route('admin.components.data.pagination')">
                <Card class="hover:border-primary/50 transition-colors cursor-pointer h-full">
                    <CardHeader>
                        <div class="flex items-center gap-3">
                            <div class="p-2 bg-primary/10 rounded-lg">
                                <ChevronLeft class="size-5 text-primary" />
                            </div>
                            <CardTitle class="text-base">Pagination</CardTitle>
                        </div>
                        <CardDescription>Page navigation controls</CardDescription>
                    </CardHeader>
                </Card>
            </Link>

            <Link :href="$route('admin.components.data.table')">
                <Card class="hover:border-primary/50 transition-colors cursor-pointer h-full">
                    <CardHeader>
                        <div class="flex items-center gap-3">
                            <div class="p-2 bg-primary/10 rounded-lg">
                                <Table2 class="size-5 text-primary" />
                            </div>
                            <CardTitle class="text-base">Table</CardTitle>
                        </div>
                        <CardDescription>Basic and data tables with sorting and selection</CardDescription>
                    </CardHeader>
                </Card>
            </Link>
        </div>
    </LayoutApp>
</template>

<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { AppLayout as LayoutApp } from '@/components/app';
import { Card, CardHeader, CardTitle, CardDescription } from '@/components/ui/card';
import { ChevronLeft, MousePointerClick, Table2 } from 'lucide-vue-next';
import { useShowcaseNav } from '../_composables/useShowcaseNav';

const { sidebarItems } = useShowcaseNav();
</script>
