<template>
    <LayoutApp
        title="Components - Actions"
        pageTitle="Actions"
        :pageNavItems="sideNavItems"
        :pageTabs="actionsTabs"
    >
        <div class="grid grid-cols-2 gap-4">
                <Link :href="$route('admin.components.actions.button')">
                    <Card class="hover:border-primary/50 transition-colors cursor-pointer h-full">
                        <CardHeader>
                            <div class="flex items-center gap-3">
                                <div class="p-2 bg-primary/10 rounded-lg">
                                    <MousePointerClick class="size-5 text-primary" />
                                </div>
                                <CardTitle class="text-base">Button</CardTitle>
                            </div>
                            <CardDescription>Variants, sizes, states, and button groups</CardDescription>
                        </CardHeader>
                    </Card>
                </Link>

                <Link :href="$route('admin.components.actions.menu')">
                    <Card class="hover:border-primary/50 transition-colors cursor-pointer h-full">
                        <CardHeader>
                            <div class="flex items-center gap-3">
                                <div class="p-2 bg-primary/10 rounded-lg">
                                    <Menu class="size-5 text-primary" />
                                </div>
                                <CardTitle class="text-base">Menu</CardTitle>
                            </div>
                            <CardDescription>Dropdown menus and button menus</CardDescription>
                        </CardHeader>
                    </Card>
                </Link>

                <Link :href="$route('admin.components.actions.dialog')">
                    <Card class="hover:border-primary/50 transition-colors cursor-pointer h-full">
                        <CardHeader>
                            <div class="flex items-center gap-3">
                                <div class="p-2 bg-primary/10 rounded-lg">
                                    <PanelRight class="size-5 text-primary" />
                                </div>
                                <CardTitle class="text-base">Dialog & Sheet</CardTitle>
                            </div>
                            <CardDescription>Modal dialogs and sliding panels</CardDescription>
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
import { MousePointerClick, Menu, PanelRight } from 'lucide-vue-next';
import { useShowcaseNav } from '../_composables/useShowcaseNav';

const { sideNavItems, actionsTabs } = useShowcaseNav();
</script>
