<template>
    <EditorToolButton
        :isActive="editor?.isActive('orderedList')"
        tooltip="Numbered list"
        @click="editor?.chain().focus().toggleOrderedList().run()"
    >
        <IconListNumbers class="size-5" />
    </EditorToolButton>
</template>

<script setup lang="ts">
import { IconListNumbers } from '@tabler/icons-vue';
import EditorToolButton from './EditorToolButton.vue';

defineProps({ editor: Object });
</script>
