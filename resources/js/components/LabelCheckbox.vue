<template>
    <div :class="mergedPt.root.class">
        <Checkbox v-model="model" v-bind="inputAttrs" />
        <label
            v-if="label"
            :for="inputAttrs.inputId"
            :class="mergedPt.label.class"
        >
            {{ label }}
        </label>
    </div>
</template>

<script setup lang="ts">
import Checkbox from './Checkbox.vue';
import { computed, useAttrs } from 'vue';
import { usePrimeBindings } from '../composables';

interface LabelCheckboxPassThroughOptions {
    root?: any;
    input?: any;
    label?: any;
}

interface Props {
    label?: string;
    pt?: LabelCheckboxPassThroughOptions;
}

const props = withDefaults(defineProps<Props>(), {
    label: ''
});

const model = defineModel<any>();
const attrs = useAttrs();

const isChecked = computed(() => {
    if ((attrs as any)?.value !== undefined) {
        const val = (attrs as any).value;
        return Array.isArray(model.value)
            ? model.value.includes(val)
            : model.value === val;
    }
    return !!model.value;
});

const theme = computed<LabelCheckboxPassThroughOptions>(() => ({
    root: 'w-full flex items-center space-x-2',
    input: {},
    label: `block text-sm transition-colors ${
        (attrs as any)?.disabled ? 'cursor-default pointer-events-none' : 'cursor-pointer'
    } ${
        isChecked.value
            ? ((attrs as any)?.disabled
                ? 'text-gray-500 dark:text-gray-400'
                : 'text-black dark:text-white hover:text-primary-700 dark:hover:text-white')
            : ((attrs as any)?.disabled
                ? 'text-gray-400 dark:text-gray-400'
                : 'text-gray-600 dark:text-gray-300 hover:text-primary-600 dark:hover:text-white')
    }`
}));

const { bindProps: inputAttrs, mergedPt } = usePrimeBindings(props, attrs, theme, ['label'] as const);
</script>
