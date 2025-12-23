<template>
    <CheckboxBase
        :modelValue="isChecked"
        :disabled="disabled"
        :class="[computedSizeClass, invalidClass, className]"
        @update:modelValue="handleChange"
    />
</template>

<script setup lang="ts">
import { computed } from 'vue';
import CheckboxBase from './CheckboxBase.vue';

interface Props {
    modelValue?: boolean | string[];
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
    'update:modelValue': [value: boolean | string[]];
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
const isChecked = computed(() => {
    if (isArrayMode.value && props.value !== undefined) {
        return (props.modelValue as string[]).includes(props.value);
    }
    return props.modelValue as boolean;
});

// Handle checkbox change
const handleChange = (checked: boolean) => {
    if (isArrayMode.value && props.value !== undefined) {
        const currentArray = props.modelValue as string[];
        if (checked) {
            emit('update:modelValue', [...currentArray, props.value]);
        } else {
            emit('update:modelValue', currentArray.filter(v => v !== props.value));
        }
    } else {
        emit('update:modelValue', checked);
    }
};
</script>
