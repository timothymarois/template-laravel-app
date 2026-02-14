<template>
    <ScrollFrame
        v-bind="{
            page: true,
            offset: offset,
            addOffset: footerHeight,
            scrollable: scrollable,
            ...attrs
        }"
    >
        <div :class="containerClassInternal">
            <slot />
        </div>
    </ScrollFrame>
</template>

<script setup>
import { computed, useAttrs } from 'vue';
import { ScrollFrame } from '@/components/ui';

const props = defineProps({
    offset: {
        type: Number,
        default: null,
    },
    footerHeight: {
        type: Number,
        default: 0,
    },
    widthClass: {
        type: String,
        default: 'max-w-screen-2xl',
    },
    containerClass: {
        type: String,
        default: 'mx-auto p-4',
    },
    scrollable: {
        type: Boolean,
        default: true,
    },
});

const attrs = useAttrs();

const containerClassInternal = computed(() => `${props.widthClass} ${props.containerClass}`);
</script>
