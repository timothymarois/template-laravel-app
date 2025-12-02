<template>
    <DataTable
        ref="el"
        unstyled
        v-bind="bindProps"
        :pt="mergedPt"
        :ptOptions="{
            mergeProps: ptViewMerge
        }"
    >
        <template #paginatorcontainer="{ page, pageCount, pageLinks, changePageCallback, firstPageCallback, lastPageCallback, prevPageCallback, nextPageCallback }">
            <div class="flex flex-wrap gap-2 items-center justify-center">
                <Button text size="small" @click="firstPageCallback" :disabled="page === 0">
                    <template #icon>
                        <AngleDoubleLeftIcon />
                    </template>
                </Button>
                <Button text size="small" @click="prevPageCallback" :disabled="page === 0">
                    <template #icon>
                        <AngleLeftIcon />
                    </template>
                </Button>
                <div class="items-center justify-center gap-2 hidden sm:flex">
                    <Button outlined v-for="pageLink of pageLinks" :key="pageLink" :text="page + 1 !== pageLink" size="small" @click="() => changePageCallback(pageLink - 1)" :class="['shrink-0 min-w-10 h-10']"
                        >{{ pageLink }}
                    </Button>
                </div>
                <Button text size="small" @click="nextPageCallback" :disabled="page === pageCount! - 1">
                    <template #icon>
                        <AngleRightIcon />
                    </template>
                </Button>
                <Button text size="small" @click="lastPageCallback" :disabled="page === pageCount! - 1">
                    <template #icon>
                        <AngleDoubleRightIcon />
                    </template>
                </Button>
            </div>
        </template>
        <template #loadingicon>
            <SpinnerIcon class="animate-spin text-[2rem] w-8 h-8" />
        </template>
        <template v-for="(_, slotName) in $slots" v-slot:[slotName]="slotProps">
            <slot :name="slotName" v-bind="slotProps ?? {}" />
        </template>
    </DataTable>
</template>

<script setup lang="ts">
import AngleDoubleLeftIcon from '@primevue/icons/angledoubleleft';
import AngleDoubleRightIcon from '@primevue/icons/angledoubleright';
import AngleLeftIcon from '@primevue/icons/angleleft';
import AngleRightIcon from '@primevue/icons/angleright';
import SpinnerIcon from '@primevue/icons/spinner';
import DataTable, { type DataTablePassThroughOptions, type DataTableProps } from 'primevue/datatable';
import { ref, useAttrs } from 'vue';
import Button from './Button.vue';
import { ptViewMerge } from '../utils';
import { usePrimeBindings } from '../composables';

interface Props extends /* @vue-ignore */ DataTableProps {}
const props = defineProps<Props>();
const attrs = useAttrs();

const theme = ref<DataTablePassThroughOptions>({
    root: `relative p-flex-scrollable:flex p-flex-scrollable:flex-col p-flex-scrollable:h-full text-sm dark:text-surface-0`,
    tableContainer: `p-scrollable:relative p-flex-scrollable:flex p-flex-scrollable:flex-col p-flex-scrollable:flex-1 p-flex-scrollable:h-full max-w-full dark:bg-surface-950`,
    header: `py-3 px-4 border-b border-surface-300 dark:border-surface-700 bg-surface-0 dark:bg-surface-900 text-surface-900 dark:text-surface-0`,
    table: `border-spacing-0 w-full border-separate`,
    thead: `p-scrollable:bg-surface-0 dark:p-scrollable:bg-surface-900 p-scrollable:top-0 p-scrollable:z-10 p-scrollable:shadow-sm`,
    tbody: `p-hoverable:*:hover:bg-surface-100 p-hoverable:*:hover:text-surface-800 dark:p-hoverable:*:hover:bg-surface-700 dark:p-hoverable:*:hover:text-surface-0 p-frozen:sticky p-frozen:z-10`,
    bodyRow: `bg-white dark:bg-surface-900 text-surface-900 dark:text-surface-0 p-selectable:cursor-pointer p-selected:!bg-yellow-100 dark:p-selected:!bg-surface-700 hover:!bg-surface-100 dark:hover:!bg-surface-700 p-selected:hover:!bg-yellow-200 dark:p-selected:hover:!bg-surface-600`,
    tfoot: `p-scrollable:bg-surface-0 dark:p-scrollable:bg-surface-900 p-scrollable:bottom-0 p-scrollable:z-10`,
    footer: `py-3 px-4 border-b border-surface-200 dark:border-surface-700 text-surface-700 dark:text-surface-0`,
    mask: `bg-black/50 dark:bg-black/70 text-surface-200 absolute z-10 flex items-center justify-center w-full h-full backdrop-blur-sm`,
    column: {
        root: ``,
        headerCell: `group py-2 px-3 font-normal text-start transition-colors duration-200 border-b border-surface-300 dark:border-surface-700 first:border-l-0 last:border-r-0 border-r bg-surface-50/50 dark:bg-surface-900 text-surface-900 dark:text-surface-0 text-xs p-sortable:cursor-pointer p-sortable:select-none p-sortable:focus-visible:outline p-sortable:focus-visible:outline-1 p-sortable:focus-visible:-outline-offset-1 p-sortable:focus-visible:outline-primary p-sortable:not-p-sorted:hover:!bg-surface-100 p-sortable:not-p-sorted:hover:text-surface-800 dark:p-sortable:not-p-sorted:hover:!bg-surface-800 dark:p-sortable:not-p-sorted:hover:text-surface-0 p-sorted:bg-surface-100 dark:p-sorted:bg-surface-800 first:pl-6 last:pr-6 p-frozen:sticky p-frozen:bg-surface-0 dark:p-frozen:bg-surface-900 p-frozen:z-10`,
        columnHeaderContent: `flex justify-between items-center w-full`,
        columnTitle: `font-semibold`,
        bodyCell: `text-start py-2 px-3 border-b first:pl-6 last:pr-6 first:border-l-0 last:border-r-0 border-r border-surface-200 dark:border-surface-800 p-frozen:sticky p-frozen:bg-inherit p-selected:!bg-inherit`,
        bodyCellContent: ``,
        footerCell: `text-start py-3 px-4 border-b border-surface-200 dark:border-surface-800 bg-surface-0 dark:bg-surface-900 text-surface-700 dark:text-surface-0 p-frozen:sticky p-frozen:bg-surface-0 dark:p-frozen:bg-surface-900`,
        columnFooter: `font-semibold`,
        columnResizer: `block absolute top-0 end-0 m-0 w-2 h-full p-0 cursor-col-resize border border-transparent`,
        sort: ``,
        sortIcon: `text-surface-500 dark:text-surface-400 transition-colors duration-200 group-p-sortable:not-group-p-sorted:group-hover:text-surface-600 dark:group-p-sortable:not-group-p-sorted:group-hover:text-surface-300 group-p-sorted:text-primary-500 dark:group-p-sorted:text-primary-400`,
        pcSortBadge: {
            root: `bg-primary dark:bg-primary text-primary-contrast rounded-full min-w-6 h-6 inline-flex items-center justify-center text-xs font-bold`
        },
        pcHeaderCheckbox: {
            root: `relative inline-flex select-none w-5 h-5 align-bottom`,
            input: `peer cursor-pointer disabled:cursor-default appearance-none absolute start-0 top-0 w-full h-full m-0 p-0 opacity-0 z-10 border border-transparent rounded-xs`,
            box: `flex justify-center items-center rounded-sm w-5 h-5 border border-surface-300 dark:border-surface-700 bg-surface-0 dark:bg-surface-950 text-surface-700 dark:text-surface-0 peer-enabled:peer-hover:border-surface-400 dark:peer-enabled:peer-hover:border-surface-600 p-checked:border-primary-500 p-checked:bg-primary-500 p-checked:text-primary-contrast dark:p-checked:bg-surface-0 dark:p-checked:border-surface-900 peer-enabled:peer-hover:p-checked:bg-primary-emphasis peer-enabled:peer-hover:p-checked:border-primary-emphasis dark:peer-enabled:peer-hover:p-checked:bg-surface-50 peer-focus-visible:outline-1 peer-focus-visible:outline-offset-2 peer-focus-visible:outline-primary peer-focus-visible:outline p-disabled:bg-surface-200 dark:p-disabled:bg-surface-400 p-disabled:border-surface-300 dark:p-disabled:border-surface-700 p-disabled:text-surface-700 dark:p-disabled:text-surface-400 shadow-[0_1px_2px_0_rgba(18,18,23,0.05)] transition-colors duration-200`,
            icon: `text-sm w-[1rem] h-[1rem] transition-none text-white dark:text-black`
        },
        pcRowCheckbox: {
            root: `relative inline-flex select-none w-5 h-5 align-bottom`,
            input: `peer cursor-pointer disabled:cursor-default appearance-none absolute start-0 top-0 w-full h-full m-0 p-0 opacity-0 z-10 border border-transparent rounded-xs`,
            box: `flex justify-center items-center rounded-sm w-5 h-5 border border-surface-300 dark:border-surface-700 bg-surface-0 dark:bg-surface-950 text-surface-700 dark:text-surface-0 peer-enabled:peer-hover:border-surface-400 dark:peer-enabled:peer-hover:border-surface-600 p-checked:border-primary-500 p-checked:bg-primary-500 p-checked:text-primary-contrast dark:p-checked:bg-surface-0 dark:p-checked:border-surface-900 peer-enabled:peer-hover:p-checked:bg-primary-emphasis peer-enabled:peer-hover:p-checked:border-primary-emphasis dark:peer-enabled:peer-hover:p-checked:bg-surface-50 peer-focus-visible:outline-1 peer-focus-visible:outline-offset-2 peer-focus-visible:outline-primary peer-focus-visible:outline p-disabled:bg-surface-200 dark:p-disabled:bg-surface-400 p-disabled:border-surface-300 dark:p-disabled:border-surface-700 p-disabled:text-surface-700 dark:p-disabled:text-surface-400 shadow-[0_1px_2px_0_rgba(18,18,23,0.05)] transition-colors duration-200`,
            icon: `text-sm w-[1rem] h-[1rem] transition-none text-white dark:text-black`
        }
    },
    loadingIcon: ``,
    pcPaginator: {
        paginatorContainer: `p-bottom:border-t border-surface-200 dark:border-surface-700`,
        root: `flex items-center justify-center flex-wrap py-2 px-4 rounded-[var(--p-content-border-radius)] gap-1 bg-surface-0 dark:bg-surface-900 text-surface-700 dark:text-surface-0`
    },
    columnResizeIndicator: `w-px absolute z-10 hidden bg-primary dark:bg-primary`,
    rowReorderIndicatorUp: `absolute hidden`,
    rowReorderIndicatorDown: `absolute hidden`
});

const { bindProps, mergedPt } = usePrimeBindings(props, attrs, theme);


const el = ref();
defineExpose({
    exportCSV: () => el.value.exportCSV()
});
</script>
