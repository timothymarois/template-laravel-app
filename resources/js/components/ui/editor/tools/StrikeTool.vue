<template>
    <EditorToolButton
        :isActive="editor?.isActive('strike')"
        tooltip="Strikethrough"
        @click="editor?.chain().focus().toggleStrike().run()"
    >
        <IconStrikethrough class="size-5" />
    </EditorToolButton>
</template>

<script setup lang="ts">
import { IconStrikethrough } from '@tabler/icons-vue';
import EditorToolButton from './EditorToolButton.vue';

defineProps({ editor: Object });
</script>
