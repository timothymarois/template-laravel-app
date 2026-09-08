<template>
    <Head :title="title">
        <!-- Canonical. Absolute, and built from the server-shared appUrl rather than
             window.location — under SSR there is no window, which is exactly the
             render a crawler sees. -->
        <link v-if="canonicalUrl" rel="canonical" :href="canonicalUrl" />
        <meta v-if="robots" name="robots" :content="robots" />
        <meta v-if="resolvedDescription" name="description" :content="resolvedDescription" />

        <!-- Open Graph (Facebook, LinkedIn, etc.) -->
        <meta v-if="title" property="og:title" :content="title" />
        <meta v-if="resolvedDescription" property="og:description" :content="resolvedDescription" />
        <meta v-if="absoluteOgImage" property="og:image" :content="absoluteOgImage" />
        <meta v-if="absoluteOgImage" property="og:image:alt" :content="ogImageAlt || title" />
        <meta property="og:type" :content="ogType" />
        <meta v-if="canonicalUrl" property="og:url" :content="canonicalUrl" />
        <meta v-if="resolvedSiteName" property="og:site_name" :content="resolvedSiteName" />

        <!-- Twitter/X -->
        <meta name="twitter:card" :content="absoluteOgImage ? 'summary_large_image' : 'summary'" />
        <meta v-if="title" name="twitter:title" :content="title" />
        <meta v-if="resolvedDescription" name="twitter:description" :content="resolvedDescription" />
        <meta v-if="absoluteOgImage" name="twitter:image" :content="absoluteOgImage" />
        <meta v-if="absoluteOgImage" name="twitter:image:alt" :content="ogImageAlt || title" />
    </Head>
</template>

<script setup>
/**
 * The single source of the document head.
 *
 * It lived duplicated in AppLayout and SiteLayout, which meant every SEO fix had
 * to be made twice and the two had already drifted. Absolute URLs come from the
 * `appUrl` shared prop, not `window.location.origin`: the previous version
 * returned '' under SSR, so the server-rendered page — the one crawlers and
 * social scrapers read — shipped an empty og:url and a relative og:image.
 */
import { computed } from 'vue';
import { Head, usePage } from '@inertiajs/vue3';

const props = defineProps({
    title: { type: String, default: '' },
    description: { type: String, default: '' },
    ogImage: { type: String, default: '' },
    ogImageAlt: { type: String, default: '' },
    ogType: { type: String, default: 'website' },
    siteName: { type: String, default: '' },
    /** e.g. "noindex, nofollow" for pages that must never be indexed. */
    robots: { type: String, default: '' },
});

const page = usePage();

const appUrl = computed(() => (page.props.appUrl ?? '').replace(/\/$/, ''));
const seo = computed(() => page.props.seo ?? {});

const resolvedDescription = computed(() => props.description || seo.value.description || '');
const resolvedSiteName = computed(() => props.siteName || seo.value.siteName || '');

const canonicalUrl = computed(() => {
    if (!appUrl.value) return '';

    return appUrl.value + (page.url ?? '');
});

const absoluteOgImage = computed(() => {
    const image = props.ogImage || seo.value.image || '';
    if (!image) return '';
    if (image.startsWith('http')) return image;
    if (!appUrl.value) return '';

    return appUrl.value + (image.startsWith('/') ? '' : '/') + image;
});
</script>
