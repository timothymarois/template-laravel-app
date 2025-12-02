<template>
    <ScrollFrame rootClass="overflow-hidden relative" :addOffset="scrollOffset" :scrollable="scrollable">
        <div v-if="hasCustomizeColumns" class="flex items-center absolute -top-1 z-[99] right-[10px]">
            <CustomizeColumns
                :columns="columns"
                :activeColumnList="activeColumnList"
                :defaultColumnList="defaultColumnList"
                @update="$emit('update:activeColumnList', $event)"
            >
                <template #trigger="{ toggle }">
                    <div
                        v-tooltip.left="{
                            value: 'Customize columns',
                            pt: {
                                root: 'absolute shadow-md py-0 px-0 max-w-[260px]',
                                text: 'text-sm p-2 border border-surface-700 bg-surface-900 text-white dark:bg-surface-0 dark:border-surface-300 dark:text-black rounded-[var(--p-content-border-radius)] whitespace-pre-line'
                            }
                        }"
                        class="bg-surface-0 dark:bg-surface-900 border border-surface-300 dark:border-surface-700 cursor-pointer text-surface-600 dark:text-surface-300 hover:text-surface-800 dark:hover:text-surface-200 hover:bg-surface-100 dark:hover:bg-surface-700 hover:border-surface-400 dark:hover:border-surface-600 transition-colors duration-200 h-[38px] w-[38px] flex items-center justify-center rounded-bl-lg rounded-br-lg"
                        @click="toggle"
                    >
                        <IconSettings class="size-5" />
                    </div>
                </template>
            </CustomizeColumns>
        </div>
        <DataTable
            lazy
            v-bind="{
                value: items,
                selection: selectedItems,
                dataKey: hasSelection ? dataKey : undefined,
                rowHover: true,
                showGridlines: true,
                scrollHeight: scrollable ? 'flex' : undefined,
                scrollable: scrollable,
                size,
                tableStyle,
                ...$attrs
            }"
            @sort="handleSort"
            @update:selection="emit('update:selected', $event)"
            @row-select-all="emit('update:selectAll', false)"
            @row-unselect-all="emit('update:selectAll', false)"
            @row-select="emit('update:selectAll', false)"
            @row-unselect="selectAll ? resetSelection() : () => {}"
        >
            <Column
                v-if="hasSelection"
                selectionMode="multiple"
                class="text-center"
                header-style="width: 2rem;text-align:center;"
                :class="{ 'hide-select': hasSelectAll }"
                frozen
            >
                <template v-if="hasSelectAll" #header>
                    <ButtonMenu :items="selectOptions" :onHover="true" />
                </template>
            </Column>
            <template v-for="column in tableColumns" :key="column.key">
                <Column
                    :field="column.key"
                    :header="column.header"
                    :style="column.style"
                    :class="column.class"
                    :body-class="column.bodyClass"
                    :sortable="column.sortable ?? false"
                    :frozen="column.frozen ?? false"
                    v-bind="column.props"
                >
                    <template v-if="$slots[column.key]" #body="slotProps">
                        <slot :name="column.key" v-bind="slotProps" />
                    </template>
                    <template #sorticon="{ sorted, sortOrder }">
                        <span
                            class="inline-block transition-transform"
                            :class="{
                                'pi pi-sort': !sorted,
                                'pi pi-sort-up': sortOrder === 1,
                                'pi pi-sort-down': sortOrder === -1
                            }"
                        />
                    </template>
                </Column>
            </template>
            <template #empty>
                <slot name="empty">
                    <div class="text-center p-4">{{  emptyLabel  }}</div>
                </slot>
            </template>
        </DataTable>
    </ScrollFrame>
</template>

<script setup lang="ts">
import { computed, onMounted, useAttrs } from 'vue';
import Column from 'primevue/column';
import DataTable from '../../DataTable.vue';
import ButtonMenu from '../../ButtonMenu.vue';
import CustomizeColumns from './CustomizeColumns.vue';
import ScrollFrame from '../../ScrollFrame.vue';
import { IconSettings } from '@tabler/icons-vue';
import { formatNumber } from '../../../utils';

const emit = defineEmits(['sort', 'update:selected', 'update:selectAll', 'update:activeColumnList']);

const props = defineProps({
    items: {
        type: Array,
        required: true,
    },
    itemTotal: {
        type: Number,
        default: 0
    },
    selected: {
        type: Array,
        default: undefined,
    },
    selectAll: {
        type: Boolean,
        default: false,
    },
    columns: {
        type: Array,
        required: true,
    },
    activeColumnList: {
        type: Array,
        required: true,
    },
    defaultColumnList: {
        type: Array,
        default: () => []
    },
    size: {
        type: String,
        default: 'small',
    },
    tableStyle: {
        type: String,
        default: 'min-width: 50rem',
    },
    dataKey: {
        type: String,
        default: 'id',
    },
    emptyLabel : {
        type: String,
        default: 'No results'
    },
    hasSelectAll: {
        type: Boolean,
        default: true
    },
    hasSelection: {
        type: Boolean,
        default: false
    },
    hasCustomizeColumns: {
        type: Boolean,
        default: false
    },
    scrollOffset: {
        type: Number,
        default: 0
    },
    scrollable: {
        type: Boolean,
        default: false
    }
});

const $attrs = useAttrs();

const selectedItems = computed(() => props.selectAll ? props.items : props.selected);

const handleSort = (event: any) => {
    emit('sort', {
        field: event.sortField,
        order: event.sortOrder,
    });
};

const resetSelection = () => {
    emit('update:selectAll', false);
    emit('update:selected', []);
};

const selectOptions = computed(() => [
    {
        label: `Select All (${formatNumber(props.itemTotal)})`,
        click: () => {
            emit('update:selectAll', true);
            emit('update:selected', []);
        }
    },
    {
        label: 'Select Visible',
        click: () => {
            emit('update:selectAll', false);
            emit('update:selected', props.items);
        }
    },
    {
        label: 'Select None',
        click: () => {
            resetSelection();
        }
    },
]);

const tableColumns = computed(() =>
    (props.activeColumnList as any[])
        .map(key => (props.columns as any[]).find(col => col.key === key))
        .filter(Boolean)
);

onMounted(() => {
    if (props.activeColumnList.length === 0) {
        emit('update:activeColumnList', props.defaultColumnList);
    }
});

</script>

<style>
.hide-select [data-pc-name="pcheadercheckbox"] {
    display: none ! important;
}
</style>
