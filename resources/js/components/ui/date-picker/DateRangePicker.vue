<template>
    <div class="flex">
        <PopoverBase v-model:open="isOpen">
            <PopoverTrigger as-child>
                <Button
                    variant="outline"
                    :disabled="disabled"
                    class="justify-between text-left font-normal"
                    :class="[
                        fluid ? 'w-full' : '',
                        !fluid && !clearable ? '' : 'flex-1',
                        !modelValue?.start && 'text-muted-foreground',
                        modelValue?.start && clearable && 'rounded-r-none border-r-0',
                        invalid && 'border-destructive focus:ring-destructive'
                    ]"
                >
                    <span class="flex items-center">
                        <CalendarIcon class="mr-2 h-4 w-4" />
                        <template v-if="modelValue?.start && modelValue?.end">
                            {{ formatDateValue(modelValue.start) }} - {{ formatDateValue(modelValue.end) }}
                        </template>
                        <template v-else-if="modelValue?.start">
                            {{ formatDateValue(modelValue.start) }} - ...
                        </template>
                        <template v-else>
                            {{ placeholder }}
                        </template>
                    </span>
                    <ChevronDown class="h-4 w-4 opacity-50" />
                </Button>
            </PopoverTrigger>
            <PopoverContent class="w-auto p-0" :align="align">
                <RangeCalendar
                    :model-value="modelValue"
                    :isDateDisabled="isDateDisabled"
                    :numberOfMonths="numberOfMonths"
                    @update:model-value="onSelect"
                />
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
import { ref } from 'vue';
import type { DateValue, DateRange } from 'reka-ui';
import { Button } from '@/components/ui/button';
import { PopoverBase, PopoverContent, PopoverTrigger } from '@/components/ui/popover';
import { RangeCalendar } from '@/components/ui/range-calendar';
import { Calendar as CalendarIcon, ChevronDown, X } from 'lucide-vue-next';
import { formatDateValue } from '@/utils/format';

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
}

const props = withDefaults(defineProps<Props>(), {
    placeholder: 'Pick a date range',
    clearable: false,
    disabled: false,
    invalid: false,
    fluid: false,
    align: 'start',
    numberOfMonths: 2,
});

const emit = defineEmits<{
    'update:modelValue': [value: DateRange | undefined];
}>();

const isOpen = ref(false);

function onSelect(range: DateRange) {
    emit('update:modelValue', range);
    // Close popover when both dates are selected
    if (range.start && range.end) {
        isOpen.value = false;
    }
}

function onClear() {
    emit('update:modelValue', undefined);
}
</script>
