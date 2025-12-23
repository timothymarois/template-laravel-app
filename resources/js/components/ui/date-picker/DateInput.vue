<template>
    <div>
        <div class="relative">
            <Input
                :model-value="inputValue"
                @input="onInput"
                @blur="onBlur"
                :placeholder="placeholder"
                :invalid="!isValid || invalid"
                :disabled="disabled"
                fluid
                :class="hasInput ? 'pr-18' : 'pr-10'"
            />
            <div class="absolute right-0 top-0 h-full flex items-center">
                <button
                    v-if="clearable && hasInput"
                    type="button"
                    class="px-2 text-muted-foreground hover:text-foreground cursor-pointer"
                    :disabled="disabled"
                    @click="onClear"
                >
                    <X class="h-4 w-4" />
                </button>
                <PopoverBase v-model:open="isOpen">
                    <PopoverTrigger as-child>
                        <button
                            type="button"
                            class="px-3 h-full text-muted-foreground hover:text-foreground cursor-pointer disabled:cursor-not-allowed disabled:opacity-50"
                            :disabled="disabled"
                        >
                            <CalendarIcon class="h-4 w-4" />
                        </button>
                    </PopoverTrigger>
                    <PopoverContent class="w-auto p-0" :align="align">
                        <Calendar
                            :model-value="dateValue"
                            :isDateDisabled="isDateDisabled"
                            @update:model-value="onCalendarSelect"
                        />
                    </PopoverContent>
                </PopoverBase>
            </div>
        </div>
        <div v-if="!isValid && showError" class="text-xs text-destructive mt-1">
            {{ errorMessage }}
        </div>
    </div>
</template>

<script setup lang="ts">
import { ref, computed, watch } from 'vue';
import type { DateValue } from 'reka-ui';
import { Input } from '@/components/ui/input';
import { PopoverBase, PopoverContent, PopoverTrigger } from '@/components/ui/popover';
import { Calendar } from '@/components/ui/calendar';
import { Calendar as CalendarIcon, X } from 'lucide-vue-next';
import { CalendarDate } from '@internationalized/date';
import { formatDateValue } from '@/utils/format';

interface Props {
    modelValue?: DateValue;
    placeholder?: string;
    clearable?: boolean;
    disabled?: boolean;
    invalid?: boolean;
    showError?: boolean;
    errorMessage?: string;
    align?: 'start' | 'center' | 'end';
    isDateDisabled?: (date: DateValue) => boolean;
}

const props = withDefaults(defineProps<Props>(), {
    placeholder: 'MM/DD/YYYY',
    clearable: false,
    disabled: false,
    invalid: false,
    showError: true,
    errorMessage: 'Invalid date format',
    align: 'end',
});

const emit = defineEmits<{
    'update:modelValue': [value: DateValue | undefined];
}>();

const inputValue = ref(props.modelValue ? formatDateValue(props.modelValue) : '');
const dateValue = ref<DateValue | undefined>(props.modelValue);
const isOpen = ref(false);
const isValid = ref(true);

const hasInput = computed(() => inputValue.value.length > 0);

// Sync with external modelValue changes
watch(() => props.modelValue, (newVal) => {
    if (newVal) {
        const formatted = formatDateValue(newVal);
        if (formatted !== inputValue.value) {
            inputValue.value = formatted;
            dateValue.value = newVal;
            isValid.value = true;
        }
    } else if (!newVal && dateValue.value) {
        inputValue.value = '';
        dateValue.value = undefined;
        isValid.value = true;
    }
});

// Parse and validate date string (MM/DD/YYYY)
function parseDate(str: string): DateValue | null {
    const cleaned = str.replace(/[^\d/]/g, '');
    const parts = cleaned.split('/');

    if (parts.length !== 3) return null;

    const month = parseInt(parts[0], 10);
    const day = parseInt(parts[1], 10);
    const year = parseInt(parts[2], 10);

    if (isNaN(month) || isNaN(day) || isNaN(year)) return null;
    if (month < 1 || month > 12) return null;
    if (day < 1 || day > 31) return null;
    if (year < 1900 || year > 2100) return null;

    const daysInMonth = new Date(year, month, 0).getDate();
    if (day > daysInMonth) return null;

    try {
        return new CalendarDate(year, month, day);
    } catch {
        return null;
    }
}

// Format input with auto-slashes (max 10 chars: MM/DD/YYYY)
function formatInput(value: string): string {
    const digits = value.replace(/\D/g, '');
    const limited = digits.slice(0, 8);

    if (limited.length <= 2) return limited;
    if (limited.length <= 4) return `${limited.slice(0, 2)}/${limited.slice(2)}`;
    return `${limited.slice(0, 2)}/${limited.slice(2, 4)}/${limited.slice(4)}`;
}

function onInput(event: Event) {
    const target = event.target as HTMLInputElement;
    const formatted = formatInput(target.value);
    inputValue.value = formatted;

    if (formatted.length === 10) {
        const parsed = parseDate(formatted);
        if (parsed) {
            // Check if date is disabled
            if (props.isDateDisabled?.(parsed)) {
                isValid.value = false;
            } else {
                dateValue.value = parsed;
                isValid.value = true;
                emit('update:modelValue', parsed);
            }
        } else {
            isValid.value = false;
        }
    } else if (formatted.length === 0) {
        dateValue.value = undefined;
        isValid.value = true;
        emit('update:modelValue', undefined);
    } else {
        isValid.value = true;
    }
}

function onBlur() {
    if (inputValue.value.length > 0 && inputValue.value.length < 10) {
        isValid.value = false;
    } else if (inputValue.value.length === 10) {
        const parsed = parseDate(inputValue.value);
        if (parsed && props.isDateDisabled?.(parsed)) {
            isValid.value = false;
        } else {
            isValid.value = parsed !== null;
        }
    }
}

function onCalendarSelect(date: DateValue) {
    dateValue.value = date;
    inputValue.value = formatDateValue(date);
    isValid.value = true;
    isOpen.value = false;
    emit('update:modelValue', date);
}

function onClear() {
    inputValue.value = '';
    dateValue.value = undefined;
    isValid.value = true;
    emit('update:modelValue', undefined);
}
</script>
