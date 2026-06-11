<script setup lang="ts">
import type { TagsInputRootEmits, TagsInputRootProps } from "reka-ui";
import type { HTMLAttributes } from "vue";
import { provide, toRef } from "vue";
import { reactiveOmit } from "@vueuse/core";
import { TagsInputRoot, useForwardPropsEmits } from "reka-ui";
import { cn } from '@/utils';
import type { TagsInputVariant } from './types';

const props = defineProps<TagsInputRootProps & {
    class?: HTMLAttributes["class"];
    invalid?: boolean;
    variant?: TagsInputVariant;
}>();
const emits = defineEmits<TagsInputRootEmits>();

const delegatedProps = reactiveOmit(props, "class", "invalid", "variant");

const forwarded = useForwardPropsEmits(delegatedProps, emits);

// Provide variant to child TagsInputItem components
provide('tagsInputVariant', toRef(() => props.variant ?? 'default'));

const handleClick = (event: MouseEvent) => {
    const container = event.currentTarget as HTMLElement;
    const target = event.target as HTMLElement;
    // Only skip if clicking the delete button
    if (target.closest('button')) {
        return;
    }
    // Focus the input inside
    const input = container.querySelector('input');
    input?.focus();
};
</script>

<template>
    <TagsInputRoot
        v-bind="forwarded"
        :class="cn(
            'flex min-h-9 w-full flex-wrap gap-1 items-center rounded-md border border-input bg-background px-3 py-1 text-sm transition-all cursor-text overflow-hidden',
            'hover:border-foreground/50',
            'focus-within:outline-none focus-within:border-foreground/50',
            'has-[:disabled]:cursor-not-allowed has-[:disabled]:opacity-50 has-[:disabled]:hover:border-input',
            props.invalid && 'border-destructive focus-within:border-destructive',
            props.class,
        )"
        @click="handleClick"
    >
        <slot />
    </TagsInputRoot>
</template>
