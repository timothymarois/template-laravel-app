<template>
    <AppLayout
        title="Components - ViewToggle"
        pageTitle="ViewToggle"
        :pageSidebarItems="sidebarItems"
        pageSidebarTitle="Components"
    >
        <div class="space-y-4">
            <Card>
                <CardHeader>
                    <CardTitle>Grid / list switcher</CardTitle>
                    <CardDescription>Two-mode toggle bound via v-model. Current mode: <code>{{ mode }}</code></CardDescription>
                </CardHeader>
                <CardContent class="space-y-4">
                    <ViewToggle v-model="mode" />

                    <div v-if="mode === 'grid'" class="grid grid-cols-3 gap-3">
                        <div v-for="n in 6" :key="n" class="h-20 rounded-md bg-muted border border-border" />
                    </div>

                    <ul v-else class="space-y-2">
                        <li v-for="n in 6" :key="n" class="h-10 rounded-md bg-muted border border-border" />
                    </ul>
                </CardContent>
            </Card>
        </div>
    </AppLayout>
</template>

<script setup lang="ts">
import { ref } from 'vue';
import AppLayout from '@/components/app/layout/AppLayout.vue';
import { Card, CardHeader, CardTitle, CardDescription, CardContent } from '@/components/ui/card';
import { ViewToggle } from '@/components/ui/view-toggle';
import { useShowcaseNav } from '../_composables/useShowcaseNav';

const { sidebarItems } = useShowcaseNav();

const mode = ref<'grid' | 'list'>('grid');
</script>
