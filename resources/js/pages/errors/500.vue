<template>
    <SiteLayout title="Server Error">
        <div class="flex flex-col items-center justify-center min-h-[60vh] text-center px-4">
            <h1 class="text-6xl font-bold text-foreground">500</h1>
            <p class="mt-4 text-xl text-muted-foreground">Server error</p>
            <p class="mt-2 text-muted-foreground/80">
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
import SiteLayout from '@/components/site/layout/SiteLayout.vue';
import { Button } from '@/components/ui';
</script>
