<script setup lang="ts">
import { inject, computed } from 'vue';
import { cn } from '@/utils';
import { ChevronDown } from 'lucide-vue-next';

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

const handleClick = () => {
    if (!itemContext.disabled) {
        accordionContext.toggle(itemContext.value);
    }
};
</script>

<template>
    <h3 class="flex">
        <button
            type="button"
            :aria-expanded="isOpen"
            :disabled="itemContext.disabled"
            :class="
                cn(
                    'flex flex-1 items-center justify-between py-4 px-4 text-sm font-medium transition-all hover:bg-muted/50 text-left [&[data-state=open]>svg]:rotate-180 cursor-pointer',
                    'focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-inset',
                    'disabled:pointer-events-none disabled:opacity-50 disabled:cursor-not-allowed',
                    props.class
                )
            "
            :data-state="isOpen ? 'open' : 'closed'"
            @click="handleClick"
        >
            <slot />
            <ChevronDown
                class="size-4 shrink-0 text-muted-foreground transition-transform duration-200"
                :class="{ 'rotate-180': isOpen }"
            />
        </button>
    </h3>
</template>
