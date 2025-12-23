<script setup lang="ts">
import { inject, computed, ref, watch, nextTick } from 'vue';
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

watch(
    isOpen,
    async (open) => {
        isAnimating.value = true;

        if (open) {
            await nextTick();
            if (contentRef.value) {
                height.value = `${contentRef.value.scrollHeight}px`;
            }
        } else {
            height.value = '0px';
        }

        setTimeout(() => {
            isAnimating.value = false;
            if (open) {
                height.value = 'auto';
            }
        }, 200);
    },
    { immediate: true }
);
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
