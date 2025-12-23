<script setup lang="ts">
import type { ContextMenuGroupProps } from "reka-ui";
import { ContextMenuGroup, useForwardProps } from "reka-ui";

const props = defineProps<ContextMenuGroupProps>();

const forwardedProps = useForwardProps(props);
</script>

<template>
    <ContextMenuGroup v-bind="forwardedProps">
        <slot />
    </ContextMenuGroup>
</template>
