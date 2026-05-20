<script setup lang="ts">
import { computed, inject, provide } from 'vue';
import { cn } from '@/utils';

interface AccordionContext {
    toggle: (value: string) => void;
    isOpen: (value: string) => boolean;
}

interface Props {
    value: string;
    disabled?: boolean;
    class?: string;
}

const props = defineProps<Props>();

const accordionContext = inject<AccordionContext>('accordionContext')!;

const isOpen = computed(() => accordionContext.isOpen(props.value));

provide('accordionItemContext', {
    value: props.value,
    disabled: props.disabled,
});
</script>

<template>
    <div
        :class="cn(props.class)"
        :data-state="disabled ? 'disabled' : isOpen ? 'open' : 'closed'"
        :data-disabled="disabled ? '' : undefined"
    >
        <slot />
    </div>
</template>
