<script setup lang="ts">
import { inject, computed, ref, watch, nextTick, onBeforeUnmount } from 'vue';
import { cn } from '@/utils';

interface AccordionContext {
    toggle: (value: string) => void;
    isOpen: (value: string) => boolean;
}

interface AccordionItemContext {
    value: string;
    disabled?: boolean;
}

interface Props {
    class?: string;
}

const props = defineProps<Props>();

const accordionContext = inject<AccordionContext>('accordionContext')!;
const itemContext = inject<AccordionItemContext>('accordionItemContext')!;

const isOpen = computed(() => accordionContext.isOpen(itemContext.value));

const contentRef = ref<HTMLElement | null>(null);
const height = ref<string>('0px');
const isAnimating = ref(false);
let animationTimeout: ReturnType<typeof setTimeout> | null = null;

watch(
    isOpen,
    async (open) => {
        // Clear any pending animation timeout
        if (animationTimeout) {
            clearTimeout(animationTimeout);
        }

        isAnimating.value = true;

        if (open) {
            await nextTick();
            if (contentRef.value) {
                height.value = `${contentRef.value.scrollHeight}px`;
            }
        } else {
            height.value = '0px';
        }

        animationTimeout = setTimeout(() => {
            isAnimating.value = false;
            if (open) {
                height.value = 'auto';
            }
            animationTimeout = null;
        }, 200);
    },
    { immediate: true }
);

onBeforeUnmount(() => {
    if (animationTimeout) {
        clearTimeout(animationTimeout);
        animationTimeout = null;
    }
});
</script>

<template>
    <div
        ref="contentRef"
        :data-state="isOpen ? 'open' : 'closed'"
        :class="
            cn(
                'overflow-hidden text-sm transition-all duration-200 ease-in-out',
                props.class
            )
        "
        :style="{ height: isOpen || isAnimating ? height : '0px' }"
    >
        <div class="px-4 pb-4 pt-0">
            <slot />
        </div>
    </div>
</template>
