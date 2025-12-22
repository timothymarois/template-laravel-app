<template>
    <Select v-model="modelProxy">
        <SelectTrigger
            :class="[
                computedSizeClass,
                className
            ]"
        >
            <SelectValue :placeholder="placeholder" />
        </SelectTrigger>
        <SelectContent>
            <SelectItem
                v-for="option in options"
                :key="getOptionValue(option)"
                :value="getOptionValue(option)"
            >
                {{ getOptionLabel(option) }}
            </SelectItem>
        </SelectContent>
    </Select>
</template>

<script setup lang="ts">
import { computed } from 'vue';
import {
    SelectBase as Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';

interface Props {
    modelValue?: any;
    options?: any[];
    optionLabel?: string;
    optionValue?: string;
    placeholder?: string;
    disabled?: boolean;
    size?: 'small' | 'large' | 'default';
    class?: string;
}

const props = withDefaults(defineProps<Props>(), {
    options: () => [],
    optionLabel: 'label',
    optionValue: 'value',
    size: 'default',
});

const emit = defineEmits<{
    'update:modelValue': [value: any];
}>();

const className = computed(() => props.class || '');

const modelProxy = computed({
    get: () => props.modelValue,
    set: (val) => emit('update:modelValue', val),
});

const getOptionLabel = (option: any) => {
    if (typeof option === 'string' || typeof option === 'number') return option;
    return option[props.optionLabel];
};

const getOptionValue = (option: any) => {
    if (typeof option === 'string' || typeof option === 'number') return option;
    return option[props.optionValue];
};

const computedSizeClass = computed(() => {
    if (props.size === 'small') return 'h-8';
    if (props.size === 'large') return 'h-10';
    return '';
});
</script>
