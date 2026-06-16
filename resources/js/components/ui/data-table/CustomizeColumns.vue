<template>
    <Popover v-model:open="isOpen">
        <PopoverTrigger as-child>
            <slot>
                <Button variant="outline" size="icon">
                    <Settings2 class="size-4" />
                </Button>
            </slot>
        </PopoverTrigger>
        <PopoverContent class="w-[360px] p-0" @open-auto-focus.prevent>
            <div class="flex flex-col">
                <div class="p-4 py-3" :class="{ 'border-b border-border': !showSearch }">
                    <div class="text-lg font-semibold flex items-center gap-x-1 text-foreground">
                        Customize columns
                    </div>
                </div>
                <div
                    v-if="showSearch"
                    class="p-4 pt-0 border-b border-border text-sm font-normal"
                    :class="{ 'shadow-sm': !isTop }"
                >
                    <InputText
                        v-model="searchColumns"
                        size="small"
                        clearable
                        placeholder="Search columns"
                    />
                </div>
                <div ref="frame" class="max-h-[300px] overflow-hidden overflow-y-auto" :class="{ 'is-dragging': isDragging }">
                    <div class="text-xs py-1 px-4 border-b border-border text-foreground font-semibold uppercase">
                        Visible
                    </div>
                    <VueDraggable
                        v-model="draggableColumns"
                        group="columns"
                        class="p-2 px-4"
                        ghost-class="ghost-card"
                        :animation="200"
                        @move="checkLocked"
                        @start="isDragging = true"
                        @end="isDragging = false"
                    >
                        <template v-for="column in draggableColumns" :key="column.key">
                            <div
                                v-if="column.header.toLowerCase().includes(searchColumns.toLowerCase())"
                                :data-column-key="column.key"
                                class="flex items-center w-full hover:bg-accent rounded p-1 cursor-pointer text-sm"
                                :class="{ 'cursor-not-allowed opacity-50': column.locked }"
                                @click="!column.locked && toggleColumn(column.key)"
                            >
                                <div class="grow flex items-center space-x-2">
                                    <IconGripVertical
                                        class="text-muted-foreground size-4"
                                        :class="column.locked ? 'cursor-not-allowed opacity-50' : 'cursor-grab'"
                                    />
                                    <Checkbox
                                        :model-value="activeColumns[column.key]"
                                        binary
                                        size="small"
                                        :disabled="column.locked"
                                        @click.stop="!column.locked && toggleColumn(column.key)"
                                    />
                                    <div
                                        :class="{
                                            'cursor-not-allowed': column.locked,
                                            'hover:underline': !column.locked
                                        }"
                                    >
                                        {{ column.header }}
                                    </div>
                                </div>
                                <div
                                    v-if="column.group"
                                    class="text-muted-foreground text-xs"
                                >
                                    {{ column.group }}
                                </div>
                            </div>
                        </template>
                    </VueDraggable>
                    <template v-if="Object.keys(filteredUnselectedColumnGroups).length > 0">
                        <div
                            class="text-xs py-1 px-4 border-y border-border text-foreground font-semibold uppercase"
                        >
                            Not visible
                        </div>
                        <div class="flex flex-col w-full p-2 px-4 space-y-2">
                            <template
                                v-for="(group, groupName) in filteredUnselectedColumnGroups"
                                :key="groupName"
                            >
                                <div
                                    v-if="groupName"
                                    class="flex items-center gap-2 text-xs py-1 pb-0 px-0 text-muted-foreground font-semibold"
                                >
                                    <span>{{ groupName }}</span>
                                    <div class="h-px bg-border flex-1" />
                                </div>
                                <div class="flex flex-col w-full">
                                    <div
                                        v-for="column in group"
                                        :key="column.key"
                                        class="flex items-center w-full hover:bg-accent p-1 cursor-pointer text-sm rounded"
                                        :class="{ 'cursor-not-allowed opacity-50': column.locked }"
                                        @click="!column.locked && toggleColumn(column.key)"
                                    >
                                        <div class="grow flex items-center space-x-2">
                                            <Checkbox
                                                :model-value="activeColumns[column.key]"
                                                binary
                                                size="small"
                                                :disabled="column.locked"
                                                @click.stop="!column.locked && toggleColumn(column.key)"
                                            />
                                            <div class="hover:underline">
                                                {{ column.header }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </template>
                </div>
                <div class="flex items-center justify-start border-t border-border p-3 shadow">
                    <div class="grow flex items-center space-x-2">
                        <Button label="Save" size="small" raised @click="submitColumns" />
                        <Button text type="button" label="Cancel" size="small" @click="close" />
                    </div>
                    <div>
                        <Button
                            label="Reset to default"
                            size="small"
                            text
                            :disabled="isDefault"
                            @click="resetToDefault"
                        />
                    </div>
                </div>
            </div>
        </PopoverContent>
    </Popover>
</template>

<script setup lang="ts">
import { ref, computed, watch, nextTick, useSlots } from 'vue';
import { IconGripVertical } from '@tabler/icons-vue';
import { Settings2 } from 'lucide-vue-next';
import { VueDraggable } from 'vue-draggable-plus';
import {
    PopoverBase as Popover,
    PopoverContent,
    PopoverTrigger,
} from '@/components/ui/popover';
import { Button } from '@/components/ui/button';
import { Input as InputText } from '@/components/ui/input';
import { Checkbox } from '@/components/ui/checkbox';
import { useScroll } from '@/composables';
import { buildColumnSelectionState, filterUnselectedColumnGroups, isDefaultColumnSelection } from './dataTableUtils';

interface SortState {
    column: string | null;
    direction: 'asc' | 'desc' | null;
}

interface Column {
    key: string;
    header: string;
    group?: string;
    locked?: boolean;
    hidden?: boolean;
}

const props = defineProps<{
    modelValue: string[];
    columns: Column[];
    defaultColumns: string[];
    sort?: SortState;
    defaultSort?: SortState;
}>();

const emit = defineEmits<{
    'update:modelValue': [columns: string[]];
    'update:sort': [sort: SortState];
}>();

const slots = useSlots();
const defaultSortState: SortState = { column: null, direction: null };

const { bindScrollHandler, isTop } = useScroll('customize-columns');

const frame = ref<HTMLElement | null>(null);
const isOpen = ref(false);
const isDragging = ref(false);
const searchColumns = ref('');
const activeColumns = ref<Record<string, boolean>>({});
const selectedColumns = ref<Column[]>([]);

const draggableColumns = computed({
    get: () => selectedColumns.value.filter(column => !column.hidden),
    set: (nextColumns: Column[]) => {
        const hiddenColumns = selectedColumns.value
            .map((column, index) => column.hidden ? { column, index } : null)
            .filter((entry): entry is { column: Column; index: number } => Boolean(entry));

        const totalLength = nextColumns.length + hiddenColumns.length;
        const hiddenByIndex = new Map(hiddenColumns.map(entry => [entry.index, entry.column]));
        const reordered: Column[] = [];
        let visibleIndex = 0;

        for (let index = 0; index < totalLength; index += 1) {
            const hiddenColumn = hiddenByIndex.get(index);
            if (hiddenColumn) {
                reordered.push(hiddenColumn);
            } else {
                reordered.push(nextColumns[visibleIndex]);
                visibleIndex += 1;
            }
        }

        selectedColumns.value = reordered;
    },
});

const applyColumns = (columnsList: string[]) => {
    const { activeColumns: nextActiveColumns, selectedColumns: nextSelectedColumns } =
        buildColumnSelectionState(props.columns, columnsList);

    activeColumns.value = nextActiveColumns;
    selectedColumns.value = nextSelectedColumns;
};

const applyInitColumns = () => {
    applyColumns(props.modelValue.length ? props.modelValue : props.defaultColumns);
};

const toggleColumn = (key: string) => {
    const index = selectedColumns.value.findIndex(col => col.key === key);
    const column = props.columns.find(col => col.key === key);
    if (!column || column.locked) return;

    activeColumns.value[key] = !activeColumns.value[key];

    if (activeColumns.value[key] && index === -1) {
        selectedColumns.value.push(column);
    } else if (!activeColumns.value[key] && index !== -1) {
        selectedColumns.value.splice(index, 1);
    }
};

const filteredUnselectedColumnGroups = computed(() =>
    filterUnselectedColumnGroups(props.columns, activeColumns.value, searchColumns.value)
);

const showSearch = computed(() => props.columns.length > 10);

const isSortDefault = computed(() => {
    const currentSort = props.sort ?? defaultSortState;
    const defaultSort = props.defaultSort ?? defaultSortState;
    return currentSort.column === defaultSort.column && currentSort.direction === defaultSort.direction;
});

const isColumnsDefault = computed(() =>
    isDefaultColumnSelection(props.defaultColumns, selectedColumns.value, props.columns)
);

const isDefault = computed(() => isColumnsDefault.value && isSortDefault.value);

const resetToDefault = () => {
    applyColumns(props.defaultColumns);
    emit('update:sort', props.defaultSort ?? defaultSortState);
    emit('update:modelValue', props.defaultColumns);
    close();
};

const submitColumns = () => {
    emit('update:modelValue', selectedColumns.value.map(col => col.key));
    close();
};

// vue-draggable-plus's `onMove` hands back the raw Sortable.js MoveEvent
// (DOM nodes), not vuedraggable's `draggedContext`/`relatedContext` data
// wrappers. Resolve each row's column via its `data-column-key` and reject
// the move when either the dragged or target column is locked.
const checkLocked = (evt: any) => {
    const draggedKey = (evt?.dragged as HTMLElement | undefined)?.dataset?.columnKey;
    const relatedKey = (evt?.related as HTMLElement | undefined)?.dataset?.columnKey;
    const dragged = selectedColumns.value.find(column => column.key === draggedKey);
    const related = selectedColumns.value.find(column => column.key === relatedKey);
    return !dragged?.locked && !related?.locked;
};

const close = () => { isOpen.value = false; };

// Store scroll handler reference for cleanup
let scrollHandler: { add: () => void; remove: () => void } | null = null;

// Watch for open state changes
watch(isOpen, (newValue) => {
    if (newValue) {
        applyInitColumns();
        nextTick(() => {
            scrollHandler = bindScrollHandler(frame);
            scrollHandler.add();
        });
    } else {
        scrollHandler?.remove();
        scrollHandler = null;
    }
});
</script>

<style scoped>
.ghost-card {
    opacity: 0;
}

.is-dragging,
.is-dragging * {
    cursor: grabbing !important;
}
</style>
