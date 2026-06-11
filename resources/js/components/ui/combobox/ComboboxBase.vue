<script setup lang="ts">
import { ref, computed, toRef } from 'vue';
import { useDebounceFn } from '@vueuse/core';
import { PopoverBase, PopoverContent, PopoverTrigger } from '@/components/ui/popover';
import {
    Command,
    CommandEmpty,
    CommandGroup,
    CommandInput,
    CommandItem,
    CommandList,
} from '@/components/ui/command';
import { Spinner } from '@/components/ui/spinner';
import { Check, ChevronsUpDown, X } from 'lucide-vue-next';
import { cn } from '@/utils';
import { useSelectableOptions, type SelectableRawOption } from '@/composables';

export type ComboboxOption = SelectableRawOption;

interface Props {
    modelValue?: string | number | (string | number)[] | null;
    options?: (ComboboxOption | string | number)[];
    optionLabel?: string;
    optionValue?: string;
    placeholder?: string;
    searchPlaceholder?: string;
    emptyText?: string;
    loadingText?: string;
    disabled?: boolean;
    multiple?: boolean;
    clearable?: boolean;
    /** Show loading spinner */
    loading?: boolean;
    /** Debounce delay in ms for search event */
    debounce?: number;
    /** Disable client-side filtering - use for async/server-side search */
    disableFilter?: boolean;
    fluid?: boolean;
    triggerClass?: string;
    contentClass?: string;
    invalid?: boolean;
}

const props = withDefaults(defineProps<Props>(), {
    options: () => [],
    optionLabel: 'label',
    optionValue: 'value',
    placeholder: 'Select...',
    searchPlaceholder: 'Search...',
    emptyText: 'No results found.',
    loadingText: 'Loading...',
    disabled: false,
    multiple: false,
    clearable: true,
    loading: false,
    debounce: 300,
    disableFilter: false,
    fluid: false,
    invalid: false,
});

const emit = defineEmits<{
    'update:modelValue': [value: string | number | (string | number)[] | null];
    'search': [query: string];
}>();

const isOpen = ref(false);

// Use shared selectable options composable
const {
    normalizedOptions,
    selectedValues,
    hasValue,
    getOptionLabel,
    getOptionValue,
    getLabel,
    isSelected,
    toggleOption,
    clearAll,
} = useSelectableOptions({
    modelValue: toRef(props, 'modelValue'),
    options: toRef(props, 'options'),
    optionLabel: props.optionLabel,
    optionValue: props.optionValue,
    multiple: props.multiple,
});

// Debounced search emission
const debouncedEmitSearch = useDebounceFn((query: string) => {
    emit('search', query);
}, props.debounce);

// Handle search changes from Command component
const handleSearchChange = (query: string) => {
    debouncedEmitSearch(query);
};

// Display label for trigger
const displayLabel = computed(() => {
    if (!hasValue.value) return '';

    if (props.multiple) {
        return `${selectedValues.value.length} selected`;
    }

    return getLabel(selectedValues.value[0]);
});

const handleSelect = (ev: { detail: { value: string } }) => {
    const label = ev.detail.value;
    // Find the option by label (Command uses label as value for filtering)
    const option = normalizedOptions.value.find(opt => getOptionLabel(opt) === label);
    if (!option) return;

    const newValue = toggleOption(option);
    emit('update:modelValue', newValue);

    if (!props.multiple) {
        isOpen.value = false;
    }
};

const clearValue = (e: Event) => {
    e.stopPropagation();
    emit('update:modelValue', clearAll());
};

// Simple select handler for when disableFilter is true (bypasses Command)
const handleSimpleSelect = (option: ComboboxOption | string | number) => {
    const newValue = toggleOption(option);
    emit('update:modelValue', newValue);

    if (!props.multiple) {
        isOpen.value = false;
    }
};
</script>

<template>
    <PopoverBase v-model:open="isOpen">
        <PopoverTrigger as-child>
            <button
                type="button"
                role="combobox"
                :aria-expanded="isOpen"
                :aria-haspopup="'listbox'"
                :disabled="disabled"
                :class="cn(
                    'inline-flex h-9 items-center justify-between gap-2 rounded-md border border-input bg-background px-3 py-1 text-sm text-start transition-all cursor-pointer',
                    'hover:border-foreground/50 focus:outline-none focus:border-foreground/50',
                    'disabled:cursor-not-allowed disabled:opacity-50 disabled:hover:border-input',
                    fluid ? 'w-full' : 'min-w-[200px]',
                    triggerClass,
                )"
            >
                <span :class="hasValue ? '' : 'text-muted-foreground'" class="truncate">
                    {{ displayLabel || placeholder }}
                </span>
                <span class="flex items-center gap-1 shrink-0">
                    <Spinner
                        v-if="loading"
                        size="xs"
                        variant="muted"
                    />
                    <X
                        v-else-if="clearable && hasValue && !disabled"
                        class="h-4 w-4 opacity-50 hover:opacity-100 cursor-pointer"
                        @click="clearValue"
                    />
                    <ChevronsUpDown class="h-4 w-4 opacity-50" />
                </span>
            </button>
        </PopoverTrigger>
        <PopoverContent
            :class="cn('p-0', contentClass)"
            :style="{ width: 'var(--reka-popper-anchor-width)' }"
        >
            <Command :disable-filter="disableFilter" @search-change="handleSearchChange">
                <CommandInput :placeholder="searchPlaceholder" />
                <CommandList>
                    <!-- Loading state -->
                    <div v-if="loading" class="py-6 text-center">
                        <Spinner size="sm" variant="muted" class="mx-auto" />
                        <p class="text-sm text-muted-foreground mt-2">{{ loadingText }}</p>
                    </div>
                    <template v-else>
                        <!-- Show empty text only when there are no options -->
                        <div v-if="normalizedOptions.length === 0" class="py-6 text-center text-sm text-muted-foreground">
                            {{ emptyText }}
                        </div>
                        <!-- When filtering is disabled, use simple buttons to bypass Command filtering -->
                        <div v-else-if="disableFilter" class="max-h-[300px] overflow-y-auto overflow-x-hidden p-1">
                            <button
                                v-for="option in normalizedOptions"
                                :key="getOptionValue(option)"
                                type="button"
                                class="relative flex w-full cursor-pointer select-none items-center rounded-sm px-2 py-1.5 text-sm outline-none hover:bg-accent hover:text-accent-foreground"
                                @click="handleSimpleSelect(option)"
                            >
                                <Check
                                    :class="cn(
                                        'mr-2 h-4 w-4',
                                        isSelected(getOptionValue(option)) ? 'opacity-100' : 'opacity-0'
                                    )"
                                />
                                <slot name="option" :option="option" :label="getOptionLabel(option)">
                                    {{ getOptionLabel(option) }}
                                </slot>
                            </button>
                        </div>
                        <CommandGroup v-else>
                            <CommandItem
                                v-for="option in normalizedOptions"
                                :key="getOptionValue(option)"
                                :value="getOptionLabel(option)"
                                @select="handleSelect"
                            >
                                <Check
                                    :class="cn(
                                        'mr-2 h-4 w-4',
                                        isSelected(getOptionValue(option)) ? 'opacity-100' : 'opacity-0'
                                    )"
                                />
                                <slot name="option" :option="option" :label="getOptionLabel(option)">
                                    {{ getOptionLabel(option) }}
                                </slot>
                            </CommandItem>
                        </CommandGroup>
                    </template>
                </CommandList>
            </Command>
        </PopoverContent>
    </PopoverBase>
</template>
