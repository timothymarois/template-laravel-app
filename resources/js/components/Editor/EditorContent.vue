<template>
    <div class="atlas-editor-content text-black dark:text-white" v-html="parseContent" />
</template>

<script setup>
import { computed } from 'vue';

const { content } = defineProps({
    content: {
        type: String,
        required: true
    }
});

const parseContent = computed(() => {
    if (content === null) return '';
    return content.replace(/\n/g, '<br>');
});

</script>

<style scoped>
.atlas-editor-content {
    font-size: 0.875rem; /* Tailwind's text-sm */
    line-height: 1.425;  /* Tailwind's leading-relaxed */
}
.atlas-editor-content p:empty {
    height: 1rem;
}
.atlas-editor-content p {
    font-size: 0.875rem; /* Tailwind's text-sm */
}
.atlas-editor-content a {
    color: #4f46e5; /* Tailwind's text-indigo-600 */
    text-decoration: underline;
}
.atlas-editor-content ul,
.atlas-editor-content ol {
    padding-left: 1rem;
    margin-top: 0.125rem;
    margin-bottom: 0.125rem;
}
.atlas-editor-content ul {
    list-style-type: disc;
}
.atlas-editor-content ol {
    list-style-type: decimal;
}
</style>
