<script setup lang="ts">
import type { HTMLAttributes } from "vue";
import { cn } from "@/utils";

const props = defineProps<{
    class?: HTMLAttributes["class"]
}>();
</script>

<template>
    <tbody :class="cn('', props.class)">
        <slot />
    </tbody>
</template>
