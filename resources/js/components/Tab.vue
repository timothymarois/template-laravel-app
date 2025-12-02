<template>
    <Tab
        unstyled
        v-bind="bindProps"
        :pt="mergedPt"
        :ptOptions="{ mergeProps: ptViewMerge }"
    >
        <slot></slot>
    </Tab>
</template>

<script setup lang="ts">
import Tab, { type TabPassThroughOptions, type TabProps } from 'primevue/tab';
import { ref, useAttrs } from 'vue';
import { ptViewMerge } from '../utils';
import { usePrimeBindings } from '../composables';

interface Props extends /* @vue-ignore */ TabProps {}
const props = defineProps<Props>();
const attrs = useAttrs();

const theme = ref<TabPassThroughOptions>({
    root: `flex-shrink-0 cursor-pointer select-none relative whitespace-nowrap py-4 px-6
        border-b-2 border-surface-200 dark:border-surface-700 font-semibold
        text-surface-500 dark:text-surface-400
        transition-colors duration-200 -mb-[2px]
        not-p-active:enabled:hover:text-surface-700 dark:not-p-active:enabled:hover:text-surface-0
        p-active:border-primary-500 p-active:text-primary-500 dark:p-active:border-primary-400 dark:p-active:text-primary-400
        hover:bg-surface-100 dark:hover:bg-surface-800
        disabled:pointer-events-none disabled:opacity-60
        focus-visible:z-10 focus-visible:outline focus-visible:outline-1 focus-visible:outline-offset-[-1px] focus-visible:outline-primary`
});

const { bindProps, mergedPt } = usePrimeBindings(props, attrs, theme);

</script>
