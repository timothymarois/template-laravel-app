<template>
    <div :class="cn('h-14 bg-background border-b border-border flex items-center w-full', className)">
        <slot />
    </div>
</template>

<script setup lang="ts">
import { computed } from 'vue';
import { cn } from '@/utils';

interface Props {
    class?: string;
}

const props = defineProps<Props>();

const className = computed(() => props.class || '');
</script>
