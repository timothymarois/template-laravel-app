<template>
    <ScrollFrame ref="scrollFrameRef" rootClass="overflow-y-auto overflow-x-auto relative" :addOffset="computedScrollOffset" :scrollable="scrollable" @scroll="onScroll">
        <Table>
            <TableHeader :class="{ 'shadow-[0_1px_3px_rgba(0,0,0,0.1)]': isScrolled }">
                <TableRow>
                    <TableHead v-if="hasSelection" class="w-12 text-center">
                        <DropdownMenu v-if="hasSelectAll">
                            <DropdownMenuTrigger as-child>
                                <button class="inline-flex items-center justify-center cursor-pointer rounded-md p-1.5 hover:bg-foreground/10 transition-colors">
                                    <ChevronDown class="h-4 w-4 text-muted-foreground" />
                                </button>
                            </DropdownMenuTrigger>
                            <DropdownMenuContent align="start">
                                <DropdownMenuItem @click="selectAllItems">
                                    Select All ({{ formatNumber(props.itemTotal ?? 0) }})
                                </DropdownMenuItem>
                                <DropdownMenuItem @click="selectVisibleItems">
                                    Select Visible ({{ props.items.length }})
                                </DropdownMenuItem>
                                <DropdownMenuItem @click="selectNone">
                                    Select None
                                </DropdownMenuItem>
                            </DropdownMenuContent>
                        </DropdownMenu>
                        <Checkbox
                            v-else
                            :modelValue="isSomeSelected ? 'indeterminate' : isAllSelected"
                            @update:modelValue="toggleSelectAll"
                        />
                    </TableHead>
                    <TableHead
                        v-for="column in tableColumns"
                        :key="column.key"
                        :style="column.style"
                        :class="[
                            column.class,
                            column.sortable ? 'cursor-pointer select-none hover:bg-foreground/5' : '',
                            sortField === column.key ? 'bg-foreground/5' : ''
                        ]"
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
                        :class="isRowSelected(item) ? 'bg-yellow-50 dark:bg-yellow-950/30 hover:bg-yellow-100 dark:hover:bg-yellow-900/40' : 'hover:bg-muted/50'"
                    >
                        <TableCell v-if="hasSelection" class="w-12">
                            <div class="flex items-center justify-center">
                                <Checkbox
                                    :modelValue="isRowSelected(item)"
                                    @update:modelValue="toggleRowSelection(item)"
                                />
                            </div>
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
import { ref, computed, onMounted, nextTick, inject, type Ref } from 'vue';
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
import ScrollFrame from '../layout/ScrollFrame.vue';
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

interface Props {
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
}

const props = withDefaults(defineProps<Props>(), {
    itemTotal: 0,
    selected: () => [],
    selectAll: false,
    defaultColumnList: () => [],
    dataKey: 'id',
    emptyLabel: 'No results',
    hasSelectAll: true,
    hasSelection: false,
    hasCustomizeColumns: false,
    scrollOffset: 0,
    scrollable: false,
});

// Inject layout footer height for dynamic scroll calculation
const layoutFooterHeight = inject<Ref<number>>('layoutFooterHeight', null);

// Track scroll state for header shadow and footer shadow
const scrollFrameRef = ref<any>(null);
const isScrolled = ref(false);

// Inject setter from layout to update scroll-to-bottom state
const setScrolledToBottom = inject<(value: boolean) => void>('setScrolledToBottom', null);

const checkScrollPosition = (target: HTMLElement) => {
    isScrolled.value = target.scrollTop > 0;
    // Check if scrolled to bottom (within 1px tolerance)
    const atBottom = target.scrollHeight - target.scrollTop - target.clientHeight < 1;
    setScrolledToBottom?.(atBottom);
};

const onScroll = (e: Event) => {
    checkScrollPosition(e.target as HTMLElement);
};

// Compute total scroll offset (prop offset + footer height)
const computedScrollOffset = computed(() => {
    const baseOffset = props.scrollOffset ?? 0;
    const footerOffset = layoutFooterHeight?.value ?? 0;
    return baseOffset + footerOffset;
});

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

const isRowSelected = (item: any): boolean => {
    if (props.selectAll) return true;
    if (!props.selected || !Array.isArray(props.selected) || props.selected.length === 0) return false;
    const key = props.dataKey!;
    return props.selected.some(s => s[key] === item[key]);
};

const toggleSelectAll = (value: boolean | 'indeterminate') => {
    if (value === true) {
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
    const key = props.dataKey!;
    const currentSelected = Array.isArray(props.selected) ? props.selected : [];
    const isSelected = currentSelected.some(s => s[key] === item[key]);

    if (isSelected) {
        const newSelected = currentSelected.filter(s => s[key] !== item[key]);
        emit('update:selected', newSelected);
        emit('update:selectAll', false);
    } else {
        const newSelected = [...currentSelected, item];
        emit('update:selected', newSelected);
    }
};

const handleSort = (field: string) => {
    const newOrder = props.sortField === field ? (props.sortOrder === 1 ? -1 : 1) : 1;
    emit('sort', { field, order: newOrder });
};

onMounted(() => {
    if (props.activeColumnList.length === 0) {
        emit('update:activeColumnList', props.defaultColumnList);
    }
    // Check initial scroll position after render
    nextTick(() => {
        const frame = scrollFrameRef.value?.$el || scrollFrameRef.value;
        if (frame) {
            checkScrollPosition(frame);
        }
    });
});
</script>
