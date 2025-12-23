<script setup lang="ts">
import type { RadioGroupItemProps } from "reka-ui";
import type { HTMLAttributes } from "vue";
import { reactiveOmit } from "@vueuse/core";
import { Circle } from "lucide-vue-next";
import {
    RadioGroupIndicator,
    RadioGroupItem,
    useForwardProps,
} from "reka-ui";
import { cn } from "@/utils";

interface Props extends RadioGroupItemProps {
    class?: HTMLAttributes["class"];
    invalid?: boolean;
}

const props = defineProps<Props>();

const delegatedProps = reactiveOmit(props, "class", "invalid");

const forwardedProps = useForwardProps(delegatedProps);
</script>

<template>
    <RadioGroupItem
        v-bind="forwardedProps"
        :class="
            cn(
                'group peer relative aspect-square h-4 w-4 rounded-full border border-input bg-background text-primary cursor-pointer transition-all hover:border-foreground/50 focus:outline-none focus-visible:border-foreground/50 disabled:cursor-not-allowed disabled:opacity-50 disabled:hover:border-input data-[state=checked]:border-primary data-[state=checked]:text-primary data-[state=checked]:hover:text-primary/70 data-[state=checked]:disabled:hover:text-primary',
                props.invalid && 'border-destructive data-[state=unchecked]:border-destructive',
                props.class,
            )
        "
    >
        <span class="absolute inset-0 flex items-center justify-center opacity-0 transition-opacity group-hover:opacity-30 group-data-[state=checked]:opacity-0 group-disabled:!opacity-0">
            <Circle class="h-2.5 w-2.5 fill-current text-current" />
        </span>
        <RadioGroupIndicator class="absolute inset-0 flex items-center justify-center">
            <Circle class="h-2.5 w-2.5 fill-current text-current" />
        </RadioGroupIndicator>
    </RadioGroupItem>
</template>
