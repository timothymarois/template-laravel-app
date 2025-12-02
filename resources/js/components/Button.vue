<template>
    <Button
        unstyled
        v-bind="bindProps"
        :pt="mergedPt"
        :ptOptions="{ mergeProps: ptViewMerge }"
    >
        <template v-for="(_, slotName) in $slots" v-slot:[slotName]="slotProps">
            <slot :name="slotName" v-bind="slotProps ?? {}" />
        </template>
    </Button>
</template>

<script setup lang="ts">
import Button, { type ButtonPassThroughOptions, type ButtonProps } from 'primevue/button';
import { ref, useAttrs } from 'vue';
import { ptViewMerge } from '../utils';
import { usePrimeBindings } from '../composables';

interface Props extends /* @vue-ignore */ ButtonProps {}
const props = defineProps<Props>();
const attrs = useAttrs();

const theme = ref<ButtonPassThroughOptions>({
    root: `inline-flex align-middle cursor-pointer select-none items-center justify-center overflow-hidden relative
        px-4 py-[9px] leading-5 gap-2 rounded-[var(--p-content-border-radius)] disabled:pointer-events-none disabled:opacity-60 transition-colors duration-200
        bg-primary-500 enabled:hover:bg-primary-500/70 enabled:active:bg-primary-500/60 text-white text-md font-semibold p-text:!font-medium
        dark:bg-primary-500 dark:enabled:hover:bg-primary-600 dark:enabled:active:bg-primary-700
        border border-primary-500 enabled:hover:border-primary-500/70 enabled:active:border-primary-500/60
        dark:border-primary-500 dark:enabled:hover:border-primary-600 dark:enabled:active:border-primary-700
        focus-visible:outline-1 focus-visible:outline-offset-2 focus-visible:outline-primary
        p-vertical:flex-col p-fluid:w-full p-fluid:p-icon-only:w-10 p-fluid:p-icon-only:h-10
        p-icon-only:w-10 p-icon-only:h-10 p-icon-only:p-0 p-icon-only:gap-0
        p-icon-only:p-rounded:rounded-full
        p-small:p-icon-only:w-[34px] p-small:p-icon-only:h-[34px] p-small:p-icon-only:p-0
        p-large:p-icon-only:w-[42px] p-large:p-icon-only:h-[42px] p-large:p-icon-only:p-0
        p-small:text-sm p-small:px-[0.625rem] p-small:py-[0.375rem]
        p-large:text-[1.125rem] p-large:px-[0.875rem] p-large:py-[0.625rem]
        p-raised:shadow-sm p-rounded:rounded-full
        p-outlined:bg-transparent enabled:hover:p-outlined:bg-gray-100 enabled:active:p-outlined:bg-gray-200
        p-outlined:border-surface-300 enabled:hover:p-outlined:border-surface-400 enabled:active:p-outlined:border-surface-300
        p-outlined:text-gray-700 enabled:hover:p-outlined:text-gray-700 enabled:active:p-outlined:text-gray-700
        dark:p-outlined:bg-transparent dark:enabled:hover:p-outlined:bg-primary-600 dark:enabled:active:p-outlined:bg-primary-700
        dark:p-outlined:border-primary-500 dark:enabled:hover:p-outlined:border-primary-600 dark:enabled:active:p-outlined:border-primary-700
        dark:p-outlined:text-white dark:enabled:hover:p-outlined:text-white dark:enabled:active:p-outlined:text-white
        p-text:bg-transparent enabled:hover:p-text:bg-gray-100 enabled:active:p-text:bg-primary-100
        p-text:border-transparent enabled:hover:p-text:border-transparent enabled:active:p-text:border-transparent
        p-text:text-gray-700 enabled:hover:p-text:text-gray-700 enabled:active:p-text:text-gray-700
        dark:p-text:bg-transparent dark:enabled:hover:p-text:bg-primary-600 dark:enabled:active:p-text:bg-primary-700
        dark:p-text:border-transparent dark:enabled:hover:p-text:border-transparent dark:enabled:active:p-text:border-transparent
        dark:p-text:text-white dark:enabled:hover:p-text:text-white dark:enabled:active:p-text:text-white
    `,
    loadingIcon: `animate-spin`,
    icon: `p-right:order-1 p-bottom:order-2`,
    label: `
            leading-5
            p-icon-only:invisible p-icon-only:w-0
            p-large:font-bold
        `,
    pcBadge: {
        root: `min-w-4 h-4 leading-4 bg-primary-contrast rounded-full text-primary text-xs font-bold`
    }
});

const { bindProps, mergedPt } = usePrimeBindings(props, attrs, theme);
</script>
