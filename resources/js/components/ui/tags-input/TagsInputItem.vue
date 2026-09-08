<script setup lang="ts">
import type { TagsInputItemProps } from "reka-ui";
import type { HTMLAttributes, Ref } from "vue";
import { inject, computed } from "vue";
import { reactiveOmit } from "@vueuse/core";
import { TagsInputItem, useForwardProps } from "reka-ui";
import { cn } from '@/utils';
import type { TagsInputVariant } from "./types";

const props = defineProps<TagsInputItemProps & { class?: HTMLAttributes["class"] }>();

const delegatedProps = reactiveOmit(props, "class");

const forwardedProps = useForwardProps(delegatedProps);

const variant = inject<Ref<TagsInputVariant>>('tagsInputVariant');

const variantClasses = computed(() => {
    const v = variant?.value ?? 'default';
    const variants: Record<TagsInputVariant, string> = {
        default: 'rounded bg-secondary px-2 py-0.5',
        secondary: 'rounded-full bg-secondary px-2.5 py-0.5',
        outline: 'rounded border px-2 py-0.5',
        primary: 'rounded bg-primary text-primary-foreground px-2 py-0.5',
    };
    return variants[v];
});
</script>

<template>
    <TagsInputItem
        v-bind="forwardedProps"
        :class="cn(
            'inline-flex items-center gap-1 text-xs font-medium max-w-full min-w-0',
            variantClasses,
            props.class,
        )"
    >
        <slot />
    </TagsInputItem>
</template>
