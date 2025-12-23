<script lang="ts" setup>
import type { RangeCalendarHeadingProps } from "reka-ui";
import type { HTMLAttributes } from "vue";
import { reactiveOmit } from "@vueuse/core";
import { RangeCalendarHeading } from "reka-ui";
import { cn } from "@/utils";

const props = defineProps<RangeCalendarHeadingProps & { class?: HTMLAttributes["class"] }>();

const delegatedProps = reactiveOmit(props, "class");
</script>

<template>
    <RangeCalendarHeading
        v-slot="{ headingValue }"
        :class="cn('text-sm font-medium', props.class)"
        v-bind="delegatedProps"
    >
        <slot :heading-value="headingValue">
            {{ headingValue }}
        </slot>
    </RangeCalendarHeading>
</template>
