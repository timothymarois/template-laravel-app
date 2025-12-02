<template>
    <Checkbox
        unstyled
        v-bind="bindProps"
        :pt="mergedPt"
        :ptOptions="{ mergeProps: ptViewMerge }"
    >
        <template #icon="{ checked, indeterminate, dataP }">
            <CheckIcon v-if="checked" :class="theme.icon" :data-p="dataP" />
            <MinusIcon v-else-if="indeterminate" :class="theme.icon" :data-p="dataP" />
        </template>
    </Checkbox>
</template>

<script setup lang="ts">
import CheckIcon from '@primevue/icons/check';
import MinusIcon from '@primevue/icons/minus';
import Checkbox, { type CheckboxPassThroughOptions, type CheckboxProps } from 'primevue/checkbox';
import { ref, useAttrs } from 'vue';
import { ptViewMerge } from '../utils';
import { usePrimeBindings } from '../composables';

interface Props extends /* @vue-ignore */ CheckboxProps {}
const props = defineProps<Props>();
const attrs = useAttrs();

const theme = ref<CheckboxPassThroughOptions>({
    root: `relative inline-flex select-none w-5 h-5 align-bottom
        p-small:w-4 p-small:h-4
        p-large:w-6 p-large:h-6`,
    input: `peer cursor-pointer disabled:cursor-default appearance-none
        absolute start-0 top-0 w-full h-full m-0 p-0 opacity-0 z-10
        border border-transparent rounded-xs`,
    box: `flex justify-center items-center rounded-sm w-5 h-5
        border border-surface-300 dark:border-surface-700
        bg-surface-0 dark:bg-surface-950
        text-surface-700 dark:text-surface-0
        peer-enabled:peer-hover:border-surface-500 dark:peer-enabled:peer-hover:border-surface-600
        p-checked:border-primary-400 p-checked:bg-primary-500 p-checked:text-primary-contrast
        dark:p-checked:bg-surface-0 dark:p-checked:border-surface-900
        peer-enabled:peer-hover:p-checked:bg-primary-500/70 peer-enabled:peer-hover:p-checked:border-primary-emphasis
        dark:peer-enabled:peer-hover:p-checked:bg-surface-50
        peer-focus-visible:outline-1 peer-focus-visible:outline-offset-2 peer-focus-visible:outline-primary peer-focus-visible:outline
        p-invalid:border-red-400 dark:p-invalid:border-red-600
        p-filled:bg-surface-50 dark:p-filled:bg-surface-800
        p-disabled:bg-surface-200 dark:p-disabled:bg-surface-400 p-disabled:border-surface-300 dark:p-disabled:border-surface-500 p-disabled:text-surface-700 dark:p-disabled:text-surface-400
        shadow-[0_1px_2px_0_rgba(18,18,23,0.05)] transition-colors duration-200
        p-small:w-4 p-small:h-4
        p-large:w-6 p-large:h-6`,
    icon: `text-sm w-[1rem] h-[1rem] transition-none
        p-small:w-[0.875rem] p-small:h-[0.875rem]
        p-large:w-[1.125rem] p-large:h-[1.125rem]
        text-white dark:text-black p-disabled:text-surface-400 dark:p-disabled:text-surface-600`
});

const { bindProps, mergedPt } = usePrimeBindings(props, attrs, theme);

</script>
