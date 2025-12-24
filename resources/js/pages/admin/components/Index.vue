<template>
    <LayoutApp title="Components" pageTitle="Components" :pageSidebarItems="sidebarItems" pageSidebarTitle="Components">
        <div class="grid grid-cols-2 gap-4">
            <Link :href="$route('admin.components.actions')">
                <Card class="hover:border-primary/50 transition-colors cursor-pointer h-full">
                    <CardHeader>
                        <div class="flex items-center gap-3">
                            <div class="p-2 bg-primary/10 rounded-lg">
                                <MousePointerClick class="size-5 text-primary" />
                            </div>
                            <CardTitle class="text-base">Actions</CardTitle>
                        </div>
                    </CardHeader>
                    <CardContent>
                        <CardDescription>
                            Buttons, states, groups, menus, dialogs, and sheets for user interactions.
                        </CardDescription>
                    </CardContent>
                </Card>
            </Link>

            <Link :href="$route('admin.components.charts')">
                <Card class="hover:border-primary/50 transition-colors cursor-pointer h-full">
                    <CardHeader>
                        <div class="flex items-center gap-3">
                            <div class="p-2 bg-primary/10 rounded-lg">
                                <BarChart3 class="size-5 text-primary" />
                            </div>
                            <CardTitle class="text-base">Charts</CardTitle>
                        </div>
                    </CardHeader>
                    <CardContent>
                        <CardDescription>
                            Bar, line, area, pie, and donut charts for data visualization.
                        </CardDescription>
                    </CardContent>
                </Card>
            </Link>

            <Link :href="$route('admin.components.data')">
                <Card class="hover:border-primary/50 transition-colors cursor-pointer h-full">
                    <CardHeader>
                        <div class="flex items-center gap-3">
                            <div class="p-2 bg-primary/10 rounded-lg">
                                <Table2 class="size-5 text-primary" />
                            </div>
                            <CardTitle class="text-base">Data</CardTitle>
                        </div>
                    </CardHeader>
                    <CardContent>
                        <CardDescription>
                            Tables, data tables with selection, bulk actions, and pagination.
                        </CardDescription>
                    </CardContent>
                </Card>
            </Link>

            <Link :href="$route('admin.components.display')">
                <Card class="hover:border-primary/50 transition-colors cursor-pointer h-full">
                    <CardHeader>
                        <div class="flex items-center gap-3">
                            <div class="p-2 bg-primary/10 rounded-lg">
                                <LayoutGrid class="size-5 text-primary" />
                            </div>
                            <CardTitle class="text-base">Display</CardTitle>
                        </div>
                    </CardHeader>
                    <CardContent>
                        <CardDescription>
                            Cards, badges, avatars, tooltips, and popovers for presenting information.
                        </CardDescription>
                    </CardContent>
                </Card>
            </Link>

            <Link :href="$route('admin.components.forms')">
                <Card class="hover:border-primary/50 transition-colors cursor-pointer h-full">
                    <CardHeader>
                        <div class="flex items-center gap-3">
                            <div class="p-2 bg-primary/10 rounded-lg">
                                <TextCursorInput class="size-5 text-primary" />
                            </div>
                            <CardTitle class="text-base">Forms</CardTitle>
                        </div>
                    </CardHeader>
                    <CardContent>
                        <CardDescription>
                            Input, Select, Checkbox, Radio, and form field patterns including input groups.
                        </CardDescription>
                    </CardContent>
                </Card>
            </Link>
        </div>
    </LayoutApp>
</template>

<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { AppLayout as LayoutApp } from '@/components/app';
import { Card, CardHeader, CardTitle, CardDescription, CardContent } from '@/components/ui/card';
import { BarChart3, LayoutGrid, MousePointerClick, Table2, TextCursorInput } from 'lucide-vue-next';
import { useShowcaseNav } from './_composables/useShowcaseNav';

const { sidebarItems } = useShowcaseNav();
</script>
