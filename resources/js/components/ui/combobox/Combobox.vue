<template>
    <ComboboxBase
        v-model="modelProxy"
        :options="options"
        :option-label="optionLabel"
        :option-value="optionValue"
        :placeholder="placeholder"
        :search-placeholder="searchPlaceholder"
        :empty-text="emptyText"
        :loading-text="loadingText"
        :disabled="disabled"
        :multiple="multiple"
        :clearable="clearable"
        :loading="loading"
        :debounce="debounce"
        :disable-filter="disableFilter"
        :fluid="fluid"
        :trigger-class="computedTriggerClass"
        :content-class="contentClass"
        @search="$emit('search', $event)"
    >
        <template v-if="$slots.option" #option="slotProps">
            <slot name="option" v-bind="slotProps" />
        </template>
    </ComboboxBase>
</template>

<script setup lang="ts">
import { computed } from 'vue';
import ComboboxBase from './ComboboxBase.vue';
import { cn } from '@/utils';

interface Props {
    modelValue?: any;
    options?: any[];
    optionLabel?: string;
    optionValue?: string;
    placeholder?: string;
    searchPlaceholder?: string;
    emptyText?: string;
    loadingText?: string;
    disabled?: boolean;
    multiple?: boolean;
    clearable?: boolean;
    loading?: boolean;
    /** Debounce delay in ms for search event */
    debounce?: number;
    /** Disable client-side filtering - use for async/server-side search */
    disableFilter?: boolean;
    fluid?: boolean;
    size?: 'small' | 'large' | 'default';
    invalid?: boolean;
    class?: string;
    contentClass?: string;
}

const props = withDefaults(defineProps<Props>(), {
    options: () => [],
    optionLabel: 'label',
    optionValue: 'value',
    placeholder: 'Select...',
    searchPlaceholder: 'Search...',
    emptyText: 'No results found.',
    loadingText: 'Loading...',
    clearable: true,
    debounce: 300,
    size: 'default',
});

const emit = defineEmits<{
    'update:modelValue': [value: any];
    'search': [query: string];
}>();

const modelProxy = computed({
    get: () => props.modelValue,
    set: (val) => emit('update:modelValue', val),
});

const computedTriggerClass = computed(() => {
    const sizeClass = props.size === 'small' ? 'h-8' : props.size === 'large' ? 'h-10' : '';
    const invalidClass = props.invalid ? 'border-destructive focus:border-destructive' : '';
    return cn(sizeClass, invalidClass, props.class);
});
</script>
