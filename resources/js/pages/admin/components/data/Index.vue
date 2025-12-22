<template>
    <LayoutApp
        title="Components - Data"
        pageTitle="Data"
        :pageNavItems="sideNavItems"
        :pageTabs="dataTabs"
    >
        <div class="grid grid-cols-2 gap-6">
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

                <Link :href="$route('admin.components.data.pagination')">
                    <Card class="hover:border-primary/50 transition-colors cursor-pointer h-full">
                        <CardHeader>
                            <div class="flex items-center gap-3">
                                <div class="p-2 bg-primary/10 rounded-lg">
                                    <ChevronLeft class="size-5 text-primary" />
                                </div>
                                <CardTitle class="text-base">Pagination</CardTitle>
                            </div>
                            <CardDescription>Page navigation and table actions</CardDescription>
                        </CardHeader>
                    </Card>
                </Link>
        </div>
    </LayoutApp>
</template>

<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { AdminLayout as LayoutApp } from '@/components/app';
import { Card, CardHeader, CardTitle, CardDescription } from '@/components/ui/card';
import { Table2, ChevronLeft } from 'lucide-vue-next';
import { useShowcaseNav } from '../_composables/useShowcaseNav';

const { sideNavItems, dataTabs } = useShowcaseNav();
</script>
