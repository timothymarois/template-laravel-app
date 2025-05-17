<template>
    <Drawer
        v-model:visible="showModal"
        position="right"
        :showCloseIcon="false"
    >
        <div class="p-4 text-center">This is a a blank drawer</div>
    </Drawer>
</template>

<script setup>
const { activeState } = useModal();

const showModal = activeState('BLANK_DRAWER');
</script>
