<template>
    <SiteLayout title="Server Error">
        <div class="flex flex-col items-center justify-center min-h-[60vh] text-center px-4">
            <h1 class="text-6xl font-bold text-gray-900 dark:text-gray-100">500</h1>
            <p class="mt-4 text-xl text-gray-600 dark:text-gray-400">Server error</p>
            <p class="mt-2 text-gray-500 dark:text-gray-500">
                Something went wrong on our end. Please try again later.
            </p>
            <Link href="/" class="mt-8">
                <Button label="Go Home" />
            </Link>
        </div>
    </SiteLayout>
</template>

<script setup>
import { Link } from '@inertiajs/vue3';
import { SiteLayout } from '@/components/site';
import { Button } from '@/components/ui';
</script>
