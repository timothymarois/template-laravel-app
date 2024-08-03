<template>
    <button
        type="button"
        class="h-8 w-8 rounded-lg inline-flex justify-center items-center bg-surface-100 dark:bg-surface-800 hover:bg-surface-700 dark:hover:bg-surface-700 text-surface-600 hover:text-surface-900 dark:text-surface-300 dark:hover:text-surface-200 transition-colors duration-200"
        @click="themeStore.toggleDark"
    >
        <i
            v-if="themeStore.isDark"
            class="pi pi-moon"
        />
        <i
            v-else
            class="pi pi-sun"
        />
    </button>
</template>
<script setup>
const themeStore = useThemeStore()
onMounted(() => {
    themeStore.initTheme();
});
</script>
