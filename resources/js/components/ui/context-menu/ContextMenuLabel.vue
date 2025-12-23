<script setup lang="ts">
import type { ContextMenuLabelProps } from "reka-ui";
import type { HTMLAttributes } from "vue";
import { reactiveOmit } from "@vueuse/core";
import { ContextMenuLabel, useForwardProps } from "reka-ui";
import { cn } from "@/utils";

const props = defineProps<ContextMenuLabelProps & { class?: HTMLAttributes["class"], inset?: boolean }>();

const delegatedProps = reactiveOmit(props, "class");

const forwardedProps = useForwardProps(delegatedProps);
</script>

<template>
    <ContextMenuLabel
        v-bind="forwardedProps"
        :class="cn('px-2 py-1.5 text-sm font-semibold', inset && 'pl-8', props.class)"
    >
        <slot />
    </ContextMenuLabel>
</template>
