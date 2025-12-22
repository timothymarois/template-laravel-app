<template>
    <LayoutApp
        title="Components - Forms"
        pageTitle="Forms"
        :pageNavItems="sideNavItems"
        :pageTabs="formsTabs"
    >
        <div class="grid grid-cols-2 gap-6">
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
                                <CardTitle class="text-base">Checkbox & Radio</CardTitle>
                            </div>
                            <CardDescription>Boolean and single selection controls</CardDescription>
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
import { TextCursorInput, ChevronDown, CheckSquare, FormInput } from 'lucide-vue-next';
import { useShowcaseNav } from '../_composables/useShowcaseNav';

const { sideNavItems, formsTabs } = useShowcaseNav();
</script>
