<template>
    <div class="flex items-center justify-between p-2 py-1" :class="rootClass">
        <div class="flex items-center space-x-1">
            <component
                v-for="tool in resolvedTools"
                :is="toolComponents[tool]"
                :key="tool"
                :editor="editor"
            />
            <slot :editor="editor" />
        </div>
    </div>
</template>

<script setup>
import { computed } from 'vue';
import BoldTool from './Tools/BoldTool.vue';
import ItalicTool from './Tools/ItalicTool.vue';
import StrikeTool from './Tools/StrikeTool.vue';
import BulletListTool from './Tools/BulletListTool.vue';
import OrderedListTool from './Tools/OrderedListTool.vue';
import LinkTool from './Tools/LinkTool.vue';
import ClearFormattingTool from './Tools/ClearFormattingTool.vue';

const props = defineProps({
    editor: Object,
    options: {
        type: Array,
        default: () => ['bold', 'italic', 'strike', 'bullet', 'ordered', 'link', 'clear']
    },
    rootClass: {
        type: String,
        default: ''
    }
});

const toolComponents = {
    bold: BoldTool,
    italic: ItalicTool,
    strike: StrikeTool,
    bullet: BulletListTool,
    ordered: OrderedListTool,
    link: LinkTool,
    clear: ClearFormattingTool
};

const resolvedTools = computed(() => props.options.filter(tool => toolComponents[tool]));

</script>
