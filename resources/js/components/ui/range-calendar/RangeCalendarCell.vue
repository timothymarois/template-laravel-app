<script lang="ts" setup>
import type { RangeCalendarCellProps } from "reka-ui";
import type { HTMLAttributes } from "vue";
import { reactiveOmit } from "@vueuse/core";
import { RangeCalendarCell } from "reka-ui";
import { cn } from "@/utils";

const props = defineProps<RangeCalendarCellProps & { class?: HTMLAttributes["class"] }>();

const delegatedProps = reactiveOmit(props, "class");
</script>

<template>
    <RangeCalendarCell
        :class="cn(
            'relative p-0 text-center text-sm focus-within:relative focus-within:z-20',
            // Background for selected range (in-between dates)
            '[&:has([data-selected])]:bg-primary/15',
            '[&:has([data-selected][data-outside-view])]:bg-primary/5',
            // Remove rounding for middle dates, add for edges
            '[&:has([data-selected])]:rounded-none',
            '[&:has([data-selection-start])]:rounded-l-md',
            '[&:has([data-selection-end])]:rounded-r-md',
            // First/last in row rounding
            'first:[&:has([data-selected])]:rounded-l-md',
            'last:[&:has([data-selected])]:rounded-r-md',
            props.class
        )"
        v-bind="delegatedProps"
    >
        <slot />
    </RangeCalendarCell>
</template>
