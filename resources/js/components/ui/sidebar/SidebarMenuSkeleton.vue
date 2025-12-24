<script setup lang="ts">
import type { HTMLAttributes } from "vue";
import { ref, onMounted } from "vue";
import { cn } from '@/utils';
import { Skeleton } from '@/components/ui/skeleton';

const props = defineProps<{
    showIcon?: boolean
    class?: HTMLAttributes["class"]
}>();

// Use a default value for SSR, randomize on mount to avoid hydration mismatch
const width = ref('70%');

onMounted(() => {
    width.value = `${Math.floor(Math.random() * 40) + 50}%`;
});
</script>

<template>
    <div
        data-sidebar="menu-skeleton"
        :class="cn('rounded-md h-8 flex gap-2 px-2 items-center', props.class)"
    >
        <Skeleton
            v-if="showIcon"
            class="size-4 rounded-md"
            data-sidebar="menu-skeleton-icon"
        />

        <Skeleton
            class="h-4 flex-1 max-w-[--skeleton-width]"
            data-sidebar="menu-skeleton-text"
            :style="{ '--skeleton-width': width }"
        />
    </div>
</template>
