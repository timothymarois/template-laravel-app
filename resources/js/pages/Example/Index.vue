
<template>
    <Head title="Dashboard - Example" />
    <header class="bg-white dark:bg-surface-700 shadow-sm">
        <div class="mx-auto max-w-7xl px-4 py-4 sm:px-6 lg:px-8">
        <h1 class="text-lg font-semibold leading-6 text-gray-900 dark:text-white">Dashboard</h1>
        </div>
    </header>
    <main>
        <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
            <div class="mb-4">This is an example dashboard page.</div>
        </div>
    </main>
</template>
<script setup>
const props = defineProps({
    layout: 'Example'
});
</script>
