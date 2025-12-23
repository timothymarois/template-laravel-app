<script lang="ts" setup>
import type { RangeCalendarHeadCellProps } from "reka-ui";
import type { HTMLAttributes } from "vue";
import { reactiveOmit } from "@vueuse/core";
import { RangeCalendarHeadCell } from "reka-ui";
import { cn } from "@/utils";

const props = defineProps<RangeCalendarHeadCellProps & { class?: HTMLAttributes["class"] }>();

const delegatedProps = reactiveOmit(props, "class");
</script>

<template>
    <RangeCalendarHeadCell :class="cn('w-9 rounded-md text-[0.8rem] font-normal text-muted-foreground', props.class)" v-bind="delegatedProps">
        <slot />
    </RangeCalendarHeadCell>
</template>
