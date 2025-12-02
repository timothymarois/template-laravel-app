<template>
    <Drawer
        :visible="modelValue"
        position="right"
        blockScroll
        :style="width ? `width: ${width}; max-width: 100%;` : ''"
        pt:root:class="p-right:lg:w-[60%] p-right:md:w-[80%] p-right:sm:w-[90%] p-right:w-full"
        pt:mask:class="p-modal:bg-black/30"
        pt:header:class="p-0 border-b border-surface-300 dark:border-surface-700 dark:bg-surface-800 shadow"
        pt:content:class="p-0 bg-surface-100 dark:bg-surface-900"
        pt:footer:class="py-4 px-6 flex items-center space-x-4 border-t border-surface-300 dark:border-surface-700 bg-white dark:bg-surface-900 shadow-[0_-2px_4px_1px_rgba(0,0,0,0.05)] dark:shadow-[0_-2px_6px_rgba(0,0,0,0.3)]"
        @update:visible="close"
    >
        <template #header>
            <slot name="header">
                <div class="px-6 pt-4 flex flex-col space-y-2" :class="{ 'pb-4': tabs?.length < 1 }">
                    <div class="font-semibold text-black dark:text-gray-200 text-lg z-1">
                        {{ title }}
                    </div>
                    <div v-if="tabs?.length" class="flex flex-wrap -mb-[2px] border-b border-surface-300 dark:border-surface-600 pt-2">
                        <ul class="flex flex-wrap -mb-px">
                            <li v-for="(tab, i) in tabs" :key="i" class="mr-2">
                                <button
                                    class="inline-flex items-center gap-1 py-2 px-4 border-b-4 rounded-t-lg text-base transition-colors cursor-pointer"
                                    :disabled="tab?.disabled"
                                    :class="[
                                        tab?.disabled
                                            ? 'text-gray-400 cursor-not-allowed pointer-events-none border-transparent'
                                            : activeTab === i
                                                ? 'text-gray-900 dark:text-gray-100 border-surface-600 dark:border-surface-300 font-medium'
                                                : 'text-gray-500 hover:text-gray-600 hover:border-surface-500 dark:hover:border-surface-400 dark:hover:text-gray-300 dark:text-gray-300 border-transparent'
                                    ]"
                                    @click="() => { if (!tab?.disabled) activeTab = i }"
                                >
                                    <span class="flex items-center gap-1">
                                        <span
                                            v-if="tab?.disabled && tab.lockTooltipText"
                                            class="pointer-events-auto"
                                        >
                                            <IconLock size="16" />
                                        </span>
                                        <span>{{ tab.title }}</span>
                                    </span>
                                </button>
                            </li>
                        </ul>
                    </div>
                </div>
            </slot>
        </template>
        <div class="w-full p-6">
            <div class="max-w-4xl mx-auto flex flex-col space-y-6 justify-start items-center">
                <slot v-if="!tabs.length || activeTab === 0" />
                <template v-else>
                    <slot :name="`tab-${activeTab}`" />
                </template>
            </div>
        </div>
        <template #footer>
            <slot name="footer">
                <div class="w-full flex flex-col space-y-4">
                    <slot name="message" />
                    <Errors :errors="errors" />
                    <slot name="actions">
                        <div class="flex items-center space-x-4">
                            <Button label="Save" :disabled="loading" :loading="loading" @click="emit('submit')" />
                            <Button text label="Cancel" @click="close" />
                        </div>
                    </slot>
                </div>
            </slot>
        </template>
    </Drawer>
</template>

<script setup lang="ts">
import Errors from './Errors.vue';
import Drawer from './Drawer.vue';
import Button from './Button.vue';
import { IconLock } from '@tabler/icons-vue';
import { ref, watch } from 'vue';

type Tab = { title: string; disabled?: boolean; lockTooltipText?: string };

const props = defineProps({
    modelValue: Boolean,
    title: String,
    loading: Boolean,
    width: String,
    errors: Object as () => Record<string, any>,
    tabs: {
        type: Array as () => Tab[],
        default: () => [],
    },
    modelActiveTab: {
        type: Number,
        default: 0
    }
});

const emit = defineEmits(['update:modelValue', 'submit', 'update:modelActiveTab']);
const activeTab = ref(props.modelActiveTab);

watch(activeTab, val => emit('update:modelActiveTab', val));
watch(() => props.modelActiveTab, val => activeTab.value = val);

const close = () => emit('update:modelValue', false);
</script>
