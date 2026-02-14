<template>
    <SiteLayout title="Home">
        <div class="flex flex-col items-center justify-center min-h-[60vh]">
            <h1 class="text-4xl font-bold mb-4">Welcome</h1>
            <p class="text-muted-foreground mb-8">This is the public home page.</p>
            <div class="flex space-x-4">
                <template v-if="$page.props.user">
                    <Button as-child>
                        <Link href="/admin">Go to Dashboard</Link>
                    </Button>
                    <Button as-child variant="outline">
                        <Link href="/logout">Logout</Link>
                    </Button>
                </template>
                <template v-else>
                    <Button as-child>
                        <Link href="/login">Login</Link>
                    </Button>
                    <Button as-child variant="outline">
                        <Link href="/register">Register</Link>
                    </Button>
                </template>
            </div>
        </div>
    </SiteLayout>
</template>

<script setup>
import { Link } from '@inertiajs/vue3';
import SiteLayout from '@/components/site/layout/SiteLayout.vue';
import { Button } from '@/components/ui/button';
</script>
