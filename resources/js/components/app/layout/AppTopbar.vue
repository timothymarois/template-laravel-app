<template>
    <div :class="cn('h-14 bg-background border-b border-border flex items-center w-full', className)">
        <slot />
    </div>
</template>

<script setup>
import { computed } from 'vue';
import { cn } from '@/utils';

const props = defineProps({
    class: {
        type: String,
        default: '',
    },
});

const className = computed(() => props.class || '');
</script>
