<template>
    <ToggleSwitch
        unstyled
        v-bind="bindProps"
        :pt="mergedPt"
        :ptOptions="{ mergeProps: ptViewMerge }"
    >
        <template v-for="(_, slotName) in $slots" v-slot:[slotName]="slotProps">
            <slot :name="slotName" v-bind="slotProps ?? {}" />
        </template>
    </ToggleSwitch>
</template>

<script setup lang="ts">
import ToggleSwitch, { type ToggleSwitchPassThroughOptions, type ToggleSwitchProps } from 'primevue/toggleswitch';
import { ref, useAttrs } from 'vue';
import { ptViewMerge } from '../utils';
import { usePrimeBindings } from '../composables';

interface Props extends /* @vue-ignore */ ToggleSwitchProps {}
const props = defineProps<Props>();
const attrs = useAttrs();

const theme = ref<ToggleSwitchPassThroughOptions>({
    root: `inline-block w-10 h-6`,
    input: `peer cursor-pointer disabled:cursor-default appearance-none absolute top-0 start-0 w-full h-full m-0 p-0 opacity-0 z-10 rounded-[30px]`,
    slider: `inline-block w-full h-full rounded-[30px] shadow-[0_1px_2px_0_rgba(18,18,23,0.05)]
        bg-surface-300 dark:bg-surface-700
        border border-surface-300 dark:border-surface-700
        transition-colors duration-200
        peer-enabled:peer-hover:bg-surface-400 dark:peer-enabled:peer-hover:bg-surface-600
        p-checked:bg-primary-500 dark:p-checked:bg-surface-0
        p-checked:border-primary-500 dark:p-checked:border-surface-0
        peer-enabled:peer-hover:p-checked:bg-primary-600 dark:peer-enabled:peer-hover:p-checked:bg-surface-50
        peer-enabled:peer-hover:p-checked:border-primary-600 dark:peer-enabled:peer-hover:p-checked:border-surface-50
        peer-enabled:peer-active:p-checked:bg-primary-700 dark:peer-enabled:peer-active:p-checked:bg-surface-100
        peer-enabled:peer-active:p-checked:border-primary-700 dark:peer-enabled:peer-active:p-checked:border-surface-100
        p-invalid:border-red-400 dark:p-invalid:border-red-600
        p-disabled:border-surface-200 p-disabled:p-checked:border-surface-100
        p-disabled:bg-surface-200 dark:p-disabled:bg-surface-600 dark:p-disabled:border-surface-800 dark:p-disabled:p-checked:border-surface-400
        p-disabled:p-checked:bg-primary-300 dark:p-disabled:p-checked:bg-surface-300
        peer-focus-visible:outline peer-focus-visible:outline-1 peer-focus-visible:outline-offset-2 peer-focus-visible:outline-primary`,
    handle: `absolute top-1/2 flex justify-center items-center
        bg-surface-0 dark:bg-surface-400
        text-surface-500 dark:text-surface-900
        w-4 h-4 start-1 -mt-2 rounded-full
        transition-[background,color,left] duration-200
        p-checked:bg-surface-0 dark:p-checked:bg-surface-900 p-checked:start-5
        p-disabled:p-checked:bg-primary-100 p-disabled:bg-surface-400 dark:p-disabled:bg-surface-400 dark:p-disabled:p-checked:bg-surface-500`
});

const { bindProps, mergedPt } = usePrimeBindings(props, attrs, theme);

</script>
