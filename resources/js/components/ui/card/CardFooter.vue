<script setup lang="ts">
import type { HTMLAttributes } from "vue";
import { cn } from "@/utils";

const props = defineProps<{
    class?: HTMLAttributes["class"]
}>();
</script>

<template>
    <div :class="cn('flex items-center px-4 pb-4', props.class)">
        <slot />
    </div>
</template>
