<template>
    <EditorToolButton
        :isActive="editor?.isActive('italic')"
        tooltip="Italic"
        @click="editor?.chain().focus().toggleItalic().run()"
    >
        <IconItalic class="size-5" />
    </EditorToolButton>
</template>

<script setup lang="ts">
import { IconItalic } from '@tabler/icons-vue';
import EditorToolButton from './EditorToolButton.vue';

defineProps({ editor: Object });
</script>
