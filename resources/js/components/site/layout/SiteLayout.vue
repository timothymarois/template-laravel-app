<template>
    <SeoHead
        :title="title"
        :description="description"
        :ogImage="ogImage"
        :ogImageAlt="ogImageAlt"
        :siteName="siteName"
        :robots="robots"
    />
    <AppShell
        :isSideNav="false"
        :pageTitle="pageTitle"
        :pageUrl="$page.url"
    >
        <template #default>
            <slot />
        </template>
    </AppShell>
</template>

<script setup>
import SeoHead from '@/components/app/SeoHead.vue';
import { computed } from 'vue';
import { usePage } from '@inertiajs/vue3';
import AppShell from '@/components/app/layout/AppShell.vue';

const page = usePage();

const props = defineProps({
    title: {
        type: String,
        default: ''
    },
    description: {
        type: String,
        default: ''
    },
    ogImage: {
        type: String,
        default: ''
    },
    ogImageAlt: {
        type: String,
        default: ''
    },
    siteName: {
        type: String,
        default: ''
    },
    robots: {
        type: String,
        default: ''
    },
    pageTitle: {
        type: String,
        default: ''
    },
});


</script>
