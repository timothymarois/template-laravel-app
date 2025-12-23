<script lang="ts" setup>
import type { RangeCalendarGridRowProps } from "reka-ui";
import type { HTMLAttributes } from "vue";
import { reactiveOmit } from "@vueuse/core";
import { RangeCalendarGridRow } from "reka-ui";
import { cn } from "@/utils";

const props = defineProps<RangeCalendarGridRowProps & { class?: HTMLAttributes["class"] }>();

const delegatedProps = reactiveOmit(props, "class");
</script>

<template>
    <RangeCalendarGridRow :class="cn('flex', props.class)" v-bind="delegatedProps">
        <slot />
    </RangeCalendarGridRow>
</template>
