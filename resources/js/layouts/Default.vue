<template>
    <div class="min-h-full w-full bg-surface-100 dark:bg-surface-900">
        <Nav />
        <slot />
        <Toast />
    </div>
</template>

<script setup>
import Toast from 'primevue/toast';
</script>
