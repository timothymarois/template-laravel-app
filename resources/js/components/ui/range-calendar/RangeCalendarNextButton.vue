<script lang="ts" setup>
import type { RangeCalendarNextProps } from "reka-ui";
import type { HTMLAttributes } from "vue";
import { reactiveOmit } from "@vueuse/core";
import { ChevronRight } from "lucide-vue-next";
import { RangeCalendarNext } from "reka-ui";
import { buttonVariants } from "@/components/ui/button";
import { cn } from "@/utils";

const props = defineProps<RangeCalendarNextProps & { class?: HTMLAttributes["class"] }>();

const delegatedProps = reactiveOmit(props, "class");
</script>

<template>
    <RangeCalendarNext :class="cn(buttonVariants({ variant: 'outline' }), 'h-7 w-7 bg-transparent p-0 opacity-50 hover:opacity-100', props.class)" v-bind="delegatedProps">
        <slot>
            <ChevronRight class="h-4 w-4" />
        </slot>
    </RangeCalendarNext>
</template>
