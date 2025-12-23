<template>
    <SelectPopover
        v-model="modelProxy"
        :options="options"
        :option-label="optionLabel"
        :option-value="optionValue"
        :placeholder="placeholder"
        :search-placeholder="searchPlaceholder"
        :empty-text="emptyText"
        :disabled="disabled"
        :multiple="multiple"
        :searchable="searchable"
        :clearable="clearable"
        :chips="chips"
        :chip-variant="chipVariant"
        :fluid="fluid"
        :trigger-class="computedTriggerClass"
        :content-class="contentClass"
    >
        <template v-if="$slots.option" #option="slotProps">
            <slot name="option" v-bind="slotProps" />
        </template>
    </SelectPopover>
</template>

<script setup lang="ts">
import { computed } from 'vue';
import { SelectPopover } from '@/components/ui/select-popover';
import { cn } from '@/utils';

interface Props {
    modelValue?: any;
    options?: any[];
    optionLabel?: string;
    optionValue?: string;
    placeholder?: string;
    searchPlaceholder?: string;
    emptyText?: string;
    disabled?: boolean;
    multiple?: boolean;
    searchable?: boolean;
    clearable?: boolean;
    /** Show chips in trigger for multi-select */
    chips?: boolean;
    /** Chip visual style */
    chipVariant?: 'default' | 'secondary' | 'outline' | 'primary';
    fluid?: boolean;
    size?: 'small' | 'large' | 'default';
    class?: string;
    contentClass?: string;
}

const props = withDefaults(defineProps<Props>(), {
    options: () => [],
    optionLabel: 'label',
    optionValue: 'value',
    placeholder: 'Select...',
    searchPlaceholder: 'Search...',
    emptyText: 'No options found.',
    chips: true,
    chipVariant: 'default',
    size: 'default',
});

const emit = defineEmits<{
    'update:modelValue': [value: any];
}>();

const modelProxy = computed({
    get: () => props.modelValue,
    set: (val) => emit('update:modelValue', val),
});

const computedTriggerClass = computed(() => {
    const sizeClass = props.size === 'small' ? 'h-8' : props.size === 'large' ? 'h-10' : '';
    return cn(sizeClass, props.class);
});
</script>
