<script setup lang="ts">
import type { HTMLAttributes } from 'vue';
import { useVModel } from '@vueuse/core';
import { cn } from '@/utils';

const props = defineProps<{
    defaultValue?: string;
    modelValue?: string;
    class?: HTMLAttributes['class'];
}>();

const emits = defineEmits<{
    (e: 'update:modelValue', payload: string): void;
}>();

const modelValue = useVModel(props, 'modelValue', emits, {
    passive: true,
    defaultValue: props.defaultValue,
});
</script>

<template>
    <textarea
        v-model="modelValue"
        :class="
            cn(
                'flex min-h-[80px] w-full rounded-md border border-input bg-background px-3 py-2 text-sm transition-all placeholder:text-muted-foreground hover:border-foreground/50 focus-visible:outline-none focus-visible:border-foreground/50 disabled:cursor-not-allowed disabled:opacity-50 disabled:hover:border-input resize-y',
                props.class
            )
        "
    />
</template>
