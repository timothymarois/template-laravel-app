<template>
    <div class="relative" :class="{ 'w-full': fluid }">
        <Input
            :type="type"
            :modelValue="modelValue"
            :placeholder="placeholder"
            :disabled="disabled"
            :class="[
                fluid ? 'w-full' : '',
                invalid ? 'border-destructive focus-visible:ring-destructive' : '',
                clearable && modelValue ? 'pr-8' : '',
                computedSizeClass,
                className
            ]"
            v-bind="$attrs"
            @update:modelValue="$emit('update:modelValue', $event)"
        />
        <button
            v-if="clearable && modelValue"
            type="button"
            class="absolute right-2 top-1/2 -translate-y-1/2 text-muted-foreground hover:text-foreground cursor-pointer"
            @click="$emit('update:modelValue', '')"
        >
            <X class="h-4 w-4" />
        </button>
    </div>
</template>

<script setup lang="ts">
import { computed } from 'vue';
import { InputBase as Input } from '@/components/ui/input';
import { X } from 'lucide-vue-next';

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
}

const props = withDefaults(defineProps<Props>(), {
    type: 'text',
    size: 'default',
});

defineEmits<{
    'update:modelValue': [value: string | number | null];
}>();

const className = computed(() => props.class || '');

const computedSizeClass = computed(() => {
    if (props.size === 'small') return 'h-8';
    if (props.size === 'large') return 'h-10';
    return '';
});
</script>
