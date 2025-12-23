<script setup lang="ts">
import type { HTMLAttributes } from "vue";
import { cn } from '@/utils';

interface SkeletonProps {
    class?: HTMLAttributes["class"]
}

const props = defineProps<SkeletonProps>();
</script>

<template>
  <div
    data-slot="skeleton"
    :class="cn('rounded-md skeleton-base', props.class)"
  />
</template>

<style>
.skeleton-base {
  background: linear-gradient(
    90deg,
    hsl(var(--muted)) 0%,
    hsl(var(--muted-foreground) / 0.15) 20%,
    hsl(var(--muted-foreground) / 0.2) 25%,
    hsl(var(--muted-foreground) / 0.15) 30%,
    hsl(var(--muted)) 50%,
    hsl(var(--muted)) 100%
  );
  background-size: 200% 100%;
  animation: skeleton-shimmer 1.2s linear infinite;
}

@keyframes skeleton-shimmer {
  0% {
    background-position: 100% 0;
  }
  100% {
    background-position: -100% 0;
  }
}
</style>
