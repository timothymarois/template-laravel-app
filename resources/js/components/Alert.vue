<template>
    <div v-bind="bindProps" :class="mergedPt.root.class">
        <div v-if="!hideIcon" :class="mergedPt.iconContainer.class">
            <slot name="icon">
                <IconExclamationCircleFilled
                    v-if="warning"
                    :class="mergedPt.icon.class"
                />
                <IconInfoCircleFilled
                    v-else
                    :class="mergedPt.icon.class"
                />
            </slot>
        </div>
        <div :class="mergedPt.content.class">
            <slot />
        </div>
        <div v-if="$slots.actions" :class="mergedPt.actions.class">
            <slot name="actions" />
        </div>
    </div>
</template>

<script setup lang="ts">
import { IconInfoCircleFilled, IconExclamationCircleFilled } from '@tabler/icons-vue';
import { useAttrs, computed } from 'vue';
import { usePrimeBindings } from '../composables';


interface AlertPassThroughOptions {
    root?: any;
    iconContainer?: any;
    icon?: any;
    content?: any;
    actions?: any;
}

interface Props {
    warning?: boolean;
    hideIcon?: boolean;
    pt?: AlertPassThroughOptions;
}

const props = withDefaults(defineProps<Props>(), {
    warning: false,
    hideIcon: false,
});
const attrs = useAttrs();

const theme = computed<AlertPassThroughOptions>(() => ({
    root: `w-full p-4 flex border items-center space-x-4 rounded-[var(--p-content-border-radius)] ${
        props.warning
            ? 'bg-yellow-100 border-yellow-300 text-yellow-900 dark:bg-yellow-500/10 dark:border-yellow-600 dark:text-yellow-200'
            : 'bg-surface-50 border-surface-300 text-surface-800 dark:bg-surface-900 dark:border-surface-700 dark:text-surface-0'
    }`,
    iconContainer: '',
    icon: `${
        props.warning
            ? 'size-8 text-yellow-600 dark:text-yellow-400'
            : 'size-8 text-surface-600 dark:text-surface-400'
    }`,
    content: 'flex-1 w-full text-sm',
    actions: 'flex items-center space-x-2',
}));

const { bindProps, mergedPt } = usePrimeBindings(props, attrs, theme);


</script>
