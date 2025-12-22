<template>
    <AvatarBase :class="[computedSizeClass, shape === 'circle' ? 'rounded-full' : 'rounded-md', className]">
        <AvatarImage v-if="image" :src="image" :alt="label" />
        <AvatarFallback>
            <slot>{{ initials }}</slot>
        </AvatarFallback>
    </AvatarBase>
</template>

<script setup lang="ts">
import { computed } from 'vue';
import AvatarBase from './AvatarBase.vue';
import AvatarFallback from './AvatarFallback.vue';
import AvatarImage from './AvatarImage.vue';

interface Props {
    image?: string;
    label?: string;
    shape?: 'circle' | 'square';
    size?: 'normal' | 'large' | 'xlarge';
    class?: string;
}

const props = withDefaults(defineProps<Props>(), {
    shape: 'square',
    size: 'normal',
});

const className = computed(() => props.class || '');

const initials = computed(() => {
    if (!props.label) return '';
    return props.label
        .split(' ')
        .map(word => word[0])
        .join('')
        .toUpperCase()
        .slice(0, 2);
});

const computedSizeClass = computed(() => {
    switch (props.size) {
        case 'large': return 'w-12 h-12 text-2xl';
        case 'xlarge': return 'w-16 h-16 text-[2rem]';
        default: return 'w-8 h-8 text-base';
    }
});
</script>
