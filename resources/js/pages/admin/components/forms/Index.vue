<template>
    <LayoutApp
        title="Components - Forms"
        pageTitle="Forms"
        :pageSidebarItems="sidebarItems"
        pageSidebarTitle="Components"
    >
        <div class="grid grid-cols-2 gap-4">
                <Link :href="$route('admin.components.forms.input')">
                    <Card class="hover:border-primary/50 transition-colors cursor-pointer h-full">
                        <CardHeader>
                            <div class="flex items-center gap-3">
                                <div class="p-2 bg-primary/10 rounded-lg">
                                    <TextCursorInput class="size-5 text-primary" />
                                </div>
                                <CardTitle class="text-base">Input</CardTitle>
                            </div>
                            <CardDescription>Text inputs with sizes, states, and features</CardDescription>
                        </CardHeader>
                    </Card>
                </Link>

                <Link :href="$route('admin.components.forms.select')">
                    <Card class="hover:border-primary/50 transition-colors cursor-pointer h-full">
                        <CardHeader>
                            <div class="flex items-center gap-3">
                                <div class="p-2 bg-primary/10 rounded-lg">
                                    <ChevronDown class="size-5 text-primary" />
                                </div>
                                <CardTitle class="text-base">Select</CardTitle>
                            </div>
                            <CardDescription>Dropdown selection menus</CardDescription>
                        </CardHeader>
                    </Card>
                </Link>

                <Link :href="$route('admin.components.forms.checkbox')">
                    <Card class="hover:border-primary/50 transition-colors cursor-pointer h-full">
                        <CardHeader>
                            <div class="flex items-center gap-3">
                                <div class="p-2 bg-primary/10 rounded-lg">
                                    <CheckSquare class="size-5 text-primary" />
                                </div>
                                <CardTitle class="text-base">Checkbox</CardTitle>
                            </div>
                            <CardDescription>Checkbox and radio group controls</CardDescription>
                        </CardHeader>
                    </Card>
                </Link>

                <Link :href="$route('admin.components.forms.switch')">
                    <Card class="hover:border-primary/50 transition-colors cursor-pointer h-full">
                        <CardHeader>
                            <div class="flex items-center gap-3">
                                <div class="p-2 bg-primary/10 rounded-lg">
                                    <ToggleLeft class="size-5 text-primary" />
                                </div>
                                <CardTitle class="text-base">Switch</CardTitle>
                            </div>
                            <CardDescription>Toggle switches for on/off states</CardDescription>
                        </CardHeader>
                    </Card>
                </Link>

                <Link :href="$route('admin.components.forms.slider')">
                    <Card class="hover:border-primary/50 transition-colors cursor-pointer h-full">
                        <CardHeader>
                            <div class="flex items-center gap-3">
                                <div class="p-2 bg-primary/10 rounded-lg">
                                    <SlidersHorizontal class="size-5 text-primary" />
                                </div>
                                <CardTitle class="text-base">Slider</CardTitle>
                            </div>
                            <CardDescription>Range sliders for numeric values</CardDescription>
                        </CardHeader>
                    </Card>
                </Link>

                <Link :href="$route('admin.components.forms.calendar')">
                    <Card class="hover:border-primary/50 transition-colors cursor-pointer h-full">
                        <CardHeader>
                            <div class="flex items-center gap-3">
                                <div class="p-2 bg-primary/10 rounded-lg">
                                    <CalendarDays class="size-5 text-primary" />
                                </div>
                                <CardTitle class="text-base">Calendar</CardTitle>
                            </div>
                            <CardDescription>Date pickers and calendar components</CardDescription>
                        </CardHeader>
                    </Card>
                </Link>

                <Link :href="$route('admin.components.forms.fields')">
                    <Card class="hover:border-primary/50 transition-colors cursor-pointer h-full">
                        <CardHeader>
                            <div class="flex items-center gap-3">
                                <div class="p-2 bg-primary/10 rounded-lg">
                                    <FormInput class="size-5 text-primary" />
                                </div>
                                <CardTitle class="text-base">Fields</CardTitle>
                            </div>
                            <CardDescription>Form field wrappers with labels and errors</CardDescription>
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
import { TextCursorInput, ChevronDown, CheckSquare, CalendarDays, FormInput, ToggleLeft, SlidersHorizontal } from 'lucide-vue-next';
import { useShowcaseNav } from '../_composables/useShowcaseNav';

const { sidebarItems } = useShowcaseNav();
</script>
