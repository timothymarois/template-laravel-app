<template>
    <LayoutApp
        title="Components - Display"
        pageTitle="Display"
        :pageSidebarItems="sidebarItems"
        pageSidebarTitle="Components"
    >
        <div class="grid grid-cols-2 gap-4">
                <Link :href="$route('admin.components.display.alert')">
                    <Card class="hover:border-primary/50 transition-colors cursor-pointer h-full">
                        <CardHeader>
                            <div class="flex items-center gap-3">
                                <div class="p-2 bg-primary/10 rounded-lg">
                                    <AlertCircle class="size-5 text-primary" />
                                </div>
                                <CardTitle class="text-base">Alert</CardTitle>
                            </div>
                            <CardDescription>Feedback messages for user actions and system status</CardDescription>
                        </CardHeader>
                    </Card>
                </Link>

                <Link :href="$route('admin.components.display.card')">
                    <Card class="hover:border-primary/50 transition-colors cursor-pointer h-full">
                        <CardHeader>
                            <div class="flex items-center gap-3">
                                <div class="p-2 bg-primary/10 rounded-lg">
                                    <LayoutGrid class="size-5 text-primary" />
                                </div>
                                <CardTitle class="text-base">Card</CardTitle>
                            </div>
                            <CardDescription>Container components for grouping content</CardDescription>
                        </CardHeader>
                    </Card>
                </Link>

                <Link :href="$route('admin.components.display.badge')">
                    <Card class="hover:border-primary/50 transition-colors cursor-pointer h-full">
                        <CardHeader>
                            <div class="flex items-center gap-3">
                                <div class="p-2 bg-primary/10 rounded-lg">
                                    <Tag class="size-5 text-primary" />
                                </div>
                                <CardTitle class="text-base">Badge & Avatar</CardTitle>
                            </div>
                            <CardDescription>Status indicators and user representations</CardDescription>
                        </CardHeader>
                    </Card>
                </Link>

                <Link :href="$route('admin.components.display.tooltip')">
                    <Card class="hover:border-primary/50 transition-colors cursor-pointer h-full">
                        <CardHeader>
                            <div class="flex items-center gap-3">
                                <div class="p-2 bg-primary/10 rounded-lg">
                                    <MessageSquare class="size-5 text-primary" />
                                </div>
                                <CardTitle class="text-base">Tooltip & Popover</CardTitle>
                            </div>
                            <CardDescription>Contextual information overlays</CardDescription>
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
import { AlertCircle, LayoutGrid, Tag, MessageSquare } from 'lucide-vue-next';
import { useShowcaseNav } from '../_composables/useShowcaseNav';

const { sidebarItems } = useShowcaseNav();
</script>
