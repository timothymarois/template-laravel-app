<script setup lang="ts">
import type { HTMLAttributes } from "vue";
import { cn } from "@/utils";

const props = defineProps<{
  class?: HTMLAttributes["class"]
}>();
</script>

<template>
  <th :class="cn('h-12 px-4 text-left align-middle font-semibold text-foreground bg-muted border-r border-border last:border-r-0 transition-colors hover:bg-muted/80 [&:has([role=checkbox])]:pr-0', props.class)">
    <slot />
  </th>
</template>
