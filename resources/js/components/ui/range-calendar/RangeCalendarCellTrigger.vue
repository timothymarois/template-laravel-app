<script lang="ts" setup>
import type { RangeCalendarCellTriggerProps } from "reka-ui";
import type { HTMLAttributes } from "vue";
import { reactiveOmit } from "@vueuse/core";
import { RangeCalendarCellTrigger } from "reka-ui";
import { buttonVariants } from "@/components/ui/button";
import { cn } from "@/utils";

const props = defineProps<RangeCalendarCellTriggerProps & { class?: HTMLAttributes["class"] }>();

const delegatedProps = reactiveOmit(props, "class");
</script>

<template>
    <RangeCalendarCellTrigger
        :class="cn(
            buttonVariants({ variant: 'ghost' }),
            'h-9 w-9 p-0 font-normal',
            // Today
            '[&[data-today]:not([data-selected])]:bg-accent [&[data-today]:not([data-selected])]:text-accent-foreground',
            // Selected in-between (not start or end) - just darker text, no bg on trigger
            'data-[selected]:bg-transparent data-[selected]:text-foreground data-[selected]:opacity-100',
            // Selection start - primary with left rounding
            'data-[selection-start]:bg-primary data-[selection-start]:text-primary-foreground data-[selection-start]:rounded-l-md data-[selection-start]:rounded-r-none',
            'data-[selection-start]:hover:bg-primary data-[selection-start]:hover:text-primary-foreground',
            'data-[selection-start]:focus:bg-primary data-[selection-start]:focus:text-primary-foreground',
            // Selection end - primary with right rounding
            'data-[selection-end]:bg-primary data-[selection-end]:text-primary-foreground data-[selection-end]:rounded-r-md data-[selection-end]:rounded-l-none',
            'data-[selection-end]:hover:bg-primary data-[selection-end]:hover:text-primary-foreground',
            'data-[selection-end]:focus:bg-primary data-[selection-end]:focus:text-primary-foreground',
            // When start and end are same date - full rounding
            '[&[data-selection-start][data-selection-end]]:rounded-md',
            // Outside view
            '[&[data-outside-view]]:text-muted-foreground [&[data-outside-view]]:opacity-50',
            '[&[data-outside-view][data-selected]]:bg-transparent [&[data-outside-view][data-selected]]:text-muted-foreground [&[data-outside-view][data-selected]]:opacity-30',
            // Disabled
            'data-[disabled]:text-muted-foreground data-[disabled]:opacity-50',
            // Unavailable
            'data-[unavailable]:text-destructive-foreground data-[unavailable]:line-through',
            props.class,
        )"
        v-bind="delegatedProps"
    >
        <slot />
    </RangeCalendarCellTrigger>
</template>
