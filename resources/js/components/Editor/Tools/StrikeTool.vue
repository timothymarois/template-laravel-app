<template>
    <Button
        v-tooltip.top="tooltip"
        icon
        text
        size="small"
        class="!px-2"
        :class="{
            'hover:!bg-surface-200/50': true,
            '!bg-surface-300/50 text-black': editor?.isActive('strike')
        }"
        @click="editor?.chain().focus().toggleStrike().run()"
    >
        <div class="flex items-center text-black dark:text-white">
            <IconStrikethrough class="size-5" />
        </div>
    </Button>
</template>

<script setup>
import { IconStrikethrough } from '@tabler/icons-vue';
import Button from '../../Button.vue';

const props = defineProps({ editor: Object });

const tooltip = {
    value: 'Strikethrough',
    pt: {
        root: 'absolute shadow-md py-0 px-0 max-w-[260px]',
        text: 'text-sm p-2 border border-surface-700 bg-surface-900 text-white dark:bg-surface-0 dark:border-surface-300 dark:text-black rounded-[var(--p-content-border-radius)] whitespace-pre-line'
    }
};
</script>
