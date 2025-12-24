<template>
    <Popover v-model:open="isOpen">
        <PopoverTrigger>
            <Button variant="outline" size="icon">
                <Settings2 class="size-4" />
            </Button>
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
                <div ref="frame" class="max-h-[300px] overflow-hidden overflow-y-auto">
                    <div class="text-xs py-1 px-4 border-b border-border text-foreground font-semibold uppercase">
                        Visible
                    </div>
                    <draggable
                        v-model="selectedColumns"
                        item-key="key"
                        group="columns"
                        class="p-2 px-4"
                        ghost-class="ghost-card"
                        :animation="200"
                        :move="checkLocked"
                    >
                        <template #item="{ element: column }">
                            <div
                                v-if="!column?.hidden && column.header.toLowerCase().includes(searchColumns.toLowerCase())"
                                class="flex items-center w-full hover:bg-accent rounded p-1 cursor-pointer text-sm"
                                :class="{ 'cursor-not-allowed opacity-50': column.locked }"
                                @click="!column.locked && toggleColumn(column.key)"
                            >
                                <div class="grow flex items-center space-x-2">
                                    <IconGripVertical
                                        class="cursor-grab text-muted-foreground size-4"
                                        :class="{ 'cursor-not-allowed opacity-50': column.locked }"
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
                    </draggable>
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
import { ref, computed, watch, nextTick } from 'vue';
import { IconGripVertical } from '@tabler/icons-vue';
import { Settings2 } from 'lucide-vue-next';
import draggable from 'vuedraggable';
import {
    PopoverBase as Popover,
    PopoverContent,
    PopoverTrigger,
} from '@/components/ui/popover';
import { Button } from '@/components/ui/button';
import { Input as InputText } from '@/components/ui/input';
import { Checkbox } from '@/components/ui/checkbox';
import { useScroll } from '@/composables';

interface SortState {
    column: string | null;
    direction: 'asc' | 'desc' | null;
}

const props = defineProps<{
    columns: any[];
    activeColumnList: any[];
    defaultColumnList: any[];
    sort?: SortState;
    defaultSort?: SortState;
}>();

const emit = defineEmits<{
    update: [columns: string[]];
    'update:sort': [sort: SortState];
}>();

const defaultSortState: SortState = { column: null, direction: null };

const { bindScrollHandler, isTop } = useScroll('customize-columns');

const frame = ref<any>(null);
const isOpen = ref(false);
const searchColumns = ref('');
const activeColumns = ref<Record<string, boolean>>({});
const selectedColumns = ref<any[]>([]);

const applyColumns = (columnsList: any[]) => {
    const map: Record<string, boolean> = {};
    const selected: any[] = [];

    // Build map for all columns
    for (const column of props.columns) {
        map[column.key] = columnsList.includes(column.key);
    }

    // Build selected list in the order of columnsList
    for (const key of columnsList) {
        const column = props.columns.find(col => col.key === key);
        if (column) selected.push(column);
    }

    activeColumns.value = map;
    selectedColumns.value = selected;
};

const applyInitColumns = () => {
    applyColumns(props.activeColumnList.length ? props.activeColumnList : props.defaultColumnList);
};

const toggleColumn = (key: string) => {
    const index = selectedColumns.value.findIndex(col => col.key === key);
    const column = (props.columns as any[]).find(col => col.key === key);
    if (!column || (column as any).locked) return;

    activeColumns.value[key] = !activeColumns.value[key];

    if (activeColumns.value[key] && index === -1) {
        selectedColumns.value.push(column);
    } else if (!activeColumns.value[key] && index !== -1) {
        selectedColumns.value.splice(index, 1);
    }
};

const filteredUnselectedColumnGroups = computed(() => {
    const list = (props.columns as any[]).filter(col =>
        !activeColumns.value[col.key] &&
        col.header.toLowerCase().includes(searchColumns.value.toLowerCase())
    );

    return list.reduce((groups: Record<string, any[]>, column: any) => {
        const group = column.group || '';
        (groups[group] ||= []).push(column);
        return groups;
    }, {} as Record<string, any[]>);
});

const showSearch = computed(() => (props.columns as any[])?.length > 10);

const isSortDefault = computed(() => {
    const currentSort = props.sort ?? defaultSortState;
    const defaultSort = props.defaultSort ?? defaultSortState;
    return currentSort.column === defaultSort.column && currentSort.direction === defaultSort.direction;
});

const isColumnsDefault = computed(() => {
    const defaultKeys = props.defaultColumnList.filter(key =>
        props.columns.some(col => col.key === key)
    );

    return defaultKeys.length === selectedColumns.value.length &&
        defaultKeys.every((key, i) => selectedColumns.value[i]?.key === key);
});

const isDefault = computed(() => isColumnsDefault.value && isSortDefault.value);

const resetToDefault = () => {
    applyColumns(props.defaultColumnList);
    emit('update:sort', props.defaultSort ?? defaultSortState);
    emit('update', props.defaultColumnList);
    close();
};

const submitColumns = () => {
    emit('update', selectedColumns.value.map(col => col.key));
    close();
};

const checkLocked = ({ draggedContext, relatedContext }: any) => {
    return !draggedContext.element?.locked && !relatedContext.element?.locked;
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
</style>
