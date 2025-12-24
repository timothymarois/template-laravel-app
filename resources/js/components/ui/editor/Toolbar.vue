<template>
    <div class="flex items-center justify-between p-2 py-1" :class="rootClass">
        <div class="flex items-center space-x-1">
            <component
                v-for="tool in resolvedTools"
                :is="allTools[tool]"
                :key="tool"
                :editor="editor"
            />
            <slot :editor="editor" />
        </div>
    </div>
</template>

<script setup lang="ts">
import type { Component } from 'vue';
import { computed } from 'vue';
import BoldTool from './tools/BoldTool.vue';
import ItalicTool from './tools/ItalicTool.vue';
import StrikeTool from './tools/StrikeTool.vue';
import BulletListTool from './tools/BulletListTool.vue';
import OrderedListTool from './tools/OrderedListTool.vue';
import LinkTool from './tools/LinkTool.vue';
import ClearFormattingTool from './tools/ClearFormattingTool.vue';

const props = defineProps({
    editor: Object,
    options: {
        type: Array as () => string[],
        default: () => ['bold', 'italic', 'strike', 'bullet', 'ordered', 'link', 'clear']
    },
    rootClass: {
        type: String,
        default: ''
    },
    /**
     * Custom tool components to register.
     * Keys are tool names (used in options), values are Vue components.
     * Custom tools receive `editor` as a prop.
     *
     * @example
     * { underline: UnderlineTool, image: ImageTool }
     */
    customTools: {
        type: Object as () => Record<string, Component>,
        default: () => ({})
    }
});

// Built-in tool components
const builtInTools: Record<string, Component> = {
    bold: BoldTool,
    italic: ItalicTool,
    strike: StrikeTool,
    bullet: BulletListTool,
    ordered: OrderedListTool,
    link: LinkTool,
    clear: ClearFormattingTool
};

// Merge built-in tools with custom tools (custom tools can override built-in)
const allTools = computed(() => ({
    ...builtInTools,
    ...props.customTools
}));

// Filter options to only include tools that exist
const resolvedTools = computed(() =>
    props.options.filter(tool => allTools.value[tool])
);
</script>
