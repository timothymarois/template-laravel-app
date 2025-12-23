<script setup lang="ts">
import type { ContextMenuTriggerProps } from "reka-ui";
import type { HTMLAttributes } from "vue";
import { reactiveOmit } from "@vueuse/core";
import { ContextMenuTrigger, useForwardProps } from "reka-ui";
import { cn } from "@/utils";

const props = defineProps<ContextMenuTriggerProps & { class?: HTMLAttributes["class"] }>();

const delegatedProps = reactiveOmit(props, "class");

const forwardedProps = useForwardProps(delegatedProps);
</script>

<template>
    <ContextMenuTrigger
        v-bind="forwardedProps"
        :class="cn(props.class)"
    >
        <slot />
    </ContextMenuTrigger>
</template>
