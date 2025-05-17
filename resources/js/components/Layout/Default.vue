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
import { IconBook, IconUser, IconColorFilter } from '@tabler/icons-vue';
import LayoutApp from '@atlas/components/App/Layout/App.vue';

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
