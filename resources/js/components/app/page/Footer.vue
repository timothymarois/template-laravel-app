<template>
    <div
        class="fixed bottom-0 z-10 border-t border-border bg-background transition-shadow"
        :class="showShadow ? 'shadow-[0_-2px_4px_1px_rgba(0,0,0,0.05)] dark:shadow-[0_-2px_6px_rgba(0,0,0,0.3)]' : ''"
        :style="{
            left: `${leftOffset}px`,
            width: `calc(100% - ${leftOffset}px)`
        }"
    >
        <div class="mx-auto px-4 py-1.5 flex justify-between items-center" :class="widthClass">
            <div class="grow">
                <slot />
            </div>
            <div v-if="hasAction">
                <slot name="action" />
            </div>
        </div>
    </div>
</template>

<script setup>
import { useSlots, computed, inject } from 'vue';
import { hasSlotContent } from '@/utils';

const props = defineProps({
    leftOffset: {
        type: Number,
        default: 0,
    },
    widthClass: {
        type: String,
        default: 'max-w-screen-2xl',
    },
});

const slots = useSlots();

const hasAction = computed(() => hasSlotContent(slots.action));

// Inject scroll-to-bottom state from table (if available)
const isScrolledToBottom = inject('isScrolledToBottom', null);

const showShadow = computed(() => {
    // Show shadow if we don't know scroll state or if not scrolled to bottom
    return isScrolledToBottom?.value === false;
});
</script>
