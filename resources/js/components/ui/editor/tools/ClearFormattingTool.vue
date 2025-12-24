<template>
    <EditorToolButton
        tooltip="Clear formatting"
        @click="editor?.chain().focus().unsetAllMarks().clearNodes().run()"
    >
        <IconClearFormatting class="size-5" />
    </EditorToolButton>
</template>

<script setup lang="ts">
import { IconClearFormatting } from '@tabler/icons-vue';
import EditorToolButton from './EditorToolButton.vue';

defineProps({ editor: Object });
</script>
