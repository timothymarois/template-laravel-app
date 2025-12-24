<template>
    <BadgeBase :variant="computedVariant" :class="[computedSizeClass, props.class]">
        <slot>{{ value }}</slot>
    </BadgeBase>
</template>

<script setup lang="ts">
import { computed } from 'vue';
import BadgeBase from './BadgeBase.vue';

interface Props {
    value?: string | number;
    severity?: 'primary' | 'secondary' | 'success' | 'info' | 'warn' | 'danger' | 'contrast';
    size?: 'small' | 'large' | 'xlarge';
    class?: string;
}

const props = withDefaults(defineProps<Props>(), {
    severity: 'primary',
});

const computedVariant = computed(() => {
    switch (props.severity) {
    case 'secondary': return 'secondary';
    case 'danger': return 'destructive';
    case 'success': return 'default'; // Would need custom variant
    case 'info': return 'secondary';
    case 'warn': return 'outline';
    default: return 'default';
    }
});

const computedSizeClass = computed(() => {
    if (props.size === 'small') return 'text-[0.625rem] min-w-5 h-5';
    if (props.size === 'large') return 'text-sm min-w-7 h-7';
    if (props.size === 'xlarge') return 'text-base min-w-8 h-8';
    return '';
});
</script>
