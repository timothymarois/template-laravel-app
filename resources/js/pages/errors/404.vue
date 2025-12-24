<template>
    <SiteLayout title="Page Not Found">
        <div class="flex flex-col items-center justify-center min-h-[60vh] text-center px-4">
            <h1 class="text-6xl font-bold text-gray-900 dark:text-gray-100">404</h1>
            <p class="mt-4 text-xl text-gray-600 dark:text-gray-400">Page not found</p>
            <p class="mt-2 text-gray-500 dark:text-gray-500">
                The page you're looking for doesn't exist or has been moved.
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
