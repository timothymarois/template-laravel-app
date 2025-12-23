<script setup lang="ts">
import type { ContextMenuItemProps } from "reka-ui";
import type { HTMLAttributes } from "vue";
import { reactiveOmit } from "@vueuse/core";
import { ContextMenuItem, useForwardProps } from "reka-ui";
import { cn } from "@/utils";

const props = defineProps<ContextMenuItemProps & { class?: HTMLAttributes["class"], inset?: boolean }>();

const delegatedProps = reactiveOmit(props, "class");

const forwardedProps = useForwardProps(delegatedProps);
</script>

<template>
    <ContextMenuItem
        v-bind="forwardedProps"
        :class="cn(
            'relative flex cursor-pointer select-none items-center rounded-sm gap-2 px-2 py-1.5 text-sm outline-none transition-colors focus:bg-accent focus:text-accent-foreground data-[disabled]:pointer-events-none data-[disabled]:opacity-50 data-[disabled]:cursor-not-allowed [&>svg]:size-4 [&>svg]:shrink-0',
            inset && 'pl-8',
            props.class,
        )"
    >
        <slot />
    </ContextMenuItem>
</template>
