<template>
    <TabPanels
        unstyled
        v-bind="bindProps"
        :pt="mergedPt"
        :ptOptions="{ mergeProps: ptViewMerge }"
    >
        <slot></slot>
    </TabPanels>
</template>

<script setup lang="ts">
import TabPanels, { type TabPanelsPassThroughOptions, type TabPanelsProps } from 'primevue/tabpanels';
import { ref, useAttrs } from 'vue';
import { ptViewMerge } from '../utils';
import { usePrimeBindings } from '../composables';

interface Props extends /* @vue-ignore */ TabPanelsProps {}
const props = defineProps<Props>();
const attrs = useAttrs();

const theme = ref<TabPanelsPassThroughOptions>({
    root: `text-surface-700 dark:text-surface-0
        pt-[0.875rem] pb-[1.125rem] px-6 outline-none text-base`
});

const { bindProps, mergedPt } = usePrimeBindings(props, attrs, theme);

</script>
