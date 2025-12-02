<template>
    <Head :title="title" />
    <LayoutApp
        :isSideNav="false"
        :pageTitle="pageTitle"
        :pageUrl="$page.url"
    >
        <template #default>
            <slot />
        </template>
    </LayoutApp>
</template>

<script setup>
import { Head } from '@inertiajs/vue3';
import LayoutApp from '@components/App/Index.vue';

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
