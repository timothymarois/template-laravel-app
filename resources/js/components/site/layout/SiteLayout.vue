<template>
    <Head :title="title">
        <!-- Standard meta -->
        <meta v-if="description" name="description" :content="description" />

        <!-- Open Graph (Facebook, LinkedIn, etc.) -->
        <meta v-if="title" property="og:title" :content="title" />
        <meta v-if="description" property="og:description" :content="description" />
        <meta v-if="ogImage" property="og:image" :content="absoluteOgImage" />
        <meta v-if="ogImage" property="og:image:alt" :content="ogImageAlt || title" />
        <meta property="og:type" content="website" />
        <meta property="og:url" :content="canonicalUrl" />
        <meta v-if="siteName" property="og:site_name" :content="siteName" />

        <!-- Twitter/X -->
        <meta name="twitter:card" :content="ogImage ? 'summary_large_image' : 'summary'" />
        <meta v-if="title" name="twitter:title" :content="title" />
        <meta v-if="description" name="twitter:description" :content="description" />
        <meta v-if="ogImage" name="twitter:image" :content="absoluteOgImage" />
        <meta v-if="ogImage" name="twitter:image:alt" :content="ogImageAlt || title" />
    </Head>
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
import { computed } from 'vue';
import { Head, usePage } from '@inertiajs/vue3';
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
    pageTitle: {
        type: String,
        default: ''
    },
});

const canonicalUrl = computed(() => {
    if (typeof window === 'undefined') return '';
    return window.location.origin + page.url;
});

const absoluteOgImage = computed(() => {
    if (!props.ogImage) return '';
    if (props.ogImage.startsWith('http')) return props.ogImage;
    if (typeof window === 'undefined') return props.ogImage;
    return window.location.origin + (props.ogImage.startsWith('/') ? '' : '/') + props.ogImage;
});
</script>
