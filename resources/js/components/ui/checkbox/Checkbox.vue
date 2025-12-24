<template>
    <CheckboxBase
        :modelValue="isChecked"
        :disabled="disabled"
        :class="[computedSizeClass, invalidClass, className]"
        @update:modelValue="handleChange"
    />
</template>

<script setup lang="ts">
/**
 * Checkbox - Enhanced checkbox component with array mode support
 *
 * Usage:
 *   Single boolean: <Checkbox v-model="isChecked" />
 *   Array mode:     <Checkbox v-model="selectedItems" value="item1" />
 *   Indeterminate:  <Checkbox :modelValue="'indeterminate'" />
 *
 * Props:
 *   - modelValue: boolean | 'indeterminate' | string[] - The checked state
 *   - value: string - Value for array mode (checkbox groups)
 *   - disabled: boolean - Disable the checkbox
 *   - invalid: boolean - Show invalid/error state
 *   - size: 'small' | 'default' | 'large' - Checkbox size
 */
import { computed } from 'vue';
import CheckboxBase from './CheckboxBase.vue';

interface Props {
    modelValue?: boolean | 'indeterminate' | string[];
    value?: string;
    disabled?: boolean;
    invalid?: boolean;
    size?: 'small' | 'large' | 'default';
    class?: string;
}

const props = withDefaults(defineProps<Props>(), {
    size: 'default',
});

const emit = defineEmits<{
    'update:modelValue': [value: boolean | 'indeterminate' | string[]];
}>();

const className = computed(() => props.class || '');

const computedSizeClass = computed(() => {
    if (props.size === 'small') return 'w-4 h-4';
    if (props.size === 'large') return 'w-6 h-6';
    return '';
});

const invalidClass = computed(() => {
    return props.invalid ? 'border-destructive data-[state=unchecked]:border-destructive' : '';
});

// Check if we're in array mode (checkbox group)
const isArrayMode = computed(() => Array.isArray(props.modelValue));

// Determine if checkbox is checked
const isChecked = computed((): boolean | 'indeterminate' => {
    if (isArrayMode.value && props.value !== undefined) {
        return (props.modelValue as string[]).includes(props.value);
    }
    return props.modelValue as boolean | 'indeterminate';
});

// Handle checkbox change
const handleChange = (checked: boolean | 'indeterminate') => {
    if (isArrayMode.value && props.value !== undefined) {
        const currentArray = props.modelValue as string[];
        if (checked === true) {
            emit('update:modelValue', [...currentArray, props.value]);
        } else {
            emit('update:modelValue', currentArray.filter(v => v !== props.value));
        }
    } else {
        emit('update:modelValue', checked);
    }
};
</script>
