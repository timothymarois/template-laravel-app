<template>
    <Checkbox
        :modelValue="modelValue"
        :disabled="disabled"
        :class="[computedSizeClass, className]"
        @update:modelValue="$emit('update:modelValue', $event)"
    />
</template>

<script setup lang="ts">
import { computed } from 'vue';
import { Checkbox } from '@/components/ui/checkbox';

interface Props {
    modelValue?: boolean;
    disabled?: boolean;
    invalid?: boolean;
    size?: 'small' | 'large' | 'default';
    class?: string;
}

const props = withDefaults(defineProps<Props>(), {
    size: 'default',
});

defineEmits<{
    'update:modelValue': [value: boolean];
}>();

const className = computed(() => props.class || '');

const computedSizeClass = computed(() => {
    if (props.size === 'small') return 'w-4 h-4';
    if (props.size === 'large') return 'w-6 h-6';
    return '';
});
</script>
