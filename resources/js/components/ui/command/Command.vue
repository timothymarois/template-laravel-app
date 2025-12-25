<script setup lang="ts">
import type { ListboxRootEmits, ListboxRootProps } from "reka-ui";
import type { HTMLAttributes } from "vue";
import { reactiveOmit } from "@vueuse/core";
import { ListboxRoot, useFilter, useForwardPropsEmits } from "reka-ui";
import { computed, reactive, ref, watch } from "vue";
import { cn } from '@/utils';
import { provideCommandContext } from ".";
import { filterCommandItems } from "./filterCommandItems";

interface Props extends ListboxRootProps {
    class?: HTMLAttributes["class"];
    searchTerm?: string;
    /** Disable internal filtering - useful for async/server-side search */
    disableFilter?: boolean;
}

const props = withDefaults(defineProps<Props>(), {
    modelValue: "",
    searchTerm: "",
    disableFilter: false,
});

const emits = defineEmits<ListboxRootEmits & {
    'update:searchTerm': [value: string];
    'searchChange': [value: string];
}>();

const delegatedProps = reactiveOmit(props, "class", "searchTerm", "disableFilter");

const forwarded = useForwardPropsEmits(delegatedProps, emits);

const allItems = ref<Map<string, string>>(new Map());
const allGroups = ref<Map<string, Set<string>>>(new Map());

const { contains } = useFilter({ sensitivity: "base" });
const filterState = reactive({
    search: "",
    filtered: {
        /** The count of all visible items. */
        count: 0,
        /** Map from visible item id to its search score. */
        items: new Map() as Map<string, number>,
        /** Set of groups with at least one visible item. */
        groups: new Set() as Set<string>,
    },
});

function filterItems() {
    const result = filterCommandItems({
        allItems: allItems.value,
        allGroups: allGroups.value,
        search: filterState.search,
        disableFilter: props.disableFilter,
        contains,
        previous: {
            items: filterState.filtered.items,
            groups: filterState.filtered.groups,
        },
    });

    filterState.filtered.count = result.count;
    filterState.filtered.items = result.items;
    filterState.filtered.groups = result.groups;
}

watch(() => filterState.search, (newSearch) => {
    filterItems();
    // Emit search term changes for v-model:search-term support
    emits('update:searchTerm', newSearch);
    // Also emit searchChange for direct listening
    emits('searchChange', newSearch);
});

// Sync external searchTerm prop to internal filterState
watch(() => props.searchTerm, (newTerm) => {
    if (filterState.search !== newTerm) {
        filterState.search = newTerm;
    }
}, { immediate: true });

const disableFilterRef = computed(() => props.disableFilter);

provideCommandContext({
    allItems,
    allGroups,
    filterState,
    disableFilter: disableFilterRef,
});
</script>

<template>
    <ListboxRoot
        v-bind="forwarded"
        :class="cn('flex h-full w-full flex-col overflow-hidden rounded-md bg-popover text-popover-foreground', props.class)"
    >
        <slot />
    </ListboxRoot>
</template>
