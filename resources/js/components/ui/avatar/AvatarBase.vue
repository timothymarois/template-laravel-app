<script setup lang="ts">
import type { HTMLAttributes } from "vue";
import type { AvatarRootProps } from "reka-ui";
import { AvatarRoot } from "reka-ui";
import { cn } from "@/utils";

const props = defineProps<AvatarRootProps & {
    class?: HTMLAttributes["class"]
}>();
</script>

<template>
    <AvatarRoot :class="cn('relative flex shrink-0 overflow-hidden rounded-full', props.class)">
        <slot />
    </AvatarRoot>
</template>
