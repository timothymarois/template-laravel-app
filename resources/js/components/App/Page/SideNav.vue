<template>
    <div
        v-if="items && items.length"
        class="w-64 shrink-0 border-r border-surface-300 dark:border-surface-700 bg-white dark:bg-surface-800 overflow-hidden"
    >
        <ScrollFrame>
            <div class="py-2 px-2 space-y-2">
                <template v-for="item in items" :key="item.label">
                    <div v-if="item.children && item.children.length" class="space-y-2">
                        <div class="pt-2">
                            <span class="px-4 text-sm text-gray-600/70 dark:text-surface-500 font-bold uppercase">{{ item.label }}</span>
                        </div>
                        <ul class="space-y-1">
                            <li v-for="child in item.children" :key="child.href">
                                <component
                                    :is="linkComponent"
                                    :href="child.href"
                                    class="block rounded-[var(--p-content-border-radius)] px-4 py-2 text-sm font-medium"
                                    :class="[
                                        isActive(child)
                                            ? 'bg-surface-100 dark:bg-surface-700 text-primary-800 dark:text-white font-semibold'
                                            : 'text-slate-900 hover:bg-surface-100 dark:text-slate-300 dark:hover:bg-surface-700',
                                        child.disabled && 'opacity-50 cursor-not-allowed pointer-events-none'
                                    ]"
                                >
                                    <span class="flex items-center">
                                        <span class="flex-1">{{ child.label }}</span>
                                        <span
                                            v-if="child.disabled && child.lockTooltipText"
                                            v-tooltip.right="{ value: child.lockTooltipText, pt: tooltipPt }"
                                            class="ml-auto pl-2 pointer-events-auto"
                                        >
                                            <IconLock size="16" />
                                        </span>
                                    </span>
                                </component>
                            </li>
                        </ul>
                    </div>
                    <component
                        v-else
                        :is="linkComponent"
                        :href="item.href"
                        class="block rounded-[var(--p-content-border-radius)] px-4 py-2 text-sm font-medium"
                        :class="[
                            isActive(item)
                                ? 'bg-surface-100 dark:bg-surface-700 text-primary-800 dark:text-white font-semibold'
                                : 'text-slate-900 hover:bg-surface-100 dark:text-slate-300 dark:hover:bg-surface-700',
                            item.disabled && 'opacity-50 cursor-not-allowed pointer-events-none'
                        ]"
                    >
                        <span class="flex items-center">
                            <span class="flex-1">{{ item.label }}</span>
                            <span
                                v-if="item.disabled && item.lockTooltipText"
                                v-tooltip.right="{ value: item.lockTooltipText, pt: tooltipPt }"
                                class="ml-auto pl-2 pointer-events-auto"
                            >
                                <IconLock size="16" />
                            </span>
                        </span>
                    </component>
                </template>
            </div>
        </ScrollFrame>
    </div>
</template>

<script setup lang="ts">
import ScrollFrame from '../../ScrollFrame.vue';
import { IconLock } from '@tabler/icons-vue';
import { isPageActive } from '../../../utils/vue/inertia';

interface NavItem {
    label: string;
    href: string;
    parent?: string;
    children?: NavItem[];
    disabled?: boolean;
    lockTooltipText?: string;
}

interface Props {
    items?: NavItem[];
    linkComponent?: string | object;
}

const props = withDefaults(defineProps<Props>(), {
    items: () => [],
    linkComponent: 'a',
});

const { items, linkComponent } = props;

const tooltipPt = {
    root: 'absolute ml-2 shadow-md py-0 px-0 max-w-[260px]',
    text: 'text-sm p-2 border border-surface-700 bg-surface-900 text-white dark:bg-surface-0 dark:border-surface-300 dark:text-black rounded-[var(--p-content-border-radius)] whitespace-pre-line'
};

const isActive = (item: NavItem) =>
    item.parent ? isPageActive(item.parent) : isPageActive(item.href, undefined, true);
</script>
