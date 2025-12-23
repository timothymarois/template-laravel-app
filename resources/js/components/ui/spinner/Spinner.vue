<script setup lang="ts">
import { computed } from 'vue';
import { cn } from '@/utils';
import { Loader2 } from 'lucide-vue-next';

interface Props {
    size?: 'xs' | 'sm' | 'default' | 'lg' | 'xl';
    variant?: 'default' | 'primary' | 'secondary' | 'muted';
    class?: string;
}

const props = withDefaults(defineProps<Props>(), {
    size: 'default',
    variant: 'default',
});

const sizeClasses = computed(() => {
    switch (props.size) {
        case 'xs': return 'size-3';
        case 'sm': return 'size-4';
        case 'default': return 'size-6';
        case 'lg': return 'size-8';
        case 'xl': return 'size-12';
    }
});

const variantClasses = computed(() => {
    switch (props.variant) {
        case 'primary': return 'text-primary';
        case 'secondary': return 'text-secondary-foreground';
        case 'muted': return 'text-muted-foreground';
        default: return 'text-foreground';
    }
});
</script>

<template>
    <Loader2
        :class="cn('animate-spin', sizeClasses, variantClasses, props.class)"
    />
</template>
