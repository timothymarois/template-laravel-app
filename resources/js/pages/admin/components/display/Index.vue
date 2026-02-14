<template>
    <AppLayout
        title="Components - Display"
        pageTitle="Display"
        :pageSidebarItems="sidebarItems"
        pageSidebarTitle="Components"
    >
        <div class="grid grid-cols-2 gap-4">
            <Link :href="$route('admin.components.display.accordion')">
                <Card class="hover:border-primary/50 transition-colors cursor-pointer h-full">
                    <CardHeader>
                        <div class="flex items-center gap-3">
                            <div class="p-2 bg-primary/10 rounded-lg">
                                <ChevronDown class="size-5 text-primary" />
                            </div>
                            <CardTitle class="text-base">Accordion</CardTitle>
                        </div>
                        <CardDescription>Collapsible content sections</CardDescription>
                    </CardHeader>
                </Card>
            </Link>

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

            <Link :href="$route('admin.components.display.avatar')">
                <Card class="hover:border-primary/50 transition-colors cursor-pointer h-full">
                    <CardHeader>
                        <div class="flex items-center gap-3">
                            <div class="p-2 bg-primary/10 rounded-lg">
                                <CircleUser class="size-5 text-primary" />
                            </div>
                            <CardTitle class="text-base">Avatar</CardTitle>
                        </div>
                        <CardDescription>User representations and profile images</CardDescription>
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
                            <CardTitle class="text-base">Badge</CardTitle>
                        </div>
                        <CardDescription>Status indicators and labels</CardDescription>
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

            <Link :href="$route('admin.components.display.carousel')">
                <Card class="hover:border-primary/50 transition-colors cursor-pointer h-full">
                    <CardHeader>
                        <div class="flex items-center gap-3">
                            <div class="p-2 bg-primary/10 rounded-lg">
                                <GalleryHorizontal class="size-5 text-primary" />
                            </div>
                            <CardTitle class="text-base">Carousel</CardTitle>
                        </div>
                        <CardDescription>Scrollable content slider with navigation</CardDescription>
                    </CardHeader>
                </Card>
            </Link>

            <Link :href="$route('admin.components.display.loading')">
                <Card class="hover:border-primary/50 transition-colors cursor-pointer h-full">
                    <CardHeader>
                        <div class="flex items-center gap-3">
                            <div class="p-2 bg-primary/10 rounded-lg">
                                <Loader2 class="size-5 text-primary" />
                            </div>
                            <CardTitle class="text-base">Loading</CardTitle>
                        </div>
                        <CardDescription>Spinners, progress bars, and skeleton loaders</CardDescription>
                    </CardHeader>
                </Card>
            </Link>

            <Link :href="$route('admin.components.display.popover')">
                <Card class="hover:border-primary/50 transition-colors cursor-pointer h-full">
                    <CardHeader>
                        <div class="flex items-center gap-3">
                            <div class="p-2 bg-primary/10 rounded-lg">
                                <PanelTopOpen class="size-5 text-primary" />
                            </div>
                            <CardTitle class="text-base">Popover</CardTitle>
                        </div>
                        <CardDescription>Interactive floating content panels</CardDescription>
                    </CardHeader>
                </Card>
            </Link>

            <Link :href="$route('admin.components.display.resizable')">
                <Card class="hover:border-primary/50 transition-colors cursor-pointer h-full">
                    <CardHeader>
                        <div class="flex items-center gap-3">
                            <div class="p-2 bg-primary/10 rounded-lg">
                                <PanelLeftClose class="size-5 text-primary" />
                            </div>
                            <CardTitle class="text-base">Resizable</CardTitle>
                        </div>
                        <CardDescription>Resizable panels with draggable handles</CardDescription>
                    </CardHeader>
                </Card>
            </Link>

            <Link :href="$route('admin.components.display.tabs')">
                <Card class="hover:border-primary/50 transition-colors cursor-pointer h-full">
                    <CardHeader>
                        <div class="flex items-center gap-3">
                            <div class="p-2 bg-primary/10 rounded-lg">
                                <LayoutPanelTop class="size-5 text-primary" />
                            </div>
                            <CardTitle class="text-base">Tabs</CardTitle>
                        </div>
                        <CardDescription>Tabbed navigation with multiple style variants</CardDescription>
                    </CardHeader>
                </Card>
            </Link>

            <Link :href="$route('admin.components.display.toast')">
                <Card class="hover:border-primary/50 transition-colors cursor-pointer h-full">
                    <CardHeader>
                        <div class="flex items-center gap-3">
                            <div class="p-2 bg-primary/10 rounded-lg">
                                <Bell class="size-5 text-primary" />
                            </div>
                            <CardTitle class="text-base">Toast</CardTitle>
                        </div>
                        <CardDescription>Non-intrusive notification messages</CardDescription>
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
                            <CardTitle class="text-base">Tooltip</CardTitle>
                        </div>
                        <CardDescription>Contextual hints on hover</CardDescription>
                    </CardHeader>
                </Card>
            </Link>
        </div>
    </AppLayout>
</template>

<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import AppLayout from '@/components/app/layout/AppLayout.vue';
import { Card, CardHeader, CardTitle, CardDescription } from '@/components/ui/card';
import {
    AlertCircle,
    Bell,
    ChevronDown,
    CircleUser,
    GalleryHorizontal,
    LayoutGrid,
    LayoutPanelTop,
    Loader2,
    MessageSquare,
    PanelLeftClose,
    PanelTopOpen,
    Tag,
} from 'lucide-vue-next';
import { useShowcaseNav } from '../_composables/useShowcaseNav';

const { sidebarItems } = useShowcaseNav();
</script>
