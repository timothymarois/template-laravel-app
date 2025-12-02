<template>
    <AccordionPanel
        unstyled
        v-bind="bindProps"
        :pt="mergedPt"
        :ptOptions="{ mergeProps: ptViewMerge }"
    >
        <slot></slot>
    </AccordionPanel>
</template>

<script setup lang="ts">
import AccordionPanel, { type AccordionPanelPassThroughOptions, type AccordionPanelProps } from 'primevue/accordionpanel';
import { ref, useAttrs } from 'vue';
import { ptViewMerge } from '../utils';
import { usePrimeBindings } from '../composables';

interface Props extends /* @vue-ignore */ AccordionPanelProps {}
const props = defineProps<Props>();
const attrs = useAttrs();

const theme = ref<AccordionPanelPassThroughOptions>({
    root: `flex flex-col border-b border-surface-200 dark:border-surface-700`
});

const { bindProps, mergedPt } = usePrimeBindings(props, attrs, theme);

</script>
