<template>
    <div class="relative inline-block">
        <slot name="trigger" :toggle="togglePopover" />
        <Popover ref="popover" pt:content="p-0" @hide="applyInitColumns()" @show="onPopoverShow()">
            <div class="flex flex-col w-[360px]">
                <div class="p-4 py-3">
                    <div class="text-lg font-semibold flex items-center gap-x-1 text-surface-900 dark:text-surface-100">
                        Customize columns
                    </div>
                </div>
                <div
                    class="p-4 pt-0 border-b border-surface-200 dark:border-surface-700 text-sm font-normal"
                    :class="{ 'shadow-sm': !isTop }"
                >
                    <InputText
                        v-model="searchColumns"
                        size="small"
                        clearable
                        placeholder="Search columns"
                    />
                </div>
                <div ref="frame" class="h-[300px] overflow-hidden overflow-y-auto">
                    <div class="text-xs py-1 px-3 border-b border-surface-200 dark:border-surface-700 text-surface-900 dark:text-surface-100 font-semibold uppercase">
                        Visible
                    </div>
                    <draggable
                        v-model="selectedColumns"
                        item-key="key"
                        group="columns"
                        class="p-2 px-3"
                        ghost-class="ghost-card"
                        :animation="200"
                        :move="checkLocked"
                    >
                        <template #item="{ element: column }">
                            <div
                                v-if="!column?.hidden && column.header.toLowerCase().includes(searchColumns.toLowerCase())"
                                class="flex items-center w-full hover:bg-surface-100 dark:hover:bg-surface-700 rounded p-1 cursor-pointer text-sm"
                                :class="{ 'cursor-not-allowed opacity-50': column.locked }"
                                @click="!column.locked && toggleColumn(column.key)"
                            >
                                <div class="grow flex items-center space-x-2">
                                    <IconGripVertical
                                        class="cursor-grab text-surface-400 dark:text-surface-500 size-4"
                                        :class="{ 'cursor-not-allowed opacity-50': column.locked }"
                                    />
                                    <Checkbox
                                        v-model="activeColumns[column.key]"
                                        binary
                                        size="small"
                                        :disabled="column.locked"
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
                                    class="text-surface-400 dark:text-surface-500 text-xs"
                                >
                                    {{ column.group }}
                                </div>
                            </div>
                        </template>
                    </draggable>
                    <template v-if="Object.keys(filteredUnselectedColumnGroups).length > 0">
                        <div
                            class="text-xs py-1 px-3 border-y border-surface-200 dark:border-surface-700 text-surface-900 dark:text-surface-100 font-semibold uppercase"
                        >
                            Not visible
                        </div>
                        <div class="flex flex-col w-full p-2 px-3 space-y-2">
                            <template
                                v-for="(group, groupName) in filteredUnselectedColumnGroups"
                                :key="groupName"
                            >
                                <div
                                    v-if="groupName"
                                    class="flex items-center gap-2 text-xs py-1 pb-0 px-0 text-surface-400 dark:text-surface-500 font-semibold"
                                >
                                    <span>{{ groupName }}</span>
                                    <div class="h-px bg-surface-200 dark:bg-surface-700 flex-1" />
                                </div>
                                <div class="flex flex-col w-full">
                                    <div
                                        v-for="column in group"
                                        :key="column.key"
                                        class="flex items-center w-full hover:bg-surface-100 dark:hover:bg-surface-700 p-1 cursor-pointer text-sm rounded"
                                        :class="{ 'cursor-not-allowed opacity-50': column.locked }"
                                        @click="!column.locked && toggleColumn(column.key)"
                                    >
                                        <div class="grow flex items-center space-x-2">
                                            <Checkbox
                                                v-model="activeColumns[column.key]"
                                                binary
                                                size="small"
                                                :disabled="column.locked"
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
                <div class="flex items-center justify-start border-t border-surface-200 dark:border-surface-700 p-3 shadow">
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
                            @click="() => applyColumns(props.defaultColumnList)"
                        />
                    </div>
                </div>
            </div>
        </Popover>
    </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted, nextTick } from 'vue';
import { IconGripVertical } from '@tabler/icons-vue';
import draggable from 'vuedraggable';
import Popover from '../../Popover.vue';
import Button from '../../Button.vue';
import InputText from '../../InputText.vue';
import Checkbox from '../../Checkbox.vue';
import { useScroll } from '../../../composables/useScroll';

const props = defineProps({
    columns: Array,
    activeColumnList: Array,
    defaultColumnList: Array,
});

const emit = defineEmits(['update']);

const { bindScrollHandler, isTop } = useScroll('customize-columns');

const frame = ref<any>(null);
const popover = ref<any>(null);
const searchColumns = ref('');
const activeColumns = ref<Record<string, boolean>>({});
const selectedColumns = ref<any[]>([]);

const togglePopover = (event: any) => {
    popover.value.toggle(event);
};

const applyColumns = (columnsList: any[]) => {
    const map: Record<string, boolean> = {};
    const selected: any[] = [];

    for (const column of props.columns as any[]) {
        const isActive = columnsList.includes(column.key);
        map[column.key] = isActive;
        if (isActive) selected.push(column);
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

const isDefault = computed(() => {
    const defaultKeys = (props.defaultColumnList as any[]).filter(key =>
        (props.columns as any[]).some(col => col.key === key)
    );

    return defaultKeys.length === selectedColumns.value.length &&
        defaultKeys.every((key, i) => selectedColumns.value[i]?.key === key);
});

const submitColumns = () => {
    emit('update', selectedColumns.value.map(col => col.key));
    close();
};

const checkLocked = ({ draggedContext, relatedContext }: any) => {
    return !draggedContext.element?.locked && !relatedContext.element?.locked;
};

const close = () => popover.value.hide();

const onPopoverShow = () => {
    applyInitColumns();
    nextTick(() => {
        bindScrollHandler(frame).add();
    });
};
</script>

<style scoped>
.ghost-card {
    opacity: 0;
}
</style>
