<template>
    <div class="min-h-screen w-full bg-surface-100 dark:bg-surface-900">
        <Nav />
        <PageHeader :title="title">
            <template #actions>
                <slot name="actions" />
            </template>
        </PageHeader>
        <AtlasFrame page>
            <slot />
        </AtlasFrame>
        <Toast position="bottom-left" />
    </div>
    <ModalEditUser />
    <ModalDeleteUser />
    <ModalTest />
</template>

<script setup>
const props = defineProps({
    title : {
        type: String,
        default: 'Home'
    }
});
</script>
