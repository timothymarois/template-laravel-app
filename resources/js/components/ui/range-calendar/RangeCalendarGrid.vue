<script lang="ts" setup>
import type { RangeCalendarGridProps } from "reka-ui";
import type { HTMLAttributes } from "vue";
import { reactiveOmit } from "@vueuse/core";
import { RangeCalendarGrid } from "reka-ui";
import { cn } from "@/utils";

const props = defineProps<RangeCalendarGridProps & { class?: HTMLAttributes["class"] }>();

const delegatedProps = reactiveOmit(props, "class");
</script>

<template>
    <RangeCalendarGrid :class="cn('w-full border-collapse space-y-1', props.class)" v-bind="delegatedProps">
        <slot />
    </RangeCalendarGrid>
</template>
