<script setup lang="ts">
import type { HTMLAttributes } from "vue";
import { cn } from "@/utils";

const props = defineProps<{
    class?: HTMLAttributes["class"]
}>();
</script>

<template>
    <div :class="cn('grid auto-cols-[1fr_auto] grid-rows-[auto_auto] gap-x-4 gap-y-1.5 p-4 has-data-[slot=card-action]:grid-cols-[1fr_auto] [&:not(:has([data-slot=card-action]))]:flex [&:not(:has([data-slot=card-action]))]:flex-col', props.class)">
        <slot />
    </div>
</template>
