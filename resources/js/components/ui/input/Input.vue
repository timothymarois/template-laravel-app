<template>
    <div class="relative" :class="{ 'w-full': fluid }">
        <span
            v-if="$slots.icon"
            class="absolute left-3 top-1/2 -translate-y-1/2 text-muted-foreground pointer-events-none"
        >
            <slot name="icon" />
        </span>
        <Input
            ref="inputRef"
            :type="type"
            :modelValue="displayValue"
            :placeholder="placeholder"
            :disabled="disabled"
            :class="[
                fluid ? 'w-full' : '',
                invalid ? 'border-destructive focus-visible:ring-destructive' : '',
                $slots.icon ? 'pl-9' : '',
                clearable && modelValue ? 'pr-8' : '',
                computedSizeClass,
                props.class
            ]"
            v-bind="$attrs"
            @update:modelValue="handleInput"
            @focus="handleFocus"
            @blur="handleBlur"
        />
        <button
            v-if="clearable && modelValue"
            type="button"
            class="absolute right-2 top-1/2 -translate-y-1/2 text-muted-foreground hover:text-foreground cursor-pointer"
            @click="handleClear"
        >
            <X class="h-4 w-4" />
        </button>
    </div>
</template>

<script setup lang="ts">
import { computed, ref } from 'vue';
import { InputBase as Input } from '@/components/ui/input';
import { X } from 'lucide-vue-next';
import type { InputFormatter } from '@/utils';

defineOptions({
    inheritAttrs: false,
});

interface Props {
    modelValue?: string | number | null;
    type?: string;
    placeholder?: string;
    disabled?: boolean;
    fluid?: boolean;
    invalid?: boolean;
    clearable?: boolean;
    size?: 'small' | 'large' | 'default';
    class?: string;
    /**
     * Input formatter for visual formatting.
     * Formats the value on blur, shows raw value on focus for easy editing.
     *
     * @example
     * ```vue
     * <Input v-model="price" :formatter="currencyFormatter()" />
     * <!-- Blurred: 1,234.00 | Focused: 1234.00 | v-model: 1234.00 -->
     * ```
     */
    formatter?: InputFormatter;
}

const props = withDefaults(defineProps<Props>(), {
    type: 'text',
    size: 'default',
});

const emit = defineEmits<{
    'update:modelValue': [value: string | number | null];
}>();

const inputRef = ref<InstanceType<typeof Input> | null>(null);
const isFocused = ref(false);

/**
 * Display value logic:
 * - When focused: show raw value for easy editing
 * - When blurred: show formatted value
 */
const displayValue = computed(() => {
    if (!props.formatter) {
        return props.modelValue;
    }

    // When focused, show raw value for editing
    if (isFocused.value) {
        return props.modelValue ?? '';
    }

    // When blurred, show formatted value
    return props.formatter.format(props.modelValue);
});

/**
 * Handle input - parse value when formatter is present.
 */
const handleInput = (value: string | number) => {
    if (!props.formatter) {
        emit('update:modelValue', value);
        return;
    }

    // Parse the input to get the raw value
    const rawValue = props.formatter.parse(String(value));
    emit('update:modelValue', rawValue);
};

/**
 * Handle focus - switch to raw value display.
 */
const handleFocus = () => {
    isFocused.value = true;
};

/**
 * Handle blur - switch to formatted value display.
 */
const handleBlur = () => {
    isFocused.value = false;
};

/**
 * Handles the clear button click.
 */
const handleClear = () => {
    emit('update:modelValue', '');
};

const computedSizeClass = computed(() => {
    if (props.size === 'small') return 'h-8';
    if (props.size === 'large') return 'h-10';
    return '';
});

/**
 * Expose the input element for external access (e.g., focus).
 */
defineExpose({
    inputRef,
    focus: () => {
        const el = inputRef.value?.$el as HTMLInputElement | undefined;
        el?.focus();
    },
});
</script>
