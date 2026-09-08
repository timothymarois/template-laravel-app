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
                        !modelValue && 'text-muted-foreground',
                        modelValue && clearable && 'rounded-r-none border-r-0',
                        invalid && 'border-destructive focus:ring-destructive'
                    ]"
                >
                    <span class="flex items-center min-w-0 flex-1">
                        <CalendarIcon class="mr-2 h-4 w-4 shrink-0" />
                        <span class="truncate">{{ modelValue ? formatDateValue(modelValue) : placeholder }}</span>
                    </span>
                    <ChevronDown class="h-4 w-4 opacity-50 shrink-0 ml-2" />
                </Button>
            </PopoverTrigger>
            <PopoverContent class="w-auto p-0" :align="align">
                <Calendar
                    :model-value="modelValue"
                    :isDateDisabled="isDateDisabled"
                    :quick-navigation="quickNavigation"
                    :min-year="minYear"
                    :max-year="maxYear"
                    @update:model-value="onSelect"
                />
            </PopoverContent>
        </PopoverBase>
        <Button
            v-if="clearable && modelValue"
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
import type { DateValue } from 'reka-ui';
import { Button } from '@/components/ui/button';
import { PopoverBase, PopoverContent, PopoverTrigger } from '@/components/ui/popover';
import { Calendar } from '@/components/ui/calendar';
import { Calendar as CalendarIcon, ChevronDown, X } from 'lucide-vue-next';
import { formatDateValue } from '@/utils/format';

interface Props {
    modelValue?: DateValue;
    placeholder?: string;
    clearable?: boolean;
    disabled?: boolean;
    invalid?: boolean;
    fluid?: boolean;
    align?: 'start' | 'center' | 'end';
    isDateDisabled?: (date: DateValue) => boolean;
    /** Enable month/year dropdown navigation for quick jumping to dates */
    quickNavigation?: boolean;
    /** Minimum year for quick navigation dropdown */
    minYear?: number;
    /** Maximum year for quick navigation dropdown */
    maxYear?: number;
}

const props = withDefaults(defineProps<Props>(), {
    placeholder: 'Pick a date',
    clearable: false,
    disabled: false,
    invalid: false,
    fluid: false,
    align: 'start',
    quickNavigation: false,
});

const emit = defineEmits<{
    'update:modelValue': [value: DateValue | undefined];
}>();

const isOpen = ref(false);

function onSelect(date: DateValue | undefined) {
    emit('update:modelValue', date);
    isOpen.value = false;
}

function onClear() {
    emit('update:modelValue', undefined);
}
</script>
