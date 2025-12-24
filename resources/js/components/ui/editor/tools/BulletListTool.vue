<template>
    <EditorToolButton
        :isActive="editor?.isActive('bulletList')"
        tooltip="Bullet list"
        @click="editor?.chain().focus().toggleBulletList().run()"
    >
        <IconList class="size-5" />
    </EditorToolButton>
</template>

<script setup lang="ts">
import { IconList } from '@tabler/icons-vue';
import EditorToolButton from './EditorToolButton.vue';

defineProps({ editor: Object });
</script>
