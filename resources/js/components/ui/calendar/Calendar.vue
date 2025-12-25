<script lang="ts" setup>
import type { CalendarRootEmits, CalendarRootProps, DateValue } from "reka-ui";
import type { HTMLAttributes } from "vue";
import { ref, watch } from "vue";
import { reactiveOmit } from "@vueuse/core";
import { CalendarRoot, useForwardPropsEmits } from "reka-ui";
import { cn } from "@/utils";
import { CalendarCell, CalendarCellTrigger, CalendarGrid, CalendarGridBody, CalendarGridHead, CalendarGridRow, CalendarHeadCell, CalendarHeader, CalendarHeading, CalendarNextButton, CalendarPrevButton, CalendarQuickNav } from ".";

interface Props extends CalendarRootProps {
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

const emits = defineEmits<CalendarRootEmits>();

const delegatedProps = reactiveOmit(props, "class", "quickNavigation", "minYear", "maxYear");

const forwarded = useForwardPropsEmits(delegatedProps, emits);

// Internal placeholder for quick nav
const internalPlaceholder = ref<DateValue | undefined>(props.placeholder);

// Sync with external placeholder
watch(() => props.placeholder, (newVal) => {
    internalPlaceholder.value = newVal;
});

function onQuickNavChange(date: DateValue) {
    internalPlaceholder.value = date;
    emits('update:placeholder', date);
}
</script>

<template>
    <CalendarRoot
        v-slot="{ grid, weekDays, date }"
        :class="cn('p-3', props.class)"
        :placeholder="internalPlaceholder"
        v-bind="forwarded"
        @update:placeholder="(val) => internalPlaceholder = val"
    >
        <CalendarHeader>
            <CalendarPrevButton :class="quickNavigation ? 'absolute left-1' : ''" />
            <CalendarQuickNav
                v-if="quickNavigation"
                :date="date"
                :min-year="minYear"
                :max-year="maxYear"
                class="mx-auto"
                @update:date="onQuickNavChange"
            />
            <CalendarHeading v-else />
            <CalendarNextButton :class="quickNavigation ? 'absolute right-1' : ''" />
        </CalendarHeader>

        <div class="flex flex-col gap-y-4 mt-4 sm:flex-row sm:gap-x-4 sm:gap-y-0">
            <CalendarGrid v-for="month in grid" :key="month.value.toString()">
                <CalendarGridHead>
                    <CalendarGridRow>
                        <CalendarHeadCell
                            v-for="day in weekDays" :key="day"
                        >
                            {{ day }}
                        </CalendarHeadCell>
                    </CalendarGridRow>
                </CalendarGridHead>
                <CalendarGridBody>
                    <CalendarGridRow v-for="(weekDates, index) in month.rows" :key="`weekDate-${index}`" class="mt-2 w-full">
                        <CalendarCell
                            v-for="weekDate in weekDates"
                            :key="weekDate.toString()"
                            :date="weekDate"
                        >
                            <CalendarCellTrigger
                                :day="weekDate"
                                :month="month.value"
                            />
                        </CalendarCell>
                    </CalendarGridRow>
                </CalendarGridBody>
            </CalendarGrid>
        </div>
    </CalendarRoot>
</template>
