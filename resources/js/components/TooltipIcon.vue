<template>
<span v-tooltip.top="tooltip" v-bind="bindProps" :class="mergedPt.root.class">
        <IconInfoSquareRoundedFilled :class="mergedPt.icon.class" />
    </span>
</template>

<script setup lang="ts">
import { IconInfoSquareRoundedFilled } from '@tabler/icons-vue';
import { ref, computed, useAttrs } from 'vue';
import { ptMerge } from '../utils';
import { usePrimeBindings } from '../composables';

interface TooltipDirectivePassThroughOptions {
    root?: any;
    text?: any;
}

interface TooltipIconPassThroughOptions {
    root?: any;
    icon?: any;
    tooltip?: TooltipDirectivePassThroughOptions;
}

interface Props {
    text?: string | null;
    iconClass?: string;
    pt?: TooltipIconPassThroughOptions;
}

const props = withDefaults(defineProps<Props>(), {
    text: null,
    iconClass: 'size-5'
});

const attrs = useAttrs();

const theme = computed<TooltipIconPassThroughOptions>(() => ({
    root: 'cursor-help inline text-surface-500 dark:text-surface-400 hover:text-surface-800 dark:hover:text-surface-300',
    icon: props.iconClass
}));

const { bindProps, mergedPt } = usePrimeBindings(props, attrs, theme, ['text', 'iconClass'] as const);

const tooltipTheme = ref<TooltipDirectivePassThroughOptions>({
    root: 'absolute shadow-md py-0 px-0 max-w-[260px]',
    text: 'text-sm p-2 border border-surface-700 bg-surface-900 text-white dark:bg-surface-0 dark:border-surface-300 dark:text-black rounded-[var(--p-content-border-radius)] whitespace-pre-line'
});

const tooltip = computed(() => ({
    value: props.text,
    pt: ptMerge(tooltipTheme.value, props.pt?.tooltip)
}));
</script>
