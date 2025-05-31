<template>
    <LayoutSharedApp title="Editor" :pageTabs="pageTabs">
        <div class="space-y-4">
            <Card>
                <template #header>
                    <div class="font-semibold text-gray-900 dark:text-gray-100 text-md flex items-center space-x-2">
                        <div>Editor</div>
                    </div>
                </template>
                <template #content>
                    <div class="border border-surface-300 dark:border-surface-700 rounded-md">
                        <Editor ref="editor" v-model="editContent" />
                    </div>
                </template>
            </Card>
            <Card>
                <template #header>
                    <div class="font-semibold text-gray-900 dark:text-gray-100 text-md flex items-center space-x-2">
                        <div>View content</div>
                    </div>
                </template>
                <template #content>
                    <Content :content="editContent" />
                </template>
            </Card>
            <Card>
                <template #header>
                    <div class="font-semibold text-gray-900 dark:text-gray-100 text-md flex items-center space-x-2">
                        <div>View content (as HTML)</div>
                    </div>
                </template>
                <template #content>
                    <Textarea v-model="editContent" readonly fluid rows="10" />
                </template>
            </Card>
            <Card>
                <template #content>
                    <div class="space-y-2">
                        <div>Custom access to the editor</div>
                        <Button outlined label="Add text" @click="addText" />
                    </div>
                </template>
            </Card>
        </div>
    </LayoutSharedApp>
</template>

<script setup>
import Editor from '@atlas/components/Editor/Editor.vue';
import Content from '@atlas/components/Editor/Content.vue';

const editor = ref(null);
const editContent = ref('');

const pageTabs = [
    { title: 'Overview', href: '/components/editor' },
    { title: 'Variant', href: '/components/editor/variant' },
    { title: 'Text', href: '/components/editor/text' },
];

const addText = () => {
    const editorInstance = editor.value?.editorInstance;
    if (editorInstance) {
        editorInstance.commands.insertContent(' 🚀 Extra content!');
    }
};
</script>
