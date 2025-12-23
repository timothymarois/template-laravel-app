<script lang="ts" setup>
import type { RangeCalendarHeaderProps } from "reka-ui";
import type { HTMLAttributes } from "vue";
import { reactiveOmit } from "@vueuse/core";
import { RangeCalendarHeader } from "reka-ui";
import { cn } from "@/utils";

const props = defineProps<RangeCalendarHeaderProps & { class?: HTMLAttributes["class"] }>();

const delegatedProps = reactiveOmit(props, "class");
</script>

<template>
    <RangeCalendarHeader :class="cn('relative flex w-full items-center justify-between pt-1', props.class)" v-bind="delegatedProps">
        <slot />
    </RangeCalendarHeader>
</template>
