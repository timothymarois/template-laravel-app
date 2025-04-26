<template>
    <div class="min-h-screen w-full bg-surface-100 dark:bg-surface-900">
        <Nav />
        <slot />
        <Toast />
        <Toast
            position="bottom-left"
            group="bl"
        />
    </div>
</template>
