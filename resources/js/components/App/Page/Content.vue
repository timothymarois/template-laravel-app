<template>
    <ScrollFrame
        v-bind="{
            page: true,
            offset: offset,
            addOffset: footerHeight,
            ...attrs
        }"
    >
        <div :class="containerClassInternal">
            <slot />
        </div>
    </ScrollFrame>
</template>

<script setup lang="ts">
import { computed, useAttrs } from 'vue';
import ScrollFrame from '../../ScrollFrame.vue';

interface Props {
    offset?: number | null;
    footerHeight?: number;
    widthClass?: string;
    containerClass?: string;
}

const props = withDefaults(defineProps<Props>(), {
    offset: null,
    footerHeight: 0,
    widthClass: 'max-w-screen-2xl',
    containerClass: 'mx-auto p-4',
});

const attrs = useAttrs();

const containerClassInternal = computed(() => `${props.widthClass} ${props.containerClass}`);
</script>
