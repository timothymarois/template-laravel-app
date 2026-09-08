<script setup lang="ts">
import type { ComputedRef, HTMLAttributes } from "vue";
import { inject, computed } from "vue";
import { cn } from "@/utils";

const props = defineProps<{
    class?: HTMLAttributes["class"];
}>();

const isDraggable = inject<ComputedRef<boolean>>('dialogDraggable', computed(() => false));
</script>

<template>
    <div
        data-dialog-header
        :class="cn(
            'flex flex-col gap-y-1.5 text-center sm:text-left',
            isDraggable && 'cursor-move select-none',
            props.class
        )"
    >
        <slot />
    </div>
</template>
