<template>
    <div class="flex min-w-0">
        <PopoverBase v-model:open="isOpen">
            <PopoverTrigger as-child>
                <Button
                    variant="outline"
                    :disabled="disabled"
                    class="justify-between text-left font-normal min-w-0"
                    :class="[
                        fluid ? 'w-full' : '',
                        !fluid && !clearable ? '' : 'flex-1',
                        !displayValue?.start && 'text-muted-foreground',
                        displayValue?.start && clearable && 'rounded-r-none border-r-0',
                        invalid && 'border-destructive focus:ring-destructive'
                    ]"
                >
                    <span class="flex items-center min-w-0 flex-1">
                        <CalendarIcon class="mr-2 h-4 w-4 shrink-0" />
                        <span class="truncate">
                            <template v-if="displayValue?.start && displayValue?.end">
                                {{ formatDateValue(displayValue.start) }} - {{ formatDateValue(displayValue.end) }}
                            </template>
                            <template v-else-if="displayValue?.start">
                                {{ formatDateValue(displayValue.start) }} - ...
                            </template>
                            <template v-else>
                                {{ placeholder }}
                            </template>
                        </span>
                    </span>
                    <ChevronDown class="h-4 w-4 opacity-50 shrink-0 ml-2" />
                </Button>
            </PopoverTrigger>
            <PopoverContent class="w-auto p-0" :align="align">
                <div class="flex">
                    <!-- Presets Sidebar -->
                    <div
                        v-if="resolvedPresets.length > 0"
                        class="w-40 border-r border-border p-3 flex flex-col gap-1"
                    >
                        <button
                            v-for="preset in resolvedPresets"
                            :key="preset.label"
                            type="button"
                            class="text-sm text-left w-full px-2 py-1.5 rounded transition-colors cursor-pointer"
                            :class="[
                                isPresetActive(preset)
                                    ? 'bg-accent text-accent-foreground'
                                    : 'hover:bg-accent hover:text-accent-foreground'
                            ]"
                            @click="onPresetSelect(preset)"
                        >
                            {{ preset.label }}
                        </button>
                    </div>

                    <!-- Calendar -->
                    <div>
                        <RangeCalendar
                            :model-value="internalValue"
                            :isDateDisabled="isDateDisabled"
                            :numberOfMonths="numberOfMonths"
                            @update:model-value="onCalendarSelect"
                        />

                        <!-- Confirm Mode Footer -->
                        <div
                            v-if="confirmMode"
                            class="flex justify-end gap-2 p-3 border-t border-border"
                        >
                            <Button
                                variant="outline"
                                size="sm"
                                @click="onCancel"
                            >
                                Cancel
                            </Button>
                            <Button
                                size="sm"
                                :disabled="!internalValue?.start || !internalValue?.end"
                                @click="onApply"
                            >
                                Apply
                            </Button>
                        </div>
                    </div>
                </div>
            </PopoverContent>
        </PopoverBase>
        <Button
            v-if="clearable && modelValue?.start"
            variant="outline"
            size="icon"
            class="rounded-l-none"
            :disabled="disabled"
            @click="onClear"
        >
            <X class="h-4 w-4" />
        </Button>
    </div>
</template>

<script setup lang="ts">
import { ref, computed, watch } from 'vue';
import type { DateValue, DateRange } from 'reka-ui';
import { Button } from '@/components/ui/button';
import { PopoverBase, PopoverContent, PopoverTrigger } from '@/components/ui/popover';
import { RangeCalendar } from '@/components/ui/range-calendar';
import { Calendar as CalendarIcon, ChevronDown, X } from 'lucide-vue-next';
import { formatDateValue } from '@/utils/format';
import { defaultPresets, type DateRangePreset } from './presets';

interface Props {
    modelValue?: DateRange;
    placeholder?: string;
    clearable?: boolean;
    disabled?: boolean;
    invalid?: boolean;
    fluid?: boolean;
    align?: 'start' | 'center' | 'end';
    numberOfMonths?: number;
    isDateDisabled?: (date: DateValue) => boolean;
    /**
     * Preset options for quick date range selection.
     * - `true` - use default presets
     * - `DateRangePreset[]` - use custom presets
     * - `false` or `undefined` - no presets (default)
     */
    presets?: DateRangePreset[] | boolean;
    /**
     * When true, requires explicit Apply/Cancel instead of auto-close on selection.
     * Useful when you want users to confirm their selection before applying.
     */
    confirmMode?: boolean;
}

const props = withDefaults(defineProps<Props>(), {
    placeholder: 'Pick a date range',
    clearable: false,
    disabled: false,
    invalid: false,
    fluid: false,
    align: 'start',
    numberOfMonths: 2,
    presets: false,
    confirmMode: false,
});

const emit = defineEmits<{
    'update:modelValue': [value: DateRange | undefined];
}>();

const isOpen = ref(false);
const internalValue = ref<DateRange | undefined>(props.modelValue);

// Resolve presets based on prop value
const resolvedPresets = computed<DateRangePreset[]>(() => {
    if (props.presets === true) {
        return defaultPresets;
    }
    if (Array.isArray(props.presets)) {
        return props.presets;
    }
    return [];
});

// The value to display in the trigger button
const displayValue = computed(() => {
    if (props.confirmMode) {
        // In confirm mode, always show the committed (modelValue) value
        return props.modelValue;
    }
    return props.modelValue;
});

// Sync internal value when modelValue changes externally
watch(() => props.modelValue, (newValue) => {
    internalValue.value = newValue;
}, { deep: true });

// Reset internal value when popover opens
watch(isOpen, (open) => {
    if (open) {
        internalValue.value = props.modelValue;
    }
});

// Check if a preset matches the current internal selection
function isPresetActive(preset: DateRangePreset): boolean {
    if (!internalValue.value?.start || !internalValue.value?.end) {
        return false;
    }
    const presetRange = preset.getValue();
    return (
        presetRange.start?.toString() === internalValue.value.start?.toString() &&
        presetRange.end?.toString() === internalValue.value.end?.toString()
    );
}

// Handle preset selection
function onPresetSelect(preset: DateRangePreset) {
    const range = preset.getValue();
    internalValue.value = range;

    if (!props.confirmMode) {
        emit('update:modelValue', range);
        isOpen.value = false;
    }
}

// Handle calendar selection
function onCalendarSelect(range: DateRange) {
    internalValue.value = range;

    if (!props.confirmMode) {
        emit('update:modelValue', range);
        // Close popover when both dates are selected
        if (range.start && range.end) {
            isOpen.value = false;
        }
    }
}

// Confirm mode: Apply selection
function onApply() {
    emit('update:modelValue', internalValue.value);
    isOpen.value = false;
}

// Confirm mode: Cancel and revert
function onCancel() {
    internalValue.value = props.modelValue;
    isOpen.value = false;
}

// Clear the selection
function onClear() {
    emit('update:modelValue', undefined);
}
</script>
