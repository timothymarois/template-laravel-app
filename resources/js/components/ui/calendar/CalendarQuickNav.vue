<script lang="ts" setup>
import { computed } from 'vue';
import type { DateValue } from 'reka-ui';
import { Select } from '@/components/ui/select-popover';

interface Props {
    /** Current placeholder date from calendar */
    date: DateValue;
    /** Minimum year to show in dropdown */
    minYear?: number;
    /** Maximum year to show in dropdown */
    maxYear?: number;
}

const props = withDefaults(defineProps<Props>(), {
    minYear: () => new Date().getFullYear() - 100,
    maxYear: () => new Date().getFullYear() + 50,
});

const emit = defineEmits<{
    'update:date': [value: DateValue];
}>();

const months = [
    { value: '1', label: 'January' },
    { value: '2', label: 'February' },
    { value: '3', label: 'March' },
    { value: '4', label: 'April' },
    { value: '5', label: 'May' },
    { value: '6', label: 'June' },
    { value: '7', label: 'July' },
    { value: '8', label: 'August' },
    { value: '9', label: 'September' },
    { value: '10', label: 'October' },
    { value: '11', label: 'November' },
    { value: '12', label: 'December' },
];

const years = computed(() => {
    const result: { value: string; label: string }[] = [];
    for (let year = props.maxYear; year >= props.minYear; year--) {
        result.push({ value: String(year), label: String(year) });
    }
    return result;
});

const currentMonth = computed(() => String(props.date.month));
const currentYear = computed(() => String(props.date.year));

function onMonthChange(value: string) {
    const newMonth = parseInt(value, 10);
    const newDate = props.date.set({ month: newMonth });
    emit('update:date', newDate);
}

function onYearChange(value: any) {
    if (value === null || value === undefined) return;
    const newYear = parseInt(String(value), 10);
    const newDate = props.date.set({ year: newYear });
    emit('update:date', newDate);
}
</script>

<template>
    <div class="flex items-center gap-1">
        <!-- Month Select -->
        <Select
            :model-value="currentMonth"
            :options="months"
            :clearable="false"
            placeholder=""
            class="h-7 w-auto gap-1 border-0 bg-transparent px-2 py-0 text-sm font-medium opacity-50 hover:opacity-100 focus:opacity-100 hover:bg-accent"
            @update:model-value="onMonthChange"
        />

        <!-- Year Searchable Select -->
        <Select
            :model-value="currentYear"
            :options="years"
            :clearable="false"
            :searchable="true"
            placeholder=""
            search-placeholder="Search year..."
            class="h-7 w-auto gap-1 border-0 bg-transparent px-2 py-0 text-sm font-medium opacity-50 hover:opacity-100 focus:opacity-100 hover:bg-accent"
            content-class="w-[140px]"
            @update:model-value="onYearChange"
        />
    </div>
</template>
