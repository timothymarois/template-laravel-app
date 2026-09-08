<template>
    <AppLayout
        title="Components - Calendar"
        pageTitle="Calendar"
        :pageTabs="pageTabs"
        :pageSidebarItems="sidebarItems"
        pageSidebarTitle="Components"
    >
        <div class="space-y-4">
            <!-- Basic States -->
            <Card>
                <CardHeader>
                    <CardTitle>Basic States</CardTitle>
                    <CardDescription>DateRangePicker component with various states</CardDescription>
                </CardHeader>
                <CardContent>
                    <div class="grid grid-cols-4 gap-4">
                        <div>
                            <div class="text-xs text-muted-foreground mb-1">Default</div>
                            <DateRangePicker v-model="rangePickerEmpty" fluid />
                        </div>
                        <div>
                            <div class="text-xs text-muted-foreground mb-1">Clearable</div>
                            <DateRangePicker v-model="rangePickerClearable" clearable fluid />
                        </div>
                        <div>
                            <div class="text-xs text-muted-foreground mb-1">With Value</div>
                            <DateRangePicker v-model="rangePickerValue" clearable fluid />
                        </div>
                        <div>
                            <div class="text-xs text-muted-foreground mb-1">Invalid</div>
                            <DateRangePicker v-model="rangePickerInvalid" invalid fluid />
                        </div>
                        <div>
                            <div class="text-xs text-muted-foreground mb-1">Disabled</div>
                            <DateRangePicker v-model="rangePickerDisabled" disabled fluid />
                        </div>
                        <div>
                            <div class="text-xs text-muted-foreground mb-1">Single Month</div>
                            <DateRangePicker v-model="rangePickerSingleMonth" :numberOfMonths="1" fluid />
                        </div>
                    </div>
                </CardContent>
            </Card>

            <!-- Inline Range Calendar -->
            <Card>
                <CardHeader>
                    <CardTitle>Inline Range Calendar</CardTitle>
                    <CardDescription>Range calendar displayed inline without popover</CardDescription>
                </CardHeader>
                <CardContent>
                    <div class="flex gap-8">
                        <div>
                            <div class="text-xs text-muted-foreground mb-2">Two Months (Default)</div>
                            <div class="border rounded-md w-fit">
                                <RangeCalendar v-model="dateRange" :numberOfMonths="2" />
                            </div>
                            <div class="text-sm text-muted-foreground mt-2">
                                <template v-if="dateRange?.start && dateRange?.end">
                                    Selected: {{ formatDate(dateRange.start) }} - {{ formatDate(dateRange.end) }}
                                </template>
                                <template v-else-if="dateRange?.start">
                                    Start: {{ formatDate(dateRange.start) }} (select end date)
                                </template>
                                <template v-else>
                                    No range selected
                                </template>
                            </div>
                        </div>
                        <div>
                            <div class="text-xs text-muted-foreground mb-2">Single Month</div>
                            <div class="border rounded-md w-fit">
                                <RangeCalendar v-model="dateRangeSingle" :numberOfMonths="1" />
                            </div>
                        </div>
                    </div>
                </CardContent>
            </Card>

            <!-- Quick Navigation -->
            <Card>
                <CardHeader>
                    <CardTitle>Quick Navigation</CardTitle>
                    <CardDescription>Month/year dropdowns for quickly jumping to distant dates (year is searchable)</CardDescription>
                </CardHeader>
                <CardContent class="space-y-6">
                    <div>
                        <div class="text-xs text-muted-foreground mb-2">Range Calendar with Quick Navigation</div>
                        <div class="border rounded-md w-fit">
                            <RangeCalendar v-model="quickNavRangeInline" :numberOfMonths="2" quick-navigation />
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <div class="text-xs text-muted-foreground mb-1">DateRangePicker with Quick Nav</div>
                            <DateRangePicker v-model="quickNavRange" quick-navigation fluid />
                        </div>
                        <div>
                            <div class="text-xs text-muted-foreground mb-1">With Custom Year Range</div>
                            <DateRangePicker
                                v-model="quickNavRangeCustomYear"
                                quick-navigation
                                :min-year="2020"
                                :max-year="2030"
                                fluid
                            />
                        </div>
                    </div>
                </CardContent>
            </Card>

            <!-- Disabled Dates -->
            <Card>
                <CardHeader>
                    <CardTitle>Disabled Dates</CardTitle>
                    <CardDescription>Custom rules to disable specific dates using isDateDisabled prop</CardDescription>
                </CardHeader>
                <CardContent class="space-y-6">
                    <div class="flex gap-8">
                        <div>
                            <div class="text-xs text-muted-foreground mb-2">No Weekends</div>
                            <div class="border rounded-md w-fit">
                                <RangeCalendar v-model="rangeNoWeekends" :numberOfMonths="2" :isDateDisabled="isWeekend" />
                            </div>
                        </div>
                        <div>
                            <div class="text-xs text-muted-foreground mb-2">No Past Dates</div>
                            <div class="border rounded-md w-fit">
                                <RangeCalendar v-model="rangeNoPast" :numberOfMonths="2" :isDateDisabled="isPastDate" />
                            </div>
                        </div>
                    </div>
                    <div>
                        <div class="text-sm font-medium mb-3">Pickers with Disabled Dates</div>
                        <div class="grid grid-cols-3 gap-4">
                            <div>
                                <div class="text-xs text-muted-foreground mb-1">No Weekends</div>
                                <DateRangePicker v-model="rangePickerNoWeekends" :isDateDisabled="isWeekend" clearable fluid />
                            </div>
                            <div>
                                <div class="text-xs text-muted-foreground mb-1">No Past Dates</div>
                                <DateRangePicker v-model="rangePickerNoPast" :isDateDisabled="isPastDate" clearable fluid />
                            </div>
                            <div>
                                <div class="text-xs text-muted-foreground mb-1">Weekdays Only (No Past)</div>
                                <DateRangePicker v-model="rangePickerWeekdaysFuture" :isDateDisabled="isWeekendOrPast" clearable fluid />
                            </div>
                        </div>
                    </div>
                </CardContent>
            </Card>

            <!-- Presets -->
            <Card>
                <CardHeader>
                    <CardTitle>Presets</CardTitle>
                    <CardDescription>Quick selection options for common date ranges</CardDescription>
                </CardHeader>
                <CardContent class="space-y-6">
                    <div class="grid grid-cols-2 gap-6">
                        <div>
                            <div class="text-xs text-muted-foreground mb-1">With Default Presets</div>
                            <DateRangePicker v-model="rangeWithPresets" :presets="true" fluid />
                            <div class="text-xs text-muted-foreground mt-2">
                                <code class="bg-muted px-1 py-0.5 rounded">:presets="true"</code> - uses built-in presets
                            </div>
                        </div>
                        <div>
                            <div class="text-xs text-muted-foreground mb-1">Custom Presets</div>
                            <DateRangePicker v-model="rangeCustomPresets" :presets="customPresets" fluid />
                            <div class="text-xs text-muted-foreground mt-2">
                                <code class="bg-muted px-1 py-0.5 rounded">:presets="[...]"</code> - custom preset array
                            </div>
                        </div>
                    </div>

                    <div class="p-4 bg-muted rounded-lg">
                        <div class="text-xs font-medium mb-2">Custom Presets Example</div>
                        <pre class="text-xs overflow-x-auto"><code>import { today, getLocalTimeZone } from '@internationalized/date';
import type { DateRangePreset } from '@/components/ui/date-picker';

const customPresets: DateRangePreset[] = [
    {
        label: 'This Week',
        getValue: () => {
            const t = today(getLocalTimeZone());
            const dayOfWeek = t.toDate(getLocalTimeZone()).getDay();
            return { start: t.subtract({ days: dayOfWeek }), end: t };
        },
    },
    {
        label: 'Last Quarter',
        getValue: () => {
            const t = today(getLocalTimeZone());
            return { start: t.subtract({ months: 3 }), end: t };
        },
    },
];</code></pre>
                    </div>
                </CardContent>
            </Card>

            <!-- Confirm Mode -->
            <Card>
                <CardHeader>
                    <CardTitle>Confirm Mode</CardTitle>
                    <CardDescription>Requires explicit Apply/Cancel instead of auto-close on selection</CardDescription>
                </CardHeader>
                <CardContent>
                    <div class="grid grid-cols-3 gap-6">
                        <div>
                            <div class="text-xs text-muted-foreground mb-1">Basic Confirm Mode</div>
                            <DateRangePicker v-model="rangeConfirmBasic" confirm-mode fluid />
                            <div class="text-xs text-muted-foreground mt-2">
                                <code class="bg-muted px-1 py-0.5 rounded">confirm-mode</code>
                            </div>
                        </div>
                        <div>
                            <div class="text-xs text-muted-foreground mb-1">With Presets</div>
                            <DateRangePicker v-model="rangeConfirm" :presets="true" confirm-mode fluid />
                        </div>
                        <div>
                            <div class="text-xs text-muted-foreground mb-1">Full Featured</div>
                            <DateRangePicker
                                v-model="rangeFull"
                                :presets="true"
                                confirm-mode
                                clearable
                                quick-navigation
                                fluid
                            />
                            <div class="text-xs text-muted-foreground mt-2">
                                All options enabled
                            </div>
                        </div>
                    </div>
                </CardContent>
            </Card>
        </div>
    </AppLayout>
</template>

<script setup lang="ts">
import { ref, shallowRef } from 'vue';
import type { DateValue, DateRange } from 'reka-ui';
import AppLayout from '@/components/app/layout/AppLayout.vue';
import { Card, CardHeader, CardTitle, CardDescription, CardContent } from '@/components/ui/card';
import { RangeCalendar } from '@/components/ui/range-calendar';
import { DateRangePicker, type DateRangePreset } from '@/components/ui/date-picker';
import { useShowcaseNav } from '../../_composables/useShowcaseNav';
import { today, getLocalTimeZone, getDayOfWeek } from '@internationalized/date';

const { sidebarItems } = useShowcaseNav();

const pageTabs = [
    { href: '/admin/components/forms/calendar/date-input', title: 'Date Input' },
    { href: '/admin/components/forms/calendar/date-range-input', title: 'Date Range Input' },
];

// DateRangePicker examples
const rangePickerEmpty = shallowRef<DateRange>();
const rangePickerClearable = shallowRef<DateRange>();
const rangePickerValue = shallowRef<DateRange>({
    start: today(getLocalTimeZone()),
    end: today(getLocalTimeZone()).add({ days: 7 }),
});
const rangePickerInvalid = shallowRef<DateRange>();
const rangePickerDisabled = shallowRef<DateRange>(rangePickerValue.value);
const rangePickerSingleMonth = shallowRef<DateRange>();

// Inline range calendar
const dateRange = shallowRef<DateRange>();
const dateRangeSingle = shallowRef<DateRange>();

// Quick navigation
const quickNavRange = shallowRef<DateRange>();
const quickNavRangeInline = shallowRef<DateRange>();
const quickNavRangeCustomYear = shallowRef<DateRange>();

// Disabled dates
const rangeNoWeekends = shallowRef<DateRange>();
const rangeNoPast = shallowRef<DateRange>();
const rangePickerNoWeekends = shallowRef<DateRange>();
const rangePickerNoPast = shallowRef<DateRange>();
const rangePickerWeekdaysFuture = shallowRef<DateRange>();

// Presets
const rangeWithPresets = shallowRef<DateRange>();
const rangeCustomPresets = shallowRef<DateRange>();

// Confirm mode
const rangeConfirmBasic = shallowRef<DateRange>();
const rangeConfirm = shallowRef<DateRange>();
const rangeFull = shallowRef<DateRange>();

// Custom presets
const customPresets: DateRangePreset[] = [
    {
        label: 'This Week',
        getValue: () => {
            const t = today(getLocalTimeZone());
            const dayOfWeek = t.toDate(getLocalTimeZone()).getDay();
            return { start: t.subtract({ days: dayOfWeek }), end: t };
        },
    },
    {
        label: 'Last Quarter',
        getValue: () => {
            const t = today(getLocalTimeZone());
            return { start: t.subtract({ months: 3 }), end: t };
        },
    },
    {
        label: 'Last Year',
        getValue: () => {
            const t = today(getLocalTimeZone());
            return {
                start: t.subtract({ years: 1 }).set({ month: 1, day: 1 }),
                end: t.subtract({ years: 1 }).set({ month: 12, day: 31 }),
            };
        },
    },
];

// Disable functions
const todayDate = today(getLocalTimeZone());

function isWeekend(date: DateValue): boolean {
    const dayOfWeek = getDayOfWeek(date, 'en-US');
    return dayOfWeek === 0 || dayOfWeek === 6;
}

function isPastDate(date: DateValue): boolean {
    return date.compare(todayDate) < 0;
}

function isWeekendOrPast(date: DateValue): boolean {
    return isWeekend(date) || isPastDate(date);
}

function formatDate(date: DateValue) {
    const month = String(date.month).padStart(2, '0');
    const day = String(date.day).padStart(2, '0');
    return `${month}/${day}/${date.year}`;
}
</script>
