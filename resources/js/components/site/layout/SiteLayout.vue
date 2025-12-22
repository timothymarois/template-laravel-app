<template>
    <Head :title="title" />
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
import { Head } from '@inertiajs/vue3';
import { AppShell } from '@/components/app/layout';

const props = defineProps({
    title : {
        type: String,
        default: ''
    },
    pageTitle : {
        type: String,
        default: ''
    },
});
</script>
