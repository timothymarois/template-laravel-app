<template>
    <ButtonGroup
        unstyled
        v-bind="bindProps"
        :pt="mergedPt"
        :ptOptions="{ mergeProps: ptViewMerge }"
    >
        <template v-for="(_, slotName) in $slots" v-slot:[slotName]="slotProps">
            <slot :name="slotName" v-bind="slotProps ?? {}" />
        </template>
    </ButtonGroup>
</template>

<script setup lang="ts">
import ButtonGroup, { type ButtonGroupPassThroughOptions, type ButtonGroupProps } from 'primevue/buttongroup';
import { ref, useAttrs } from 'vue';
import { ptViewMerge } from '../utils';
import { usePrimeBindings } from '../composables';

interface Props extends /* @vue-ignore */ ButtonGroupProps {}
const props = defineProps<Props>();
const attrs = useAttrs();

const theme = ref<ButtonGroupPassThroughOptions>({
    root: `*:rounded-none *:first:rounded-s-[var(--p-content-border-radius)] *:last:rounded-e-[var(--p-content-border-radius)]
        *:focus-visible:relative *:focus-visible:z-10 *:not-last:border-r-0
        *:p-outlined:border-surface-300 *:enabled:hover:p-outlined:border-surface-400 *:enabled:active:p-outlined:border-surface-300
        dark:*:p-outlined:border-surface-700 dark:*:enabled:hover:p-outlined:border-surface-600 dark:*:enabled:active:p-outlined:border-surface-600`
});

const { bindProps, mergedPt } = usePrimeBindings(props, attrs, theme);

</script>
