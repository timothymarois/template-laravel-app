<template>
    <button
        type="button"
        class="h-8 w-8 rounded-lg inline-flex justify-center items-center bg-surface-100 dark:bg-surface-800 hover:bg-surface-700 dark:hover:bg-surface-700 text-surface-600 hover:text-surface-900 dark:text-surface-300 dark:hover:text-surface-200 transition-colors duration-200"
        @click="toggleDark"
    >
        <i
            v-if="isDark"
            class="pi pi-moon"
        />
        <i
            v-else
            class="pi pi-sun"
        />
    </button>
</template>
<script setup>
const { initTheme, toggleDark, isDark } = useTheme()
onMounted(() => {
    initTheme();
});
</script>
