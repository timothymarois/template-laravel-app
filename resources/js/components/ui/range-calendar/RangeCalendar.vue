<script lang="ts" setup>
import type { RangeCalendarRootEmits, RangeCalendarRootProps, DateValue } from "reka-ui";
import type { HTMLAttributes } from "vue";
import { ref, watch, computed } from "vue";
import { reactiveOmit } from "@vueuse/core";
import { RangeCalendarRoot, useForwardPropsEmits } from "reka-ui";
import { cn } from "@/utils";
import { Select } from "@/components/ui/select-popover";
import {
    RangeCalendarCell,
    RangeCalendarCellTrigger,
    RangeCalendarGrid,
    RangeCalendarGridBody,
    RangeCalendarGridHead,
    RangeCalendarGridRow,
    RangeCalendarHeadCell,
    RangeCalendarHeader,
    RangeCalendarHeading,
    RangeCalendarNextButton,
    RangeCalendarPrevButton,
} from ".";

const monthNames = [
    'January', 'February', 'March', 'April', 'May', 'June',
    'July', 'August', 'September', 'October', 'November', 'December'
];

const monthOptions = monthNames.map((label, index) => ({
    value: String(index + 1),
    label
}));

interface Props extends RangeCalendarRootProps {
    class?: HTMLAttributes["class"];
    /** Enable month/year dropdown navigation for quick jumping to dates */
    quickNavigation?: boolean;
    /** Minimum year for quick navigation dropdown */
    minYear?: number;
    /** Maximum year for quick navigation dropdown */
    maxYear?: number;
}

const props = withDefaults(defineProps<Props>(), {
    quickNavigation: false,
});

const emits = defineEmits<RangeCalendarRootEmits>();

const delegatedProps = reactiveOmit(props, "class", "quickNavigation", "minYear", "maxYear");

const forwarded = useForwardPropsEmits(delegatedProps, emits);

// Internal placeholder for quick nav
const internalPlaceholder = ref<DateValue | undefined>(props.placeholder);

// Sync with external placeholder
watch(() => props.placeholder, (newVal) => {
    internalPlaceholder.value = newVal;
});

// Generate year options based on props
const yearOptions = computed(() => {
    const min = props.minYear ?? new Date().getFullYear() - 100;
    const max = props.maxYear ?? new Date().getFullYear() + 50;
    const result: { value: string; label: string }[] = [];
    for (let year = max; year >= min; year--) {
        result.push({ value: String(year), label: String(year) });
    }
    return result;
});

// Format month heading from DateValue
function formatMonthHeading(date: DateValue): string {
    return `${monthNames[date.month - 1]} ${date.year}`;
}

// Handle month change for a specific grid position
// gridIndex: 0 = left calendar, 1 = right calendar, etc.
function onGridMonthChange(gridDate: DateValue, gridIndex: number, newMonth: string) {
    // Set the grid to show the selected month by adjusting placeholder
    // placeholder shows at grid[0], so subtract gridIndex months
    const targetDate = gridDate.set({ month: parseInt(newMonth) });
    const newPlaceholder = targetDate.subtract({ months: gridIndex });
    internalPlaceholder.value = newPlaceholder;
    emits('update:placeholder', newPlaceholder);
}

// Handle year change for a specific grid position
function onGridYearChange(gridDate: DateValue, gridIndex: number, newYear: string) {
    // Set the grid to show the selected year by adjusting placeholder
    const targetDate = gridDate.set({ year: parseInt(newYear) });
    const newPlaceholder = targetDate.subtract({ months: gridIndex });
    internalPlaceholder.value = newPlaceholder;
    emits('update:placeholder', newPlaceholder);
}
</script>

<template>
    <RangeCalendarRoot
        v-slot="{ grid, weekDays, date }"
        :class="cn('p-3', props.class)"
        :placeholder="internalPlaceholder"
        v-bind="forwarded"
        @update:placeholder="(val) => internalPlaceholder = val"
    >
        <!-- Header with prev button, month headings, and next button -->
        <RangeCalendarHeader class="justify-between">
            <RangeCalendarPrevButton />
            <div class="flex flex-1 items-center justify-center">
                <template v-for="(month, index) in grid" :key="month.value.toString()">
                    <div class="flex items-center justify-center gap-1">
                        <template v-if="quickNavigation">
                            <Select
                                :model-value="String(month.value.month)"
                                :options="monthOptions"
                                :clearable="false"
                                placeholder=""
                                class="h-7 w-auto gap-1 border-0 bg-transparent px-2 py-0 text-sm font-medium opacity-80 hover:opacity-100 focus:opacity-100 hover:bg-accent"
                                @update:model-value="(val) => onGridMonthChange(month.value, index, val)"
                            />
                            <Select
                                :model-value="String(month.value.year)"
                                :options="yearOptions"
                                :clearable="false"
                                :searchable="true"
                                placeholder=""
                                search-placeholder="Search year..."
                                class="h-7 w-auto gap-1 border-0 bg-transparent px-2 py-0 text-sm font-medium opacity-80 hover:opacity-100 focus:opacity-100 hover:bg-accent"
                                content-class="w-[140px]"
                                @update:model-value="(val) => onGridYearChange(month.value, index, val)"
                            />
                        </template>
                        <span v-else class="text-sm font-medium">
                            {{ formatMonthHeading(month.value) }}
                        </span>
                    </div>
                    <span v-if="index < grid.length - 1" class="mx-4 text-muted-foreground">–</span>
                </template>
            </div>
            <RangeCalendarNextButton />
        </RangeCalendarHeader>

        <div class="flex flex-col gap-y-4 mt-4 sm:flex-row sm:gap-x-4 sm:gap-y-0">
            <RangeCalendarGrid v-for="month in grid" :key="month.value.toString()">
                <RangeCalendarGridHead>
                    <RangeCalendarGridRow>
                        <RangeCalendarHeadCell
                            v-for="day in weekDays" :key="day"
                        >
                            {{ day }}
                        </RangeCalendarHeadCell>
                    </RangeCalendarGridRow>
                </RangeCalendarGridHead>
                <RangeCalendarGridBody>
                    <RangeCalendarGridRow v-for="(weekDates, index) in month.rows" :key="`weekDate-${index}`" class="mt-2 w-full">
                        <RangeCalendarCell
                            v-for="weekDate in weekDates"
                            :key="weekDate.toString()"
                            :date="weekDate"
                        >
                            <RangeCalendarCellTrigger
                                :day="weekDate"
                                :month="month.value"
                            />
                        </RangeCalendarCell>
                    </RangeCalendarGridRow>
                </RangeCalendarGridBody>
            </RangeCalendarGrid>
        </div>
    </RangeCalendarRoot>
</template>
