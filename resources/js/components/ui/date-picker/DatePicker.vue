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
                        !modelValue && 'text-muted-foreground',
                        modelValue && clearable && 'rounded-r-none border-r-0'
                    ]"
                >
                    <span class="flex items-center">
                        <CalendarIcon class="mr-2 h-4 w-4" />
                        {{ modelValue ? formatDate(modelValue) : placeholder }}
                    </span>
                    <ChevronDown class="h-4 w-4 opacity-50" />
                </Button>
            </PopoverTrigger>
            <PopoverContent class="w-auto p-0" :align="align">
                <Calendar
                    :model-value="modelValue"
                    :isDateDisabled="isDateDisabled"
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

interface Props {
    modelValue?: DateValue;
    placeholder?: string;
    clearable?: boolean;
    disabled?: boolean;
    fluid?: boolean;
    align?: 'start' | 'center' | 'end';
    isDateDisabled?: (date: DateValue) => boolean;
}

const props = withDefaults(defineProps<Props>(), {
    placeholder: 'Pick a date',
    clearable: false,
    disabled: false,
    fluid: false,
    align: 'start',
});

const emit = defineEmits<{
    'update:modelValue': [value: DateValue | undefined];
}>();

const isOpen = ref(false);

function formatDate(date: DateValue) {
    const month = String(date.month).padStart(2, '0');
    const day = String(date.day).padStart(2, '0');
    return `${month}/${day}/${date.year}`;
}

function onSelect(date: DateValue) {
    emit('update:modelValue', date);
    isOpen.value = false;
}

function onClear() {
    emit('update:modelValue', undefined);
}
</script>
