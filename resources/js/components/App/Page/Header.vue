<template>
    <div
        class="w-full bg-white dark:bg-surface-900 border-b border-surface-300 dark:border-surface-700 z-20 sticky top-0"
        :class="{
            'shadow-[2px_0px_6px_-2px_rgba(0,0,0,0.5)] dark:shadow-[2px_0px_6px_-2px_rgba(0,0,0,0.5)]': !isTop
        }"
    >
        <div class="flex justify-center w-full">
            <div class="w-full" :class="widthClass">
                <div class="flex justify-center items-center w-full px-4">
                    <div class="grow">
                        <div v-if="hasTitle" class="flex items-center">
                            <slot name="title">
                                <div class="mr-auto text-xl m-0 p-0 flex items-center">
                                    <ul class="flex items-center py-4">
                                        <li v-for="nav in breadcrumbs" :key="nav.href">
                                            <div class="flex items-center text-slate-700">
                                                <component
                                                    :is="linkComponent"
                                                    class="hover:underline"
                                                    :href="nav.href"
                                                >
                                                    {{ nav.title }}
                                                </component>
                                                <svg
                                                    aria-hidden="true"
                                                    class="w-6 h-6 text-gray-400"
                                                    fill="currentColor"
                                                    viewBox="0 0 20 20"
                                                    xmlns="http://www.w3.org/2000/svg"
                                                >
                                                    <path
                                                        fill-rule="evenodd"
                                                        d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"
                                                        clip-rule="evenodd"
                                                    />
                                                </svg>
                                            </div>
                                        </li>
                                        <li class="text-gray-900 dark:text-gray-100 font-semibold">
                                            {{ title }}
                                        </li>
                                    </ul>
                                    <div v-if="slots.badge" class="ml-4">
                                        <slot name="badge" />
                                    </div>
                                </div>
                            </slot>
                        </div>
                        <div
                            v-if="tabs && tabs.length > 0"
                            class="flex flex-wrap -mb-px"
                            :class="{ 'pt-2': hideTitle }"
                        >
                            <div class="grow">
                                <ul class="flex flex-wrap -mb-[0px]">
                                    <li
                                        v-for="tab in tabs"
                                        :key="tab.href"
                                        class="mr-2"
                                    >
                                        <component
                                            :is="linkComponent"
                                            :href="tab.href"
                                            class="text-base inline-flex items-center gap-1 py-2 px-4 border-b-4"
                                            :class="[
                                                tab?.disabled
                                                    ? 'text-gray-400 cursor-not-allowed pointer-events-none select-none border-transparent'
                                                    : [
                                                        'text-gray-500 hover:text-black hover:border-surface-300 dark:hover:border-surface-400 dark:hover:text-gray-300 dark:text-gray-300',
                                                        isActiveTab(tab)
                                                            ? '!text-black !border-surface-600 dark:!border-surface-300 dark:!text-gray-100'
                                                            : 'border-transparent'
                                                    ]
                                            ]"
                                        >
                                            <IconLock v-if="tab.disabled" size="16" />
                                            <span>{{ tab.title }}</span>
                                        </component>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div v-if="hasAction" class="py-4">
                        <slot name="action" />
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import { computed, useSlots, toRefs } from 'vue';
import { IconLock } from '@tabler/icons-vue';
import { useScroll } from '../../../composables/useScroll';
import { hasSlotContent } from '../../../utils';
import { isPageActive } from '../../../utils/vue/inertia';

interface Breadcrumb {
    href: string;
    title: string;
}

interface Tab {
    href: string;
    title: string;
    disabled?: boolean;
    parent?: string;
}

interface Props {
    breadcrumbs?: Breadcrumb[];
    tabs?: Tab[];
    title?: string;
    linkComponent?: string | object;
    widthClass?: string;
    hideTitle?: boolean;
}

const slots = useSlots();

const props = withDefaults(defineProps<Props>(), {
    breadcrumbs: () => [],
    tabs: () => [],
    title: '',
    linkComponent: 'a',
    widthClass: 'max-w-screen-2xl',
    hideTitle: false,
});

const { breadcrumbs, tabs, title, linkComponent, widthClass, hideTitle } = toRefs(props);

const isActiveTab = (tab: Tab) =>
    tab.parent ? isPageActive(tab.parent) : isPageActive(tab.href, undefined, true);

const hasAction = computed(() => hasSlotContent(slots.action));
const hasTitle = computed(() => hasSlotContent(slots.title) || title.value);
const { isTop } = useScroll('page');
</script>
