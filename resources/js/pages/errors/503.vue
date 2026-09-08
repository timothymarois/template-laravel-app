<template>
    <SiteLayout title="Service Unavailable" robots="noindex">
        <div class="flex flex-col items-center justify-center min-h-[60vh] text-center px-4">
            <h1 class="text-6xl font-bold text-foreground">503</h1>
            <p class="mt-4 text-xl text-muted-foreground">Service unavailable</p>
            <p class="mt-2 text-muted-foreground/80">
                We're currently undergoing maintenance. Please check back soon.
            </p>
        </div>
    </SiteLayout>
</template>

<script setup>
import SiteLayout from '@/components/site/layout/SiteLayout.vue';
</script>
