<template>
    <TextareaBase
        :modelValue="modelValue"
        :placeholder="placeholder"
        :disabled="disabled"
        :rows="rows"
        :class="[
            fluid ? 'w-full' : '',
            invalid ? 'border-destructive focus-visible:ring-destructive' : '',
            resize === 'none' ? 'resize-none' : resize === 'horizontal' ? 'resize-x' : resize === 'vertical' ? 'resize-y' : 'resize',
            props.class,
        ]"
        v-bind="$attrs"
        @update:modelValue="$emit('update:modelValue', $event)"
    />
</template>

<script setup lang="ts">
import { TextareaBase } from '@/components/ui/textarea';

defineOptions({
    inheritAttrs: false,
});

interface Props {
    modelValue?: string;
    placeholder?: string;
    disabled?: boolean;
    fluid?: boolean;
    invalid?: boolean;
    rows?: number;
    resize?: 'none' | 'vertical' | 'horizontal' | 'both';
    class?: string;
}

const props = withDefaults(defineProps<Props>(), {
    rows: 3,
    resize: 'vertical',
});

defineEmits<{
    'update:modelValue': [value: string];
}>();
</script>
