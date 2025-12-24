<script setup lang="ts">
import type { ListboxRootEmits, ListboxRootProps } from "reka-ui";
import type { HTMLAttributes } from "vue";
import { reactiveOmit } from "@vueuse/core";
import { ListboxRoot, useFilter, useForwardPropsEmits } from "reka-ui";
import { computed, reactive, ref, watch } from "vue";
import { cn } from '@/utils';
import { provideCommandContext } from ".";

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
    // Skip filtering if disabled (for async/server-side search)
    if (props.disableFilter) {
        // Mark all items as visible when filtering is disabled
        filterState.filtered.count = allItems.value.size;
        for (const [id] of allItems.value) {
            filterState.filtered.items.set(id, 1);
        }
        for (const [groupId] of allGroups.value) {
            filterState.filtered.groups.add(groupId);
        }
        return;
    }

    if (!filterState.search) {
        filterState.filtered.count = allItems.value.size;
        // Do nothing, each item will know to show itself because search is empty
        return;
    }

    // Reset the groups
    filterState.filtered.groups = new Set();
    let itemCount = 0;

    // Check which items should be included
    for (const [id, value] of allItems.value) {
        const score = contains(value, filterState.search);
        filterState.filtered.items.set(id, score ? 1 : 0);
        if (score)
            itemCount++;
    }

    // Check which groups have at least 1 item shown
    for (const [groupId, group] of allGroups.value) {
        for (const itemId of group) {
            if (filterState.filtered.items.get(itemId)! > 0) {
                filterState.filtered.groups.add(groupId);
                break;
            }
        }
    }

    filterState.filtered.count = itemCount;
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
