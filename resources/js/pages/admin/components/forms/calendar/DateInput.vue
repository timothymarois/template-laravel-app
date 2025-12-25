<template>
    <LayoutApp
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
                    <CardDescription>DateInput and DatePicker components with various states</CardDescription>
                </CardHeader>
                <CardContent class="space-y-6">
                    <div>
                        <div class="text-sm font-medium mb-3">DateInput</div>
                        <div class="grid grid-cols-5 gap-4">
                            <div>
                                <div class="text-xs text-muted-foreground mb-1">Default</div>
                                <DateInput v-model="dateInputEmpty" />
                            </div>
                            <div>
                                <div class="text-xs text-muted-foreground mb-1">Clearable</div>
                                <DateInput v-model="dateInputClearable" clearable />
                            </div>
                            <div>
                                <div class="text-xs text-muted-foreground mb-1">With Value</div>
                                <DateInput v-model="dateInputValue" clearable />
                            </div>
                            <div>
                                <div class="text-xs text-muted-foreground mb-1">Invalid</div>
                                <DateInput v-model="dateInputInvalid" invalid :showError="false" />
                            </div>
                            <div>
                                <div class="text-xs text-muted-foreground mb-1">Disabled</div>
                                <DateInput v-model="dateInputDisabled" disabled />
                            </div>
                        </div>
                    </div>
                    <div>
                        <div class="text-sm font-medium mb-3">DatePicker</div>
                        <div class="grid grid-cols-5 gap-4">
                            <div>
                                <div class="text-xs text-muted-foreground mb-1">Default</div>
                                <DatePicker v-model="datePickerEmpty" fluid />
                            </div>
                            <div>
                                <div class="text-xs text-muted-foreground mb-1">Clearable</div>
                                <DatePicker v-model="datePickerClearable" clearable fluid />
                            </div>
                            <div>
                                <div class="text-xs text-muted-foreground mb-1">With Value</div>
                                <DatePicker v-model="datePickerValue" clearable fluid />
                            </div>
                            <div>
                                <div class="text-xs text-muted-foreground mb-1">Invalid</div>
                                <DatePicker v-model="datePickerInvalid" invalid fluid />
                            </div>
                            <div>
                                <div class="text-xs text-muted-foreground mb-1">Disabled</div>
                                <DatePicker v-model="datePickerDisabled" disabled fluid />
                            </div>
                        </div>
                    </div>
                </CardContent>
            </Card>

            <!-- Inline Calendar -->
            <Card>
                <CardHeader>
                    <CardTitle>Inline Calendar</CardTitle>
                    <CardDescription>Calendar displayed inline without popover</CardDescription>
                </CardHeader>
                <CardContent>
                    <div class="flex gap-8">
                        <div>
                            <div class="text-xs text-muted-foreground mb-2">Single Month</div>
                            <div class="border rounded-md w-fit">
                                <Calendar v-model="calendarInline" />
                            </div>
                            <div class="text-sm text-muted-foreground mt-2">
                                Selected: {{ calendarInline ? formatDate(calendarInline) : 'None' }}
                            </div>
                        </div>
                        <div>
                            <div class="text-xs text-muted-foreground mb-2">Multiple Months</div>
                            <div class="border rounded-md w-fit">
                                <Calendar v-model="calendarMultiMonth" :numberOfMonths="2" />
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
                    <div class="flex gap-8">
                        <div>
                            <div class="text-xs text-muted-foreground mb-2">With Quick Navigation</div>
                            <div class="border rounded-md w-fit">
                                <Calendar v-model="quickNavDate" quick-navigation />
                            </div>
                            <div class="text-xs text-muted-foreground mt-2">
                                <code class="bg-muted px-1 py-0.5 rounded">quick-navigation</code>
                            </div>
                        </div>
                        <div>
                            <div class="text-xs text-muted-foreground mb-2">Custom Year Range (Birth Date)</div>
                            <div class="border rounded-md w-fit">
                                <Calendar
                                    v-model="birthDate"
                                    quick-navigation
                                    :min-year="1920"
                                    :max-year="currentYear"
                                    :isDateDisabled="isFutureDate"
                                />
                            </div>
                            <div class="text-xs text-muted-foreground mt-2">
                                <code class="bg-muted px-1 py-0.5 rounded">:min-year="1920" :max-year="2025"</code>
                            </div>
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <div class="text-xs text-muted-foreground mb-1">DatePicker with Quick Nav</div>
                            <DatePicker v-model="quickNavPicker" quick-navigation fluid />
                        </div>
                        <div>
                            <div class="text-xs text-muted-foreground mb-1">DateInput with Quick Nav</div>
                            <DateInput v-model="quickNavInput" quick-navigation />
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
                    <div class="grid grid-cols-3 gap-8">
                        <div>
                            <div class="text-xs text-muted-foreground mb-2">No Weekends</div>
                            <div class="border rounded-md w-fit">
                                <Calendar v-model="noWeekendsDate" :isDateDisabled="isWeekend" />
                            </div>
                        </div>
                        <div>
                            <div class="text-xs text-muted-foreground mb-2">No Past Dates</div>
                            <div class="border rounded-md w-fit">
                                <Calendar v-model="noPastDate" :isDateDisabled="isPastDate" />
                            </div>
                        </div>
                        <div>
                            <div class="text-xs text-muted-foreground mb-2">No Future Dates</div>
                            <div class="border rounded-md w-fit">
                                <Calendar v-model="noFutureDate" :isDateDisabled="isFutureDate" />
                            </div>
                        </div>
                        <div>
                            <div class="text-xs text-muted-foreground mb-2">Weekdays Only (No Past)</div>
                            <div class="border rounded-md w-fit">
                                <Calendar v-model="weekdaysFutureDate" :isDateDisabled="isWeekendOrPast" />
                            </div>
                        </div>
                        <div>
                            <div class="text-xs text-muted-foreground mb-2">No Wednesdays</div>
                            <div class="border rounded-md w-fit">
                                <Calendar v-model="noWednesdaysDate" :isDateDisabled="isWednesday" />
                            </div>
                        </div>
                        <div>
                            <div class="text-xs text-muted-foreground mb-2">Specific Dates Blocked</div>
                            <div class="border rounded-md w-fit">
                                <Calendar v-model="blockedDatesDate" :isDateDisabled="isBlockedDate" />
                            </div>
                            <div class="text-xs text-muted-foreground mt-2">
                                Blocked: 15th, 20th, 25th of each month
                            </div>
                        </div>
                    </div>
                    <div>
                        <div class="text-sm font-medium mb-3">Pickers with Disabled Dates</div>
                        <div class="grid grid-cols-4 gap-4">
                            <div>
                                <div class="text-xs text-muted-foreground mb-1">No Weekends</div>
                                <DatePicker v-model="pickerNoWeekends" :isDateDisabled="isWeekend" clearable fluid />
                            </div>
                            <div>
                                <div class="text-xs text-muted-foreground mb-1">No Past Dates</div>
                                <DatePicker v-model="pickerNoPast" :isDateDisabled="isPastDate" clearable fluid />
                            </div>
                            <div>
                                <div class="text-xs text-muted-foreground mb-1">Input - No Weekends</div>
                                <DateInput v-model="inputNoWeekends" :isDateDisabled="isWeekend" clearable />
                            </div>
                            <div>
                                <div class="text-xs text-muted-foreground mb-1">Input - No Past Dates</div>
                                <DateInput v-model="inputNoPast" :isDateDisabled="isPastDate" clearable />
                            </div>
                        </div>
                    </div>
                </CardContent>
            </Card>

            <!-- Native Browser Inputs -->
            <Card>
                <CardHeader>
                    <CardTitle>Native Browser Date Inputs</CardTitle>
                    <CardDescription>Browser-native date and time input types for comparison</CardDescription>
                </CardHeader>
                <CardContent>
                    <div class="grid grid-cols-4 gap-4">
                        <div>
                            <div class="text-xs text-muted-foreground mb-1">Date</div>
                            <Input v-model="nativeDate" type="date" fluid />
                        </div>
                        <div>
                            <div class="text-xs text-muted-foreground mb-1">Date Time</div>
                            <Input v-model="nativeDateTime" type="datetime-local" fluid />
                        </div>
                        <div>
                            <div class="text-xs text-muted-foreground mb-1">Time</div>
                            <Input v-model="nativeTime" type="time" fluid />
                        </div>
                        <div>
                            <div class="text-xs text-muted-foreground mb-1">Month</div>
                            <Input v-model="nativeMonth" type="month" fluid />
                        </div>
                    </div>
                </CardContent>
            </Card>
        </div>
    </LayoutApp>
</template>

<script setup lang="ts">
import { ref } from 'vue';
import type { DateValue } from 'reka-ui';
import { AppLayout as LayoutApp } from '@/components/app';
import { Card, CardHeader, CardTitle, CardDescription, CardContent } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Calendar } from '@/components/ui/calendar';
import { DatePicker, DateInput } from '@/components/ui/date-picker';
import { useShowcaseNav } from '../../_composables/useShowcaseNav';
import { today, getLocalTimeZone, getDayOfWeek } from '@internationalized/date';

const { sidebarItems } = useShowcaseNav();

const pageTabs = [
    { href: '/admin/components/forms/calendar/date-input', title: 'Date Input' },
    { href: '/admin/components/forms/calendar/date-range-input', title: 'Date Range Input' },
];

// Native date inputs
const nativeDate = ref('');
const nativeDateTime = ref('');
const nativeTime = ref('');
const nativeMonth = ref('');

// DateInput examples
const dateInputEmpty = ref<DateValue>();
const dateInputClearable = ref<DateValue>();
const dateInputValue = ref<DateValue>(today(getLocalTimeZone()));
const dateInputInvalid = ref<DateValue>(today(getLocalTimeZone()));
const dateInputDisabled = ref<DateValue>(today(getLocalTimeZone()));

// DatePicker examples
const datePickerEmpty = ref<DateValue>();
const datePickerClearable = ref<DateValue>();
const datePickerValue = ref<DateValue>(today(getLocalTimeZone()));
const datePickerInvalid = ref<DateValue>(today(getLocalTimeZone()));
const datePickerDisabled = ref<DateValue>(today(getLocalTimeZone()));

// Inline calendar
const calendarInline = ref<DateValue>();
const calendarMultiMonth = ref<DateValue>();

// Quick navigation
const quickNavDate = ref<DateValue>();
const birthDate = ref<DateValue>();
const quickNavPicker = ref<DateValue>();
const quickNavInput = ref<DateValue>();
const currentYear = new Date().getFullYear();

// Disabled dates
const noWeekendsDate = ref<DateValue>();
const noWednesdaysDate = ref<DateValue>();
const noPastDate = ref<DateValue>();
const noFutureDate = ref<DateValue>();
const weekdaysFutureDate = ref<DateValue>();
const blockedDatesDate = ref<DateValue>();

// Pickers with disabled dates
const pickerNoWeekends = ref<DateValue>();
const pickerNoPast = ref<DateValue>();
const inputNoWeekends = ref<DateValue>();
const inputNoPast = ref<DateValue>();

// Disable functions
const todayDate = today(getLocalTimeZone());

function isWeekend(date: DateValue): boolean {
    const dayOfWeek = getDayOfWeek(date, 'en-US');
    return dayOfWeek === 0 || dayOfWeek === 6;
}

function isWednesday(date: DateValue): boolean {
    return getDayOfWeek(date, 'en-US') === 3;
}

function isPastDate(date: DateValue): boolean {
    return date.compare(todayDate) < 0;
}

function isFutureDate(date: DateValue): boolean {
    return date.compare(todayDate) > 0;
}

function isWeekendOrPast(date: DateValue): boolean {
    return isWeekend(date) || isPastDate(date);
}

function isBlockedDate(date: DateValue): boolean {
    const blockedDays = [15, 20, 25];
    return blockedDays.includes(date.day);
}

function formatDate(date: DateValue) {
    const month = String(date.month).padStart(2, '0');
    const day = String(date.day).padStart(2, '0');
    return `${month}/${day}/${date.year}`;
}
</script>
