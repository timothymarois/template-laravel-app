<script setup lang="ts">
import type { HTMLAttributes } from "vue";
import { cn } from "@/utils";

const props = defineProps<{ class?: HTMLAttributes["class"] }>();
</script>

<template>
    <!-- Sheet panels appear on the side, so submit button goes on left (closer to content) -->
    <div
        :class="
            cn(
                'flex flex-col-reverse sm:flex-row sm:justify-start sm:gap-x-2',
                props.class,
            )
        "
    >
        <slot />
    </div>
</template>
