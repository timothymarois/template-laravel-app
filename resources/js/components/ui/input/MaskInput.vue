<template>
    <div class="relative" :class="{ 'w-full': fluid }">
        <span
            v-if="$slots.icon"
            class="absolute left-3 top-1/2 -translate-y-1/2 text-muted-foreground pointer-events-none"
        >
            <slot name="icon" />
        </span>
        <input
            ref="inputRef"
            v-model="displayValue"
            v-maska="maskaOptions"
            :type="type"
            :placeholder="placeholder"
            :disabled="disabled"
            :class="inputClasses"
            v-bind="$attrs"
            @maska="onMaska"
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
/**
 * MaskedInput - Input component with mask formatting using maska library.
 *
 * @see https://beholdr.github.io/maska/v3/#/
 *
 * Mask characters:
 * - `#` = digit (0-9)
 * - `A` = letter (a-z, A-Z), auto-uppercased
 * - `a` = letter (a-z, A-Z), auto-lowercased
 * - `*` = alphanumeric (a-z, A-Z, 0-9)
 * - Any other character = literal (auto-inserted)
 *
 * @example
 * ```vue
 * <MaskedInput v-model="phone" mask="(###) ###-####" />
 * <MaskedInput v-model="ssn" mask="###-##-####" />
 * <MaskedInput v-model="plate" mask="AAA-####" />
 * ```
 */
import { computed, ref, useSlots, watch } from 'vue';
import { X } from 'lucide-vue-next';
import { vMaska } from 'maska/vue';
import { cn } from '@/utils';

interface MaskaDetail {
    masked: string;
    unmasked: string;
    completed: boolean;
}

defineOptions({
    inheritAttrs: false,
});

interface Props {
    modelValue?: string | number | null;
    /**
     * Mask pattern for formatting.
     * - `#` = digit (0-9)
     * - `A` = letter (a-z, A-Z), auto-uppercased
     * - `a` = letter (a-z, A-Z), auto-lowercased
     * - `*` = alphanumeric (a-z, A-Z, 0-9)
     * - Any other character = literal (auto-inserted)
     */
    mask: string;
    /**
     * When true, v-model stores the formatted value (e.g., "(555) 123-4567").
     * When false (default), v-model stores raw characters (e.g., "5551234567").
     */
    maskValue?: boolean;
    type?: string;
    placeholder?: string;
    disabled?: boolean;
    fluid?: boolean;
    invalid?: boolean;
    clearable?: boolean;
    size?: 'small' | 'large' | 'default';
    class?: string;
}

const props = withDefaults(defineProps<Props>(), {
    type: 'text',
    size: 'default',
    maskValue: false,
});

const emit = defineEmits<{
    'update:modelValue': [value: string | null];
}>();

const slots = useSlots();
const inputRef = ref<HTMLInputElement | null>(null);

/**
 * Local display value - maska manages this via v-model.
 * Initialized from modelValue - maska will format it on mount.
 */
const displayValue = ref(String(props.modelValue ?? ''));

/**
 * Track the last emitted value to avoid re-syncing when we caused the change.
 */
let lastEmittedValue = props.modelValue;

/**
 * Watch for external modelValue changes and sync to display.
 */
watch(
    () => props.modelValue,
    (newValue) => {
        // Only sync if the change came from outside (not from our own emit)
        if (newValue !== lastEmittedValue) {
            displayValue.value = String(newValue ?? '');
            lastEmittedValue = newValue;
        }
    }
);

/**
 * Maska options with custom tokens matching our API.
 */
const maskaOptions = computed(() => ({
    mask: props.mask,
    tokens: {
        '#': { pattern: /[0-9]/ },
        'A': { pattern: /[a-zA-Z]/, transform: (char: string) => char.toUpperCase() },
        'a': { pattern: /[a-zA-Z]/, transform: (char: string) => char.toLowerCase() },
        '*': { pattern: /[a-zA-Z0-9]/ },
    },
}));

/**
 * Computed input classes.
 */
const inputClasses = computed(() => {
    const sizeClass = props.size === 'small' ? 'h-8' : props.size === 'large' ? 'h-10' : '';

    return cn(
        'flex h-9 w-full rounded-md border border-input bg-background px-3 py-1 text-sm transition-all',
        'file:border-0 file:bg-transparent file:text-foreground file:text-sm file:font-medium',
        'placeholder:text-muted-foreground hover:border-foreground/50',
        'focus-visible:outline-none focus-visible:border-foreground/50',
        'disabled:cursor-not-allowed disabled:opacity-50 disabled:hover:border-input',
        props.fluid ? 'w-full' : '',
        props.invalid ? 'border-destructive focus-visible:ring-destructive' : '',
        slots.icon ? 'pl-9' : '',
        props.clearable && props.modelValue ? 'pr-8' : '',
        sizeClass,
        props.class
    );
});

/**
 * Handle maska events - emits raw or masked value based on maskValue prop.
 */
const onMaska = (event: CustomEvent<MaskaDetail>) => {
    const { masked, unmasked } = event.detail;
    const valueToEmit = props.maskValue ? masked : unmasked;
    lastEmittedValue = valueToEmit;
    emit('update:modelValue', valueToEmit);
};

/**
 * Handle clear button click.
 */
const handleClear = () => {
    displayValue.value = '';
    lastEmittedValue = '';
    emit('update:modelValue', '');
};

/**
 * Expose focus method.
 */
defineExpose({
    focus: () => inputRef.value?.focus(),
    inputRef,
});
</script>
