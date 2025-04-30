<template>
    <div class="min-h-screen w-full bg-surface-100 dark:bg-surface-900">
        <Nav />
        <slot />
        <Toast position="bottom-left" />
    </div>
    <ModalEditUser />
    <ModalDeleteUser />
    <ModalTest />
</template>
