<template>
    <Popover v-model:open="isOpen">
        <PopoverTrigger as-child>
            <button
                type="button"
                role="combobox"
                :aria-expanded="isOpen"
                :aria-haspopup="'listbox'"
                :disabled="disabled"
                :class="cn(
                    'inline-flex items-center justify-between gap-2 rounded-md border border-input bg-background px-3 text-sm text-start transition-all cursor-pointer',
                    'hover:border-foreground/50 focus:outline-none focus:border-foreground/50',
                    'disabled:cursor-not-allowed disabled:opacity-50 disabled:hover:border-input',
                    multiple && showChips ? 'min-h-9 py-1.5' : 'h-9 py-1',
                    fluid ? 'w-full' : '',
                    triggerClass,
                )"
            >
                <!-- Multi-select with chips -->
                <span v-if="multiple && showChips && selectedValues.length > 0" class="flex flex-wrap gap-1 flex-1">
                    <span
                        v-for="value in selectedValues"
                        :key="value"
                        :class="chipClasses"
                    >
                        {{ getLabel(value) }}
                        <X
                            v-if="!disabled"
                            class="h-3 w-3 cursor-pointer hover:text-destructive"
                            @click.stop="removeValue(value)"
                        />
                    </span>
                </span>
                <!-- Single select or multi without chips -->
                <span v-else :class="hasValue ? '' : 'text-muted-foreground'" class="truncate">
                    {{ displayLabel }}
                </span>
                <span class="flex items-center gap-1 shrink-0">
                    <X
                        v-if="clearable && hasValue && !disabled"
                        class="h-4 w-4 opacity-50 hover:opacity-100 cursor-pointer"
                        @click.stop="clearAll"
                    />
                    <ChevronsUpDown class="h-4 w-4 opacity-50" />
                </span>
            </button>
        </PopoverTrigger>
            <PopoverContent
                :class="cn('p-0', contentClass)"
                :style="{ width: 'var(--reka-popover-trigger-width)' }"
            >
                <!-- Search input -->
                <div v-if="searchable" class="flex items-center border-b px-3">
                    <Search class="mr-2 h-4 w-4 shrink-0 opacity-50" />
                    <input
                        ref="searchInput"
                        v-model="searchQuery"
                        :placeholder="searchPlaceholder"
                        class="flex h-10 w-full bg-transparent py-3 text-sm outline-none placeholder:text-muted-foreground"
                        @keydown="handleSearchKeydown"
                    />
                </div>
                <!-- Options list -->
                <div
                    ref="listRef"
                    role="listbox"
                    :aria-multiselectable="multiple"
                    class="max-h-60 overflow-y-auto p-1"
                    @keydown="handleListKeydown"
                >
                    <div
                        v-if="filteredOptions.length === 0"
                        class="py-6 text-center text-sm text-muted-foreground"
                    >
                        {{ emptyText }}
                    </div>
                    <div
                        v-for="(option, index) in filteredOptions"
                        :key="getOptionValue(option)"
                        role="option"
                        :aria-selected="isSelected(getOptionValue(option))"
                        :data-highlighted="highlightedIndex === index"
                        :class="cn(
                            'relative flex cursor-pointer select-none items-center rounded-sm px-2 py-1.5 text-sm outline-none',
                            'hover:bg-accent hover:text-accent-foreground',
                            'data-[highlighted=true]:bg-accent data-[highlighted=true]:text-accent-foreground',
                        )"
                        @click="selectOption(option)"
                        @mouseenter="highlightedIndex = index"
                    >
                        <span class="mr-2 flex h-4 w-4 items-center justify-center">
                            <Check v-if="isSelected(getOptionValue(option))" class="h-4 w-4" />
                        </span>
                        <slot name="option" :option="option" :label="getOptionLabel(option)">
                            {{ getOptionLabel(option) }}
                        </slot>
                    </div>
                </div>
            </PopoverContent>
    </Popover>
</template>

<script setup lang="ts">
import { ref, computed, watch, nextTick } from 'vue';
import { PopoverBase as Popover, PopoverContent, PopoverTrigger } from '@/components/ui/popover';
import { Check, ChevronsUpDown, Search, X } from 'lucide-vue-next';
import { cn } from '@/utils';

export interface SelectOption {
    label: string;
    value: string | number;
    disabled?: boolean;
    [key: string]: any;
}

interface Props {
    modelValue?: string | number | (string | number)[] | null;
    options?: (SelectOption | string | number)[];
    optionLabel?: string;
    optionValue?: string;
    placeholder?: string;
    searchPlaceholder?: string;
    emptyText?: string;
    disabled?: boolean;
    multiple?: boolean;
    searchable?: boolean;
    clearable?: boolean;
    /** Show chips in trigger for multi-select */
    chips?: boolean;
    /** Chip visual style */
    chipVariant?: 'default' | 'secondary' | 'outline' | 'primary';
    fluid?: boolean;
    triggerClass?: string;
    contentClass?: string;
}

const props = withDefaults(defineProps<Props>(), {
    options: () => [],
    optionLabel: 'label',
    optionValue: 'value',
    placeholder: 'Select...',
    searchPlaceholder: 'Search...',
    emptyText: 'No options found.',
    disabled: false,
    multiple: false,
    searchable: false,
    clearable: false,
    chips: true,
    chipVariant: 'default',
    fluid: false,
});

const emit = defineEmits<{
    'update:modelValue': [value: string | number | (string | number)[] | null];
}>();

const isOpen = ref(false);
const searchQuery = ref('');
const highlightedIndex = ref(0);
const searchInput = ref<HTMLInputElement>();
const listRef = ref<HTMLElement>();

// Whether to show chips in trigger
const showChips = computed(() => props.chips);

// Chip styling based on variant
const chipClasses = computed(() => {
    const base = 'inline-flex items-center gap-1 text-xs font-medium';
    const variants = {
        default: 'rounded bg-secondary px-2 py-0.5',
        secondary: 'rounded-full bg-secondary px-2.5 py-0.5',
        outline: 'rounded-full border px-2.5 py-0.5',
        primary: 'rounded-full bg-primary/10 text-primary px-2.5 py-0.5',
    };
    return cn(base, variants[props.chipVariant]);
});

// Normalize options to always have label/value
const normalizedOptions = computed(() => {
    return props.options.map(opt => {
        if (typeof opt === 'string' || typeof opt === 'number') {
            return { label: String(opt), value: opt };
        }
        return opt;
    });
});

// Filter options based on search
const filteredOptions = computed(() => {
    if (!searchQuery.value) return normalizedOptions.value;
    const query = searchQuery.value.toLowerCase();
    return normalizedOptions.value.filter(opt =>
        getOptionLabel(opt).toLowerCase().includes(query)
    );
});

// Get the selected values as array
const selectedValues = computed<(string | number)[]>(() => {
    if (props.modelValue === null || props.modelValue === undefined || props.modelValue === '') return [];
    if (Array.isArray(props.modelValue)) return props.modelValue.filter(v => v !== '' && v !== null && v !== undefined);
    return [props.modelValue];
});

const hasValue = computed(() => selectedValues.value.length > 0);

// Display label for trigger
const displayLabel = computed(() => {
    if (!hasValue.value) return props.placeholder;

    if (props.multiple) {
        // If chips are shown, trigger shows placeholder (chips handle display)
        if (showChips.value) return props.placeholder;
        // No chips: show count
        return `${selectedValues.value.length} selected`;
    }

    return getLabel(selectedValues.value[0]);
});

// Helper functions
const getOptionLabel = (option: SelectOption | string | number): string => {
    if (typeof option === 'string' || typeof option === 'number') return String(option);
    return option[props.optionLabel] ?? option.label ?? String(option.value);
};

const getOptionValue = (option: SelectOption | string | number): string | number => {
    if (typeof option === 'string' || typeof option === 'number') return option;
    return option[props.optionValue] ?? option.value;
};

const getLabel = (value: string | number): string => {
    const option = normalizedOptions.value.find(opt => getOptionValue(opt) === value);
    return option ? getOptionLabel(option) : String(value);
};

const isSelected = (value: string | number): boolean => {
    return selectedValues.value.includes(value);
};

const selectOption = (option: SelectOption | string | number) => {
    const value = getOptionValue(option);

    if (props.multiple) {
        const newValues = isSelected(value)
            ? selectedValues.value.filter(v => v !== value)
            : [...selectedValues.value, value];
        emit('update:modelValue', newValues);
    } else {
        emit('update:modelValue', value);
        isOpen.value = false;
        searchQuery.value = '';
    }
};

const removeValue = (value: string | number) => {
    if (props.multiple) {
        emit('update:modelValue', selectedValues.value.filter(v => v !== value));
    }
};

const clearAll = () => {
    emit('update:modelValue', props.multiple ? [] : null);
};

// Keyboard navigation
const handleListKeydown = (e: KeyboardEvent) => {
    switch (e.key) {
        case 'ArrowDown':
            e.preventDefault();
            highlightedIndex.value = Math.min(highlightedIndex.value + 1, filteredOptions.value.length - 1);
            break;
        case 'ArrowUp':
            e.preventDefault();
            highlightedIndex.value = Math.max(highlightedIndex.value - 1, 0);
            break;
        case 'Enter':
            e.preventDefault();
            if (filteredOptions.value[highlightedIndex.value]) {
                selectOption(filteredOptions.value[highlightedIndex.value]);
            }
            break;
        case 'Escape':
            isOpen.value = false;
            break;
    }
};

const handleSearchKeydown = (e: KeyboardEvent) => {
    if (['ArrowDown', 'ArrowUp', 'Enter'].includes(e.key)) {
        handleListKeydown(e);
    }
};

// Focus search input when opened
watch(isOpen, async (open) => {
    if (open) {
        highlightedIndex.value = 0;
        if (props.searchable) {
            await nextTick();
            searchInput.value?.focus();
        }
    } else {
        searchQuery.value = '';
    }
});

// Reset highlight when search changes
watch(searchQuery, () => {
    highlightedIndex.value = 0;
});
</script>
