<template>
    <LayoutSharedApp title="Editor" :pageTabs="pageTabs">
        <div class="space-y-4">
            <Card pt:content:class="p-0">
                <template #content>
                    <Editor v-model="editContent" textOnly placeholder="Start typing for text output..." />
                </template>
            </Card>
            <Card v-if="editContent">
                <template #content>
                    <Content :content="editContent" />
                </template>
            </Card>
            <Card v-if="editContent">
                <template #content>
                    <Textarea v-model="editContent" readonly fluid rows="10" />
                </template>
            </Card>
        </div>
    </LayoutSharedApp>
</template>

<script setup>
import Editor from '@atlas/components/Editor/Editor.vue';
import Content from '@atlas/components/Editor/Content.vue';

const editContent = ref('');

const pageTabs = [
    { title: 'Overview', href: '/components/editor' },
    { title: 'Variants', href: '/components/editor/variant' },
    { title: 'Text', href: '/components/editor/text' },
];
</script>
