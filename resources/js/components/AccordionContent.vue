<template>
    <AccordionContent
        unstyled
        v-bind="bindProps"
        :pt="mergedPt"
        :ptOptions="{ mergeProps: ptViewMerge }"
    >
        <slot></slot>
    </AccordionContent>
</template>

<script setup lang="ts">
import AccordionContent, { type AccordionContentPassThroughOptions, type AccordionContentProps } from 'primevue/accordioncontent';
import { ref, useAttrs } from 'vue';
import { ptViewMerge } from '../utils';
import { usePrimeBindings } from '../composables';

interface Props extends /* @vue-ignore */ AccordionContentProps {}
const props = defineProps<Props>();
const attrs = useAttrs();

const theme = ref<AccordionContentPassThroughOptions>({
    root: `flex flex-col`,
    content: `text-surface-700 dark:text-surface-0 pt-0 px-6 pb-[1.125rem] text-base`,
    transition: {
        enterFromClass: 'max-h-0',
        enterActiveClass: 'overflow-hidden transition-[max-height] duration-1000 ease-[cubic-bezier(0.42,0,0.58,1)]',
        enterToClass: 'max-h-[1000px]',
        leaveFromClass: 'max-h-[1000px]',
        leaveActiveClass: 'overflow-hidden transition-[max-height] duration-[450ms] ease-[cubic-bezier(0,1,0,1)]',
        leaveToClass: 'max-h-0'
    }
});

const { bindProps, mergedPt } = usePrimeBindings(props, attrs, theme);

</script>
