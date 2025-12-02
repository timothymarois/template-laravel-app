<template>
    <div class="bg-primary-500 text-white px-4 rounded-full shadow">
        <div class="grow flex items-center">
            <div class="text-sm font-semibold px-3 py-2" :class="{ 'pr-6': menuItems?.length }">
                Selected: <span class="font-bold">{{ formatNumber(selectedCount) }}</span>
            </div>
            <div v-if="menuItems?.length" class="flex relative justify-center items-center mx-2 py-3 min-h-full before:block before:absolute before:left-1/2 before:top-0 before:transform before:-translate-x-1/2 before:min-h-full before:border-solid before:border-l before:border-white" />
            <div
                v-for="(menuItem, index) in menuItems"
                :key="index"
                v-tooltip.bottom="{
                    value: menuItem?.tooltip ?? null,
                    pt: {
                        root: 'absolute shadow-md py-0 px-0 max-w-[260px] mt-1',
                        text: 'text-sm p-2 border border-surface-700 bg-surface-900 text-white dark:bg-surface-0 dark:border-surface-300 dark:text-black rounded-[var(--p-content-border-radius)] whitespace-pre-line'
                    }
                }"
                class="pl-3"
            >
                <div v-if="menuItem.children && menuItem.children.length" class="relative">
                    <div
                        class="flex items-center space-x-0.5 font-semibold cursor-pointer hover:text-white text-sm hover:bg-primary-600/50 px-3 py-2"
                        @click.stop="toggle"
                    >
                        <div class="flex items-center space-x-0.5 font-semibold hover:cursor-pointer">
                            <div>{{ menuItem.label }}</div>
                            <svg xmlns="http://www.w3.org/2000/svg" class="size-4" viewBox="0 0 24 24">
                                <path
                                    fill="currentColor"
                                    fill-rule="evenodd"
                                    d="M7 9a1 1 0 0 0-.707 1.707l5 5a1 1 0 0 0 1.414 0l5-5A1 1 0 0 0 17 9z"
                                    clip-rule="evenodd"
                                />
                            </svg>
                        </div>
                    </div>
                    <Menu
                        v-if="menuItem?.children && menuItem.children.length"
                        ref="menu"
                        :model="menuItem?.children"
                        size="small"
                        popup
                    >
                        <template #item="{ item, props }">
                            <div
                                class="flex align-items-center"
                                v-bind="props.action"
                                @click="actionClick(item)"
                            >
                                <span v-if="item?.icon" :class="item.icon" />
                                <span
                                    class="text-sm"
                                    :class="{
                                        'text-gray-400': item?.disabled,
                                        'ml-4': item?.icon
                                    }"
                                >{{ item.label }}</span>
                            </div>
                        </template>
                    </Menu>
                </div>
                <div
                    v-else
                    class="flex items-center space-x-0.5 font-semibold cursor-pointer hover:text-white text-sm hover:bg-primary-600/50 px-3 py-2"
                    :class="{ 'text-white/50 pointer-events-none': menuItem?.disabled }"
                    @click="() => actionClick(menuItem)"
                >
                    <span class="text-sm">{{ menuItem.label }}</span>
                </div>
            </div>
            <div>
                <button
                    v-tooltip.bottom="{
                        value: 'Clear selection',
                        pt: {
                            root: 'absolute shadow-md py-0 px-0 max-w-[260px] mt-1',
                            text: 'text-sm p-2 border border-surface-700 bg-surface-900 text-white dark:bg-surface-0 dark:border-surface-300 dark:text-black rounded-[var(--p-content-border-radius)] whitespace-pre-line'
                        }
                    }"
                    type="button"
                    class="flex items-center justify-center hover:bg-primary-600/50 transition px-3 py-2 cursor-pointer opacity-80 hover:opacity-100"
                    @click="$emit('action', 'clear')"
                >
                    <IconX class="size-4" />
                </button>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import { ref } from 'vue';
import { formatNumber } from '../../../utils';
import { IconX } from '@tabler/icons-vue';
import Menu from '../../Menu.vue';

const emit = defineEmits(['action']);

const { selectedCount, menuItems } = defineProps({
    selectedCount: {
        type: Number,
        default: null,
    },
    menuItems: {
        type: Array,
        default: () => []
    },
});

const menu = ref<any>(null);

const actionClick = (item: any) => {
    if (!item?.disabled) {
        emit('action', item?.action);
    }
};

const toggle = (event: any) => {
    if (menu.value) {
        // Menu component is actually an array? In snippet menu.value[0].toggle
        // Wait we changed to ref<any>, but we replicate same logic as snippet: menu.value[0].toggle(event)
        (menu.value as any)[0].toggle(event);
    }
};
</script>
