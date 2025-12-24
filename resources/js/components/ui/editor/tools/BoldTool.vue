<template>
    <EditorToolButton
        :isActive="editor?.isActive('bold')"
        tooltip="Bold"
        @click="editor?.chain().focus().toggleBold().run()"
    >
        <IconBold class="size-5" />
    </EditorToolButton>
</template>

<script setup lang="ts">
import { IconBold } from '@tabler/icons-vue';
import EditorToolButton from './EditorToolButton.vue';

defineProps({ editor: Object });
</script>
