<template>
    <LayoutSharedApp title="Editor" :pageTabs="pageTabs">
        <div class="space-y-4">
            <Card pt:content:class="p-0">
                <template #content>
                    <Editor v-model="editContent" placeholder="Start typing..." />
                </template>
            </Card>
            <Card pt:content:class="p-0">
                <template #content>
                    <Editor v-model="editContent" :toolbar="false" placeholder="No toolbar editor" />
                </template>
            </Card>
            <Card pt:content:class="p-0">
                <template #content>
                    <div class="bg-yellow-100/60 w-full flex flex-col">
                        <div>
                            <Editor v-model="editContent" toolbarClass="" />
                        </div>
                        <div class="flex items-center space-x-2 px-4 pb-4">
                            <Button label="Save" raised size="small" />
                            <Button text label="Cancel" size="small" @click="editContent = ''" />
                        </div>
                    </div>
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
