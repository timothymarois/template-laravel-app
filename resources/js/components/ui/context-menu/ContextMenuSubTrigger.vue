<script setup lang="ts">
import type { ContextMenuSubTriggerProps } from "reka-ui";
import type { HTMLAttributes } from "vue";
import { reactiveOmit } from "@vueuse/core";
import { ChevronRight } from "lucide-vue-next";
import {
    ContextMenuSubTrigger,
    useForwardProps,
} from "reka-ui";
import { cn } from "@/utils";

const props = defineProps<ContextMenuSubTriggerProps & { class?: HTMLAttributes["class"], inset?: boolean }>();

const delegatedProps = reactiveOmit(props, "class");

const forwardedProps = useForwardProps(delegatedProps);
</script>

<template>
    <ContextMenuSubTrigger
        v-bind="forwardedProps"
        :class="cn(
            'flex cursor-pointer select-none items-center rounded-sm gap-2 px-2 py-1.5 text-sm outline-none focus:bg-accent data-[state=open]:bg-accent [&>svg:first-child]:size-4 [&>svg:first-child]:shrink-0',
            inset && 'pl-8',
            props.class,
        )"
    >
        <slot />
        <ChevronRight class="ml-auto h-4 w-4" />
    </ContextMenuSubTrigger>
</template>
