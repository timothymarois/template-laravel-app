<template>
    <div
        class="w-full bg-background border-b border-border z-20 sticky top-0"
        :class="{
            'shadow-[2px_0px_6px_-2px_rgba(0,0,0,0.5)] dark:shadow-[2px_0px_6px_-2px_rgba(0,0,0,0.5)]': !isTop
        }"
    >
        <div class="flex justify-center w-full">
            <div class="w-full" :class="widthClass">
                <div class="flex justify-center items-center w-full px-4">
                    <div class="grow min-w-0">
                        <div v-if="hasTitle" class="flex items-center">
                            <slot name="title">
                                <div class="mr-auto text-xl m-0 p-0 flex items-center">
                                    <ul class="flex items-center pt-4" :class="tabs && tabs.length > 0 ? 'pb-1' : 'pb-4'">
                                        <li v-for="nav in breadcrumbs" :key="nav.href">
                                            <div class="flex items-center text-muted-foreground">
                                                <component
                                                    :is="linkComponent"
                                                    class="hover:underline hover:text-foreground"
                                                    :href="nav.href"
                                                >
                                                    {{ nav.title }}
                                                </component>
                                                <ChevronRight class="w-5 h-5 text-muted-foreground/60" />
                                            </div>
                                        </li>
                                        <li class="text-foreground font-semibold">
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
                                <ul class="flex flex-wrap -mb-[0px] select-none">
                                    <li
                                        v-for="tab in tabs"
                                        :key="tab.href"
                                        class="mr-2"
                                    >
                                        <component
                                            :is="linkComponent"
                                            :href="tab.href"
                                            draggable="false"
                                            class="text-base inline-flex items-center gap-1 py-2 px-4 border-b-4"
                                            :class="[
                                                tab?.disabled
                                                    ? 'text-muted-foreground/60 cursor-not-allowed pointer-events-none select-none border-transparent'
                                                    : [
                                                        'text-muted-foreground hover:text-foreground hover:border-border',
                                                        isActiveTab(tab)
                                                            ? '!text-foreground !border-primary'
                                                            : 'border-transparent'
                                                    ]
                                            ]"
                                        >
                                            <Lock v-if="tab.disabled" :size="16" />
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

<script setup>
import { computed, useSlots, toRefs } from 'vue';
import { Lock, ChevronRight } from 'lucide-vue-next';
import { useScroll } from '@/composables';
import { hasSlotContent } from '@/utils';
import { isPageActive } from '@/utils/vue/inertia';

const slots = useSlots();

const props = defineProps({
    breadcrumbs: {
        type: Array,
        default: () => [],
    },
    tabs: {
        type: Array,
        default: () => [],
    },
    title: {
        type: String,
        default: '',
    },
    linkComponent: {
        type: [String, Object],
        default: 'a',
    },
    widthClass: {
        type: String,
        default: 'max-w-screen-2xl',
    },
    hideTitle: {
        type: Boolean,
        default: false,
    },
});

const { breadcrumbs, tabs, title, linkComponent, widthClass, hideTitle } = toRefs(props);

const isActiveTab = (tab) =>
    tab.parent ? isPageActive(tab.parent) : isPageActive(tab.href, undefined, true);

const hasAction = computed(() => hasSlotContent(slots.action));
const hasTitle = computed(() => hasSlotContent(slots.title) || title.value);
const { isTop } = useScroll('page');
</script>
