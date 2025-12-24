<template>
    <SiteLayout title="Service Unavailable">
        <div class="flex flex-col items-center justify-center min-h-[60vh] text-center px-4">
            <h1 class="text-6xl font-bold text-gray-900 dark:text-gray-100">503</h1>
            <p class="mt-4 text-xl text-gray-600 dark:text-gray-400">Service unavailable</p>
            <p class="mt-2 text-gray-500 dark:text-gray-500">
                We're currently undergoing maintenance. Please check back soon.
            </p>
        </div>
    </SiteLayout>
</template>

<script setup>
import { SiteLayout } from '@/components/site';
</script>
