<script lang="ts" setup>
import type { RangeCalendarPrevProps } from "reka-ui";
import type { HTMLAttributes } from "vue";
import { reactiveOmit } from "@vueuse/core";
import { ChevronLeft } from "lucide-vue-next";
import { RangeCalendarPrev } from "reka-ui";
import { buttonVariants } from "@/components/ui/button";
import { cn } from "@/utils";

const props = defineProps<RangeCalendarPrevProps & { class?: HTMLAttributes["class"] }>();

const delegatedProps = reactiveOmit(props, "class");
</script>

<template>
    <RangeCalendarPrev :class="cn(buttonVariants({ variant: 'outline' }), 'h-7 w-7 bg-transparent p-0 opacity-50 hover:opacity-100', props.class)" v-bind="delegatedProps">
        <slot>
            <ChevronLeft class="h-4 w-4" />
        </slot>
    </RangeCalendarPrev>
</template>
