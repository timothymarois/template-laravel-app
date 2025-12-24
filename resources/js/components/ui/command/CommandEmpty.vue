<script setup lang="ts">
import type { PrimitiveProps } from "reka-ui";
import type { HTMLAttributes } from "vue";
import { reactiveOmit } from "@vueuse/core";
import { Primitive } from "reka-ui";
import { computed } from "vue";
import { cn } from '@/utils';
import { useCommand } from ".";

const props = defineProps<PrimitiveProps & { class?: HTMLAttributes["class"] }>();

const delegatedProps = reactiveOmit(props, "class");

const { filterState, disableFilter, allItems } = useCommand();
const isRender = computed(() => {
    // When filtering is disabled (async mode), don't show empty based on filter count
    // Instead, check if there are actually no items
    if (disableFilter.value) {
        return allItems.value.size === 0;
    }
    return !!filterState.search && filterState.filtered.count === 0;
});
</script>

<template>
    <Primitive v-if="isRender" v-bind="delegatedProps" :class="cn('py-6 text-center text-sm', props.class)">
        <slot />
    </Primitive>
</template>
