<template>
    <ScrollFrame ref="scrollFrameRef" rootClass="overflow-y-auto overflow-x-auto relative" :addOffset="computedScrollOffset" :scrollable="scrollable" @scroll="onScroll">
        <Table>
            <TableHeader :class="{ 'shadow-[0_1px_3px_rgba(0,0,0,0.1)]': isScrolled }">
                <TableRow>
                    <TableHead v-if="hasSelection" class="w-12 text-center">
                        <DropdownMenu v-if="hasSelectAll">
                            <DropdownMenuTrigger as-child>
                                <button class="inline-flex items-center justify-center cursor-pointer">
                                    <Checkbox
                                        :checked="isAllSelected"
                                        :indeterminate="isSomeSelected"
                                        class="pointer-events-none"
                                    />
                                    <ChevronDown class="h-3 w-3 ml-1 text-muted-foreground" />
                                </button>
                            </DropdownMenuTrigger>
                            <DropdownMenuContent align="start">
                                <DropdownMenuItem @click="selectAllItems">
                                    Select All ({{ formatNumber(props.itemTotal ?? 0) }})
                                </DropdownMenuItem>
                                <DropdownMenuItem @click="selectVisibleItems">
                                    Select Visible
                                </DropdownMenuItem>
                                <DropdownMenuItem @click="selectNone">
                                    Select None
                                </DropdownMenuItem>
                            </DropdownMenuContent>
                        </DropdownMenu>
                        <Checkbox
                            v-else
                            :checked="isAllSelected"
                            :indeterminate="isSomeSelected"
                            @update:checked="toggleSelectAll"
                        />
                    </TableHead>
                    <TableHead
                        v-for="column in tableColumns"
                        :key="column.key"
                        :style="column.style"
                        :class="[column.class, column.sortable ? 'cursor-pointer select-none' : '']"
                        @click="column.sortable ? handleSort(column.key) : null"
                    >
                        <div class="flex items-center gap-2">
                            {{ column.header }}
                            <template v-if="column.sortable">
                                <ArrowUpDown v-if="sortField !== column.key" class="h-4 w-4 text-muted-foreground" />
                                <ArrowUp v-else-if="sortOrder === 1" class="h-4 w-4" />
                                <ArrowDown v-else class="h-4 w-4" />
                            </template>
                        </div>
                    </TableHead>
                </TableRow>
            </TableHeader>
            <TableBody>
                <template v-if="items.length === 0">
                    <TableRow>
                        <TableCell :colspan="tableColumns.length + (hasSelection ? 1 : 0)" class="text-center py-8">
                            <slot name="empty">{{ emptyLabel }}</slot>
                        </TableCell>
                    </TableRow>
                </template>
                <template v-else>
                    <TableRow
                        v-for="(item, index) in items"
                        :key="item[dataKey] || index"
                        :class="{ 'bg-muted/50': isRowSelected(item) }"
                    >
                        <TableCell v-if="hasSelection" class="text-center">
                            <Checkbox
                                :checked="isRowSelected(item)"
                                @update:checked="toggleRowSelection(item)"
                            />
                        </TableCell>
                        <TableCell
                            v-for="column in tableColumns"
                            :key="column.key"
                            :class="column.bodyClass"
                        >
                            <slot :name="column.key" :data="item">
                                {{ item[column.key] }}
                            </slot>
                        </TableCell>
                    </TableRow>
                </template>
            </TableBody>
        </Table>
    </ScrollFrame>
</template>

<script setup lang="ts">
import { ref, computed, onMounted, inject, type Ref } from 'vue';
import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from '@/components/ui/table';
import { Checkbox } from '@/components/ui/checkbox';
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuItem,
    DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu';
import ScrollFrame from '../ScrollFrame.vue';
import { ArrowUpDown, ArrowUp, ArrowDown, ChevronDown } from 'lucide-vue-next';
import { formatNumber } from '@/utils';

interface Column {
    key: string;
    header: string;
    style?: string;
    class?: string;
    bodyClass?: string;
    sortable?: boolean;
    frozen?: boolean;
    props?: Record<string, any>;
}

const emit = defineEmits(['sort', 'update:selected', 'update:selectAll', 'update:activeColumnList']);

const props = defineProps<{
    items: any[];
    itemTotal?: number;
    selected?: any[];
    selectAll?: boolean;
    columns: Column[];
    activeColumnList: string[];
    defaultColumnList?: string[];
    sortField?: string;
    sortOrder?: number;
    size?: string;
    tableStyle?: string;
    dataKey?: string;
    emptyLabel?: string;
    hasSelectAll?: boolean;
    hasSelection?: boolean;
    hasCustomizeColumns?: boolean;
    scrollOffset?: number;
    scrollable?: boolean;
}>();

// Inject layout footer height for dynamic scroll calculation
const layoutFooterHeight = inject<Ref<number>>('layoutFooterHeight', null);

// Track scroll state for header shadow
const scrollFrameRef = ref<any>(null);
const isScrolled = ref(false);

const onScroll = (e: Event) => {
    const target = e.target as HTMLElement;
    isScrolled.value = target.scrollTop > 0;
};

// Compute total scroll offset (prop offset + footer height)
const computedScrollOffset = computed(() => {
    const baseOffset = props.scrollOffset ?? 0;
    const footerOffset = layoutFooterHeight?.value ?? 0;
    return baseOffset + footerOffset;
});

const defaultProps = {
    itemTotal: 0,
    selected: [],
    selectAll: false,
    defaultColumnList: [],
    dataKey: 'id',
    emptyLabel: 'No results',
    hasSelectAll: true,
    hasSelection: false,
    hasCustomizeColumns: false,
    scrollOffset: 0,
    scrollable: false,
};

const getVal = <T>(val: T | undefined, defaultVal: T): T => val ?? defaultVal;

const tableColumns = computed(() =>
    props.activeColumnList
        .map(key => props.columns.find(col => col.key === key))
        .filter(Boolean) as Column[]
);

const isAllSelected = computed(() => {
    if (props.selectAll) return true;
    if (!props.selected?.length) return false;
    return props.selected.length === props.items.length;
});

const isSomeSelected = computed(() => {
    if (!props.selected?.length) return false;
    return props.selected.length > 0 && props.selected.length < props.items.length;
});

const isRowSelected = (item: any) => {
    if (props.selectAll) return true;
    const key = getVal(props.dataKey, defaultProps.dataKey);
    return props.selected?.some(s => s[key] === item[key]) ?? false;
};

const toggleSelectAll = (checked: boolean) => {
    if (checked) {
        emit('update:selected', [...props.items]);
    } else {
        emit('update:selected', []);
        emit('update:selectAll', false);
    }
};

const selectAllItems = () => {
    emit('update:selectAll', true);
    emit('update:selected', []);
};

const selectVisibleItems = () => {
    emit('update:selectAll', false);
    emit('update:selected', [...props.items]);
};

const selectNone = () => {
    emit('update:selectAll', false);
    emit('update:selected', []);
};

const toggleRowSelection = (item: any) => {
    const key = getVal(props.dataKey, defaultProps.dataKey);
    const currentSelected = props.selected || [];
    const isSelected = currentSelected.some(s => s[key] === item[key]);

    if (isSelected) {
        emit('update:selected', currentSelected.filter(s => s[key] !== item[key]));
        emit('update:selectAll', false);
    } else {
        emit('update:selected', [...currentSelected, item]);
    }
};

const handleSort = (field: string) => {
    const newOrder = props.sortField === field ? (props.sortOrder === 1 ? -1 : 1) : 1;
    emit('sort', { field, order: newOrder });
};

onMounted(() => {
    if (props.activeColumnList.length === 0) {
        emit('update:activeColumnList', getVal(props.defaultColumnList, defaultProps.defaultColumnList));
    }
});
</script>
