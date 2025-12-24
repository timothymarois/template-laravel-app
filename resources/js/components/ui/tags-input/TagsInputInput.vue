<script setup lang="ts">
import type { TagsInputInputProps } from "reka-ui";
import type { HTMLAttributes } from "vue";
import { reactiveOmit } from "@vueuse/core";
import { TagsInputInput, useForwardProps } from "reka-ui";
import { cn } from '@/utils';

const props = defineProps<TagsInputInputProps & { class?: HTMLAttributes["class"] }>();

const delegatedProps = reactiveOmit(props, "class");

const forwardedProps = useForwardProps(delegatedProps);
</script>

<template>
    <TagsInputInput
        v-bind="forwardedProps"
        :class="cn(
            'flex-1 bg-transparent text-sm placeholder:text-muted-foreground focus:outline-none min-w-20',
            props.class,
        )"
    />
</template>
