<script setup lang="ts">
import type { HTMLAttributes } from "vue";
import { cn } from "@/lib/utils";

const props = defineProps<{
  class?: HTMLAttributes["class"]
}>();
</script>

<template>
  <thead :class="cn('sticky top-0 z-10 [&_tr]:hover:bg-transparent [&_tr]:border-b-0 [&_tr]:relative [&_tr]:after:absolute [&_tr]:after:bottom-0 [&_tr]:after:left-0 [&_tr]:after:right-0 [&_tr]:after:h-px [&_tr]:after:bg-border bg-muted', props.class)">
    <slot />
  </thead>
</template>
