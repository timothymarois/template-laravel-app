<template>
    <TabPanel
        unstyled
        v-bind="bindProps"
        :pt="mergedPt"
        :ptOptions="{ mergeProps: ptViewMerge }"
    >
        <slot></slot>
    </TabPanel>
</template>

<script setup lang="ts">
import TabPanel, { type TabPanelPassThroughOptions, type TabPanelProps } from 'primevue/tabpanel';
import { ref, useAttrs } from 'vue';
import { ptViewMerge } from '../utils';
import { usePrimeBindings } from '../composables';

interface Props extends /* @vue-ignore */ TabPanelProps {}
const props = defineProps<Props>();
const attrs = useAttrs();

const theme = ref<TabPanelPassThroughOptions>({
    root: ``
});

const { bindProps, mergedPt } = usePrimeBindings(props, attrs, theme);

</script>
