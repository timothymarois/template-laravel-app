<template>
    <Slider
        unstyled
        v-bind="bindProps"
        :pt="mergedPt"
        :ptOptions="{ mergeProps: ptViewMerge }"
    >
        <template v-for="(_, slotName) in $slots" v-slot:[slotName]="slotProps">
            <slot :name="slotName" v-bind="slotProps ?? {}" />
        </template>
    </Slider>
</template>

<script setup lang="ts">
import Slider, { type SliderPassThroughOptions, type SliderProps } from 'primevue/slider';
import { ref, useAttrs } from 'vue';
import { ptViewMerge } from '../utils';
import { usePrimeBindings } from '../composables';

interface Props extends /* @vue-ignore */ SliderProps {}
const props = defineProps<Props>();
const attrs = useAttrs();

const handleCommon = `cursor-grab touch-none flex items-center justify-center h-[20px] w-[20px]
        bg-surface-200 dark:bg-surface-700 rounded-full
        transition-colors duration-200
        focus-visible:outline focus-visible:outline-1 focus-visible:outline-offset-2 focus-visible:outline-primary
        before:w-[16px] before:h-[16px] before:block before:rounded-full
        before:bg-surface-0 dark:before:bg-surface-950
        before:shadow-[0px_0.5px_0px_0px_rgba(0,0,0,0.08),0px_1px_1px_0px_rgba(0,0,0,0.14)]
        before:transition-colors before:duration-200
        p-horizontal:top-1/2 p-horizontal:-mt-[10px] p-horizontal:-ms-[10px]
        p-vertical:start-1/2 p-vertical:-mb-[10px] p-vertical:-ms-[10px]`;

const theme = ref<SliderPassThroughOptions>({
    root: `relative bg-surface-200 dark:bg-surface-700 rounded-xs
            p-horizontal:h-[3px]
            p-vertical:min-h-[100px] p-vertical:w-[3px]`,
    range: `block bg-primary rounded-xs
            p-horizontal:top-0 p-horizontal:start-0 p-horizontal:h-full
            p-vertical:bottom-0 p-vertical:start-0 p-vertical:w-full`,
    handle: handleCommon,
    startHandler: handleCommon,
    endHandler: handleCommon
});

const { bindProps, mergedPt } = usePrimeBindings(props, attrs, theme);

</script>
