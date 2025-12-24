<template>
    <div class="relative inline-flex items-center" :class="{ 'w-full': fluid }">
        <button
            type="button"
            :disabled="disabled || (min !== undefined && (modelValue ?? 0) <= min)"
            class="inline-flex items-center justify-center rounded-l-md border border-r-0 bg-background hover:bg-accent hover:text-accent-foreground cursor-pointer disabled:pointer-events-none disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
            :class="[buttonSizeClass, invalid ? 'border-destructive' : 'border-input']"
            @click="decrement"
        >
            <Minus class="size-4" />
        </button>
        <input
            type="number"
            :value="modelValue"
            :min="min"
            :max="max"
            :step="step"
            :disabled="disabled"
            :placeholder="placeholder"
            class="flex border bg-background text-center transition-all placeholder:text-muted-foreground hover:border-foreground/50 focus-visible:outline-none focus-visible:border-foreground/50 disabled:cursor-not-allowed disabled:opacity-50 disabled:hover:border-input [appearance:textfield] [&::-webkit-outer-spin-button]:appearance-none [&::-webkit-inner-spin-button]:appearance-none"
            :class="[inputSizeClass, fluid ? 'w-full' : 'w-20', invalid ? 'border-destructive hover:border-destructive focus-visible:border-destructive' : 'border-input']"
            @input="onInput"
            @blur="onBlur"
        />
        <button
            type="button"
            :disabled="disabled || (max !== undefined && (modelValue ?? 0) >= max)"
            class="inline-flex items-center justify-center rounded-r-md border border-l-0 bg-background hover:bg-accent hover:text-accent-foreground cursor-pointer disabled:pointer-events-none disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
            :class="[buttonSizeClass, invalid ? 'border-destructive' : 'border-input']"
            @click="increment"
        >
            <Plus class="size-4" />
        </button>
    </div>
</template>

<script setup lang="ts">
/**
 * NumberInput - Numeric input with increment/decrement buttons
 *
 * Usage:
 *   <NumberInput v-model="quantity" :min="0" :max="100" />
 *   <NumberInput v-model="price" :step="0.01" :min="0" />
 *
 * Props:
 *   - modelValue: number - The current value
 *   - min: number - Minimum allowed value
 *   - max: number - Maximum allowed value
 *   - step: number - Increment/decrement step (default: 1)
 *   - disabled: boolean - Disable the input
 *   - placeholder: string - Placeholder text
 *   - size: 'small' | 'default' | 'large' - Input size
 *   - fluid: boolean - Full width
 *   - invalid: boolean - Show invalid state
 */
import { computed } from 'vue';
import { Minus, Plus } from 'lucide-vue-next';

interface Props {
    modelValue?: number | null;
    min?: number;
    max?: number;
    step?: number;
    disabled?: boolean;
    placeholder?: string;
    size?: 'small' | 'default' | 'large';
    fluid?: boolean;
    invalid?: boolean;
}

const props = withDefaults(defineProps<Props>(), {
    step: 1,
    size: 'default',
});

const emit = defineEmits<{
    'update:modelValue': [value: number | null];
}>();

const buttonSizeClass = computed(() => {
    if (props.size === 'small') return 'h-8 w-8';
    if (props.size === 'large') return 'h-10 w-10';
    return 'h-9 w-9';
});

const inputSizeClass = computed(() => {
    if (props.size === 'small') return 'h-8 text-sm';
    if (props.size === 'large') return 'h-10 text-base';
    return 'h-9 text-sm';
});

const clampValue = (value: number): number => {
    let clamped = value;
    if (props.min !== undefined && clamped < props.min) clamped = props.min;
    if (props.max !== undefined && clamped > props.max) clamped = props.max;
    return clamped;
};

const increment = () => {
    const current = props.modelValue ?? 0;
    const newValue = clampValue(current + props.step);
    emit('update:modelValue', newValue);
};

const decrement = () => {
    const current = props.modelValue ?? 0;
    const newValue = clampValue(current - props.step);
    emit('update:modelValue', newValue);
};

const onInput = (event: Event) => {
    const target = event.target as HTMLInputElement;
    const value = target.value === '' ? null : parseFloat(target.value);
    if (value === null || !isNaN(value)) {
        emit('update:modelValue', value);
    }
};

const onBlur = () => {
    if (props.modelValue !== null && props.modelValue !== undefined) {
        const clamped = clampValue(props.modelValue);
        if (clamped !== props.modelValue) {
            emit('update:modelValue', clamped);
        }
    }
};
</script>
