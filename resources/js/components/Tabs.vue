<template>
    <Tabs
        unstyled
        v-bind="bindProps"
        :pt="mergedPt"
        :ptOptions="{ mergeProps: ptViewMerge }"
    >
        <template v-for="(_, slotName) in $slots" v-slot:[slotName]="slotProps">
            <slot :name="slotName" v-bind="slotProps ?? {}" />
        </template>
    </Tabs>
</template>

<script setup lang="ts">
import Tabs, { type TabsPassThroughOptions, type TabsProps } from 'primevue/tabs';
import { ref, useAttrs } from 'vue';
import { ptViewMerge } from '../utils';
import { usePrimeBindings } from '../composables';

interface Props extends /* @vue-ignore */ TabsProps {}
const props = defineProps<Props>();
const attrs = useAttrs();

const theme = ref<TabsPassThroughOptions>({
    root: `flex flex-col`
});

const { bindProps, mergedPt } = usePrimeBindings(props, attrs, theme);

</script>
