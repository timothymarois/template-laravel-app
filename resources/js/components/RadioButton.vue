<template>
    <RadioButton
        unstyled
        v-bind="bindProps"
        :pt="mergedPt"
        :ptOptions="{ mergeProps: ptViewMerge }"
    />
</template>

<script setup lang="ts">
import RadioButton, { type RadioButtonPassThroughOptions, type RadioButtonProps } from 'primevue/radiobutton';
import { ref, useAttrs } from 'vue';
import { ptViewMerge } from '../utils';
import { usePrimeBindings } from '../composables';

interface Props extends /* @vue-ignore */ RadioButtonProps {}
const props = defineProps<Props>();
const attrs = useAttrs();

const theme = ref<RadioButtonPassThroughOptions>({
    root: `relative inline-flex select-none w-5 h-5 align-bottom
        p-small:w-4 p-small:h-4
        p-large:w-6 p-large:h-6`,
    input: `peer cursor-pointer disabled:cursor-default appearance-none
        absolute start-0 top-0 w-full h-full m-0 p-0 opacity-0 z-10`,
    box: `flex justify-center items-center rounded-full w-5 h-5
        border border-surface-300 dark:border-surface-700
        bg-surface-0 dark:bg-surface-950
        text-surface-700 dark:text-surface-0
        peer-enabled:peer-hover:border-surface-500 dark:peer-enabled:peer-hover:border-surface-600
        p-checked:border-primary-400 p-checked:bg-primary-500
        dark:p-checked:bg-surface-0 dark:p-checked:border-surface-900
        peer-enabled:peer-hover:p-checked:bg-primary-500/70 peer-enabled:peer-hover:p-checked:border-primary-emphasis
        dark:peer-enabled:peer-hover:p-checked:bg-surface-50
        peer-focus-visible:outline-1 peer-focus-visible:outline-offset-2 peer-focus-visible:outline-primary
        p-invalid:border-red-400 dark:p-invalid:border-red-600
        p-disabled:bg-surface-200 dark:p-disabled:bg-surface-400 p-disabled:border-surface-300 dark:p-disabled:border-surface-500
        p-disabled:text-surface-700 dark:p-disabled:text-surface-400
        shadow-[0_1px_2px_0_rgba(18,18,23,0.05)] transition-colors duration-200
        p-small:w-4 p-small:h-4
        p-large:w-6 p-large:h-6`,
    icon: `w-[0.75rem] h-[0.75rem] rounded-full bg-surface-0 transition-transform duration-200
        transform scale-0 p-checked:scale-100
        dark:p-checked:bg-black
        p-disabled:bg-surface-400 dark:p-disabled:bg-surface-600`
});

const { bindProps, mergedPt } = usePrimeBindings(props, attrs, theme);

</script>
