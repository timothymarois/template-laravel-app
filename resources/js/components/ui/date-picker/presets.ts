import { today, getLocalTimeZone } from '@internationalized/date';
import type { DateRange } from 'reka-ui';

/**
 * Interface for date range presets.
 * Presets are function-based to ensure dates are always calculated relative to the current date.
 */
export interface DateRangePreset {
    /** Display label for the preset */
    label: string;
    /** Function that returns the date range - called when preset is selected */
    getValue: () => DateRange;
}

/**
 * Helper to get a CalendarDate for N days ago from today.
 */
function daysAgo(days: number) {
    return today(getLocalTimeZone()).subtract({ days });
}

/**
 * Default preset options for common date range selections.
 * These can be used directly or as a reference for creating custom presets.
 *
 * @example
 * ```vue
 * <!-- Use default presets -->
 * <DateRangePicker v-model="range" :presets="true" />
 *
 * <!-- Or import and extend -->
 * import { defaultPresets } from '@/components/ui/date-picker';
 * const myPresets = [...defaultPresets, { label: 'Custom', getValue: () => {...} }];
 * ```
 */
export const defaultPresets: DateRangePreset[] = [
    {
        label: 'Today',
        getValue: () => {
            const t = today(getLocalTimeZone());
            return { start: t, end: t };
        },
    },
    {
        label: 'Yesterday',
        getValue: () => {
            const y = daysAgo(1);
            return { start: y, end: y };
        },
    },
    {
        label: 'Last 7 days',
        getValue: () => ({
            start: daysAgo(6),
            end: today(getLocalTimeZone()),
        }),
    },
    {
        label: 'Last 30 days',
        getValue: () => ({
            start: daysAgo(29),
            end: today(getLocalTimeZone()),
        }),
    },
    {
        label: 'Last 90 days',
        getValue: () => ({
            start: daysAgo(89),
            end: today(getLocalTimeZone()),
        }),
    },
    {
        label: 'This month',
        getValue: () => {
            const t = today(getLocalTimeZone());
            return {
                start: t.set({ day: 1 }),
                end: t,
            };
        },
    },
    {
        label: 'Last month',
        getValue: () => {
            const t = today(getLocalTimeZone());
            const lastMonth = t.subtract({ months: 1 });
            const daysInLastMonth = lastMonth.calendar.getDaysInMonth(lastMonth);
            return {
                start: lastMonth.set({ day: 1 }),
                end: lastMonth.set({ day: daysInLastMonth }),
            };
        },
    },
    {
        label: 'Year to date',
        getValue: () => {
            const t = today(getLocalTimeZone());
            return {
                start: t.set({ month: 1, day: 1 }),
                end: t,
            };
        },
    },
];
