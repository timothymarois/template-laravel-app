<template>
    <div class="relative" :class="{ 'w-full': fluid }">
        <span
            v-if="$slots.icon"
            class="absolute left-3 top-1/2 -translate-y-1/2 text-muted-foreground pointer-events-none"
        >
            <slot name="icon" />
        </span>
        <InputBase
            ref="inputRef"
            :type="type"
            :modelValue="displayValue"
            :placeholder="placeholder"
            :disabled="disabled"
            :maxlength="computedMaxLength"
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
import { InputBase } from '@/components/ui/input';
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
    maxlength?: number | string;
    /**
     * Custom input formatter for text transformations.
     *
     * @example
     * ```vue
     * <Input v-model="code" :formatter="uppercaseFormatter()" />
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

const inputRef = ref<InstanceType<typeof InputBase> | null>(null);

/**
 * Compute maxlength from formatter if available.
 */
const computedMaxLength = computed(() => {
    if (props.formatter && props.formatter.maxLength !== Infinity) {
        return props.formatter.maxLength;
    }
    return props.maxlength;
});

/**
 * Display value - apply formatter if provided.
 */
const displayValue = computed(() => {
    if (props.formatter) {
        return props.formatter.format(props.modelValue);
    }
    return props.modelValue ?? '';
});

/**
 * Handle input - parse with formatter if provided.
 */
const handleInput = (value: string | number) => {
    if (!props.formatter) {
        emit('update:modelValue', value);
        return;
    }
    const rawValue = props.formatter.parse(String(value));
    emit('update:modelValue', rawValue);
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
 * Expose the input element for external access.
 */
defineExpose({
    inputRef,
    focus: () => {
        const el = inputRef.value?.$el as HTMLInputElement | undefined;
        el?.focus();
    },
});
</script>
