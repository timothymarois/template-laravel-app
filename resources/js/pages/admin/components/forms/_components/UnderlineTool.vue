<template>
    <EditorToolButton
        :isActive="editor?.isActive('underline')"
        tooltip="Underline"
        @click="editor?.chain().focus().toggleUnderline().run()"
    >
        <IconUnderline class="size-5" />
    </EditorToolButton>
</template>

<script setup lang="ts">
import { IconUnderline } from '@tabler/icons-vue';
import { EditorToolButton } from '@/components/ui/editor';

defineProps({ editor: Object });
</script>
