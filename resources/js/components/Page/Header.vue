<template>
    <header class="bg-white dark:bg-surface-700 shadow-md">
        <div class="flex justify-between items-center mx-auto max-w-screen-2xl px-6 py-4">
            <h1 class="text-lg font-semibold leading-6 text-gray-900 dark:text-white">{{ props.title }}</h1>
            <slot name="actions" />
        </div>
    </header>
</template>

<script setup>
const props = defineProps({
    title: String
});
</script>
