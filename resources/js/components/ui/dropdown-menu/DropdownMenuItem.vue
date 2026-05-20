<script setup lang="ts">
import type { DropdownMenuItemEmits, DropdownMenuItemProps } from "reka-ui";
import type { HTMLAttributes } from "vue";
import { reactiveOmit } from "@vueuse/core";
import { DropdownMenuItem, useForwardPropsEmits } from "reka-ui";
import { cn } from "@/utils";

const props = defineProps<DropdownMenuItemProps & { class?: HTMLAttributes["class"], inset?: boolean }>();
const emits = defineEmits<DropdownMenuItemEmits>();

const delegatedProps = reactiveOmit(props, "class");

const forwarded = useForwardPropsEmits(delegatedProps, emits);
</script>

<template>
    <DropdownMenuItem
        v-bind="forwarded"
        :class="cn(
            'relative flex cursor-pointer select-none items-center rounded-sm gap-2 px-2 py-1.5 text-sm outline-none transition-colors focus:bg-accent focus:text-accent-foreground data-[disabled]:pointer-events-none data-[disabled]:opacity-50 data-[disabled]:cursor-not-allowed [&>svg]:size-4 [&>svg]:shrink-0',
            inset && 'pl-8',
            props.class,
        )"
    >
        <slot />
    </DropdownMenuItem>
</template>
