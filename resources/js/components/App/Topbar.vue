<template>
    <div v-bind="bindProps" :class="mergedPt.root.class">
        <slot />
    </div>
</template>

<script setup lang="ts">
import { computed, useAttrs } from 'vue';
import { usePrimeBindings } from '../../composables';

interface TopbarPassThroughOptions {
    root?: any;
}

interface Props {
    pt?: TopbarPassThroughOptions;
}

const props = defineProps<Props>();
const attrs = useAttrs();

const theme = computed<TopbarPassThroughOptions>(() => ({
    root: 'h-[56px] bg-white border-b border-surface-300 dark:bg-surface-900 dark:border-surface-700 flex items-center w-full'
}));

const { bindProps, mergedPt } = usePrimeBindings(props, attrs, theme);



</script>
