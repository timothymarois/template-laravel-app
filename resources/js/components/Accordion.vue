<template>
    <Accordion
        unstyled
        v-bind="bindProps"
        :pt="mergedPt"
        :ptOptions="{ mergeProps: ptViewMerge }"
    >
        <template v-for="(_, slotName) in $slots" v-slot:[slotName]="slotProps">
            <slot :name="slotName" v-bind="slotProps ?? {}" />
        </template>
    </Accordion>
</template>

<script setup lang="ts">
import Accordion, { type AccordionPassThroughOptions, type AccordionProps } from 'primevue/accordion';
import { ref, useAttrs } from 'vue';
import { ptViewMerge } from '../utils';
import { usePrimeBindings } from '../composables';

interface Props extends /* @vue-ignore */ AccordionProps {}
const props = defineProps<Props>();
const attrs = useAttrs();

const theme = ref<AccordionPassThroughOptions>({
    root: ``
});

const { bindProps, mergedPt } = usePrimeBindings(props, attrs, theme);

</script>
