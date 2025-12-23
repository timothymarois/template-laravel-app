<script setup lang="ts">
import type { SelectTriggerProps } from "reka-ui";
import type { HTMLAttributes } from "vue";
import { reactiveOmit } from "@vueuse/core";
import { ChevronDown } from "lucide-vue-next";
import { SelectIcon, SelectTrigger, useForwardProps } from "reka-ui";
import { cn } from "@/utils";

const props = defineProps<SelectTriggerProps & { class?: HTMLAttributes["class"] }>();

const delegatedProps = reactiveOmit(props, "class");

const forwardedProps = useForwardProps(delegatedProps);
</script>

<template>
    <SelectTrigger
        v-bind="forwardedProps"
        :class="cn(
            'inline-flex h-9 items-center justify-between gap-2 rounded-md border border-ring bg-background px-3 py-1 text-sm transition-all cursor-pointer data-[placeholder]:text-muted-foreground hover:border-foreground/50 focus:outline-none focus:border-foreground/50 disabled:cursor-not-allowed disabled:opacity-50 disabled:hover:border-ring [&>span]:truncate text-start',
            props.class,
        )"
    >
        <slot />
        <SelectIcon as-child>
            <ChevronDown class="w-4 h-4 opacity-50 shrink-0" />
        </SelectIcon>
    </SelectTrigger>
</template>
