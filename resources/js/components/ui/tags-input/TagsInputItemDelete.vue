<script setup lang="ts">
import type { TagsInputItemDeleteProps } from "reka-ui";
import type { HTMLAttributes } from "vue";
import { reactiveOmit } from "@vueuse/core";
import { X } from "lucide-vue-next";
import { TagsInputItemDelete, useForwardProps } from "reka-ui";
import { cn } from '@/utils';

const props = defineProps<TagsInputItemDeleteProps & { class?: HTMLAttributes["class"] }>();

const delegatedProps = reactiveOmit(props, "class");

const forwardedProps = useForwardProps(delegatedProps);
</script>

<template>
    <TagsInputItemDelete
        v-bind="forwardedProps"
        :class="cn(
            'rounded-sm cursor-pointer transition-colors',
            'hover:text-destructive',
            'focus:outline-none focus:ring-1 focus:ring-ring',
            props.class,
        )"
    >
        <slot>
            <X class="size-3" />
        </slot>
    </TagsInputItemDelete>
</template>
