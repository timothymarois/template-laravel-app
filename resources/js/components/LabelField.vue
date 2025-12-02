<template>
    <div v-bind="bindProps" :class="mergedPt.root.class">
        <label
            v-if="label"
            :for="name"
            :class="mergedPt.label.class"
        >
            <span :class="mergedPt.labelText.class">{{ label }}</span>
            <span v-if="required" :class="mergedPt.required.class">*</span>
            <TooltipIcon v-if="tooltip" :text="tooltip" :class="mergedPt.tooltip.class" />
        </label>
        <div :class="mergedPt.field.class">
            <slot />
            <div v-if="error" :class="mergedPt.error.class">
                {{ error }}
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import TooltipIcon from './TooltipIcon.vue';
import { computed, useAttrs } from 'vue';
import { usePrimeBindings } from '../composables';

interface LabelFieldPassThroughOptions {
    root?: any;
    label?: any;
    labelText?: any;
    required?: any;
    tooltip?: any;
    field?: any;
    error?: any;
}

interface Props {
    label?: string;
    tooltip?: string;
    name?: string;
    required?: boolean;
    error?: string;
    pt?: LabelFieldPassThroughOptions;
}

const props = withDefaults(defineProps<Props>(), {
    label: '',
    tooltip: '',
    name: '',
    required: false,
    error: ''
});

const attrs = useAttrs();

const theme = computed<LabelFieldPassThroughOptions>(() => ({
    root: 'w-full',
    label: 'flex items-center text-sm font-medium leading-6 text-gray-900 dark:text-gray-100 mb-2',
    labelText: '',
    required: 'text-red-500',
    tooltip: 'ml-2',
    field: 'w-full',
    error: 'text-sm text-red-500'
}));

const { bindProps, mergedPt } = usePrimeBindings(props, attrs, theme, ['label', 'tooltip', 'name', 'required', 'error'] as const);
</script>
