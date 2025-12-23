<script setup lang="ts">
import { computed } from 'vue';
import { cn } from '@/utils';

interface Props {
    modelValue?: number;
    max?: number;
    size?: 'sm' | 'default' | 'lg';
    variant?: 'default' | 'primary' | 'success' | 'warning' | 'destructive';
    indeterminate?: boolean;
    showValue?: boolean;
    class?: string;
}

const props = withDefaults(defineProps<Props>(), {
    modelValue: 0,
    max: 100,
    size: 'default',
    variant: 'default',
    indeterminate: false,
    showValue: false,
});

const percentage = computed(() => {
    return Math.min(100, Math.max(0, (props.modelValue / props.max) * 100));
});

const sizeClasses = computed(() => {
    switch (props.size) {
        case 'sm': return 'h-1';
        case 'lg': return 'h-3';
        default: return 'h-2';
    }
});

const variantClasses = computed(() => {
    switch (props.variant) {
        case 'primary': return 'bg-primary';
        case 'success': return 'bg-green-500';
        case 'warning': return 'bg-yellow-500';
        case 'destructive': return 'bg-destructive';
        default: return 'bg-primary';
    }
});
</script>

<template>
    <div class="w-full">
        <div
            v-if="showValue"
            class="flex justify-between text-sm mb-1"
        >
            <slot name="label" />
            <span class="text-muted-foreground">{{ Math.round(percentage) }}%</span>
        </div>
        <div
            role="progressbar"
            :aria-valuenow="modelValue"
            :aria-valuemin="0"
            :aria-valuemax="max"
            :class="cn(
                'relative w-full overflow-hidden rounded-full bg-primary/20',
                sizeClasses,
                props.class
            )"
        >
            <div
                :class="cn(
                    'h-full transition-all duration-300 ease-in-out rounded-full',
                    variantClasses,
                    indeterminate && 'animate-progress-indeterminate'
                )"
                :style="indeterminate ? {} : { width: `${percentage}%` }"
            />
        </div>
    </div>
</template>

<style>
@keyframes progress-indeterminate {
    0% {
        left: -30%;
        width: 30%;
    }
    50% {
        left: 100%;
        width: 30%;
    }
    100% {
        left: -30%;
        width: 30%;
    }
}

.animate-progress-indeterminate {
    position: absolute;
    animation: progress-indeterminate 1.5s ease-in-out infinite;
}
</style>
