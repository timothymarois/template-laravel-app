<script setup lang="ts">
import type { DialogContentEmits, DialogContentProps } from "reka-ui";
import type { HTMLAttributes } from "vue";
import type { SheetVariants } from ".";
import { computed } from "vue";
import { reactiveOmit } from "@vueuse/core";
import { X } from "lucide-vue-next";
import {
    DialogClose,
    DialogContent,
    DialogOverlay,
    DialogPortal,
    useForwardPropsEmits,
} from "reka-ui";
import { cn } from "@/utils";
import { sheetVariants } from ".";

interface SheetContentProps extends DialogContentProps {
    class?: HTMLAttributes["class"]
    side?: SheetVariants["side"]
}

defineOptions({
    inheritAttrs: false,
});

const props = defineProps<SheetContentProps>();

const emits = defineEmits<DialogContentEmits>();

const delegatedProps = reactiveOmit(props, "class", "side");

const forwarded = useForwardPropsEmits(delegatedProps, emits);

const sheetAnimationClass = computed(() => {
    const sideValue = props.side || "right";
    return `sheet-content-${sideValue}`;
});
</script>

<template>
    <DialogPortal>
        <DialogOverlay
            class="sheet-overlay fixed inset-0 z-50 bg-black/50"
        />
        <DialogContent
            :class="cn(sheetVariants({ side }), sheetAnimationClass, props.class)"
            v-bind="{ ...forwarded, ...$attrs }"
        >
            <slot />

            <DialogClose
                class="absolute right-4 top-4 size-8 rounded-md flex items-center justify-center bg-muted/50 hover:bg-muted transition-colors cursor-pointer focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2 disabled:pointer-events-none"
            >
                <X class="size-4" />
                <span class="sr-only">Close</span>
            </DialogClose>
        </DialogContent>
    </DialogPortal>
</template>
