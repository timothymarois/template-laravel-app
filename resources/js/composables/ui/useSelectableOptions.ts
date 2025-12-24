import { computed, type Ref, type ComputedRef } from 'vue';

/**
 * Composable for managing selectable options in dropdown-style components.
 *
 * This composable extracts common logic shared between SelectPopover and ComboboxBase
 * components, providing a consistent API for option normalization, selection state
 * management, and value manipulation.
 *
 * @example Single selection mode
 * ```ts
 * const { selectedValues, hasValue, toggleOption, clearAll } = useSelectableOptions({
 *     modelValue: toRef(props, 'modelValue'),
 *     options: toRef(props, 'options'),
 *     multiple: false,
 * });
 *
 * // Toggle selection
 * emit('update:modelValue', toggleOption(option));
 *
 * // Clear selection
 * emit('update:modelValue', clearAll()); // Returns null
 * ```
 *
 * @example Multiple selection mode
 * ```ts
 * const { selectedValues, isSelected, toggleOption, removeValue } = useSelectableOptions({
 *     modelValue: toRef(props, 'modelValue'),
 *     options: toRef(props, 'options'),
 *     multiple: true,
 * });
 *
 * // Check if value is selected
 * if (isSelected('apple')) { ... }
 *
 * // Toggle (add/remove) an option
 * emit('update:modelValue', toggleOption(option));
 *
 * // Remove specific value
 * emit('update:modelValue', removeValue('apple'));
 * ```
 *
 * @example Custom label/value keys
 * ```ts
 * const options = [
 *     { name: 'Apple', id: 1 },
 *     { name: 'Banana', id: 2 },
 * ];
 *
 * const { getOptionLabel, getOptionValue } = useSelectableOptions({
 *     modelValue: ref(null),
 *     options: ref(options),
 *     optionLabel: 'name',
 *     optionValue: 'id',
 * });
 *
 * getOptionLabel(options[0]); // 'Apple'
 * getOptionValue(options[0]); // 1
 * ```
 */

/** Standard option object with label and value */
export interface SelectableOption {
    label: string;
    value: string | number;
    disabled?: boolean;
    [key: string]: any;
}

/** Possible values for selection: single value, array of values, or null */
export type SelectableValue = string | number | (string | number)[] | null;

/** Props for useSelectableOptions composable */
export interface UseSelectableOptionsProps {
    /** Reactive reference to the current selection value */
    modelValue: Ref<SelectableValue> | ComputedRef<SelectableValue>;
    /** Reactive reference to available options (can be objects, strings, or numbers) */
    options: Ref<(SelectableOption | string | number)[]> | ComputedRef<(SelectableOption | string | number)[]>;
    /** Property key to use for option labels (default: 'label') */
    optionLabel?: string;
    /** Property key to use for option values (default: 'value') */
    optionValue?: string;
    /** Whether multiple selections are allowed */
    multiple?: boolean;
}

/** Return type for useSelectableOptions composable */
export interface UseSelectableOptionsReturn {
    /** All options normalized to {label, value} format */
    normalizedOptions: ComputedRef<SelectableOption[]>;
    /** Currently selected values as an array (empty if nothing selected) */
    selectedValues: ComputedRef<(string | number)[]>;
    /** Whether any value is currently selected */
    hasValue: ComputedRef<boolean>;
    /** Extract label from an option (handles string/number/object) */
    getOptionLabel: (option: SelectableOption | string | number) => string;
    /** Extract value from an option (handles string/number/object) */
    getOptionValue: (option: SelectableOption | string | number) => string | number;
    /** Find the label for a given value */
    getLabel: (value: string | number) => string;
    /** Check if a value is currently selected */
    isSelected: (value: string | number) => boolean;
    /** Toggle an option's selection state, returns new value to emit */
    toggleOption: (option: SelectableOption | string | number) => SelectableValue;
    /** Remove a value from selection, returns new value to emit */
    removeValue: (value: string | number) => SelectableValue;
    /** Clear all selections, returns new value to emit ([] or null) */
    clearAll: () => SelectableValue;
}

export function useSelectableOptions(props: UseSelectableOptionsProps): UseSelectableOptionsReturn {
    const optionLabelKey = props.optionLabel ?? 'label';
    const optionValueKey = props.optionValue ?? 'value';

    // Normalize options to always have label/value
    const normalizedOptions = computed<SelectableOption[]>(() => {
        return props.options.value.map(opt => {
            if (typeof opt === 'string' || typeof opt === 'number') {
                return { label: String(opt), value: opt };
            }
            return opt;
        });
    });

    // Get the selected values as array
    const selectedValues = computed<(string | number)[]>(() => {
        const val = props.modelValue.value;
        if (val === null || val === undefined || val === '') return [];
        if (Array.isArray(val)) return val.filter(v => v !== '' && v !== null && v !== undefined);
        return [val];
    });

    const hasValue = computed(() => selectedValues.value.length > 0);

    // Helper functions
    const getOptionLabel = (option: SelectableOption | string | number): string => {
        if (typeof option === 'string' || typeof option === 'number') return String(option);
        return option[optionLabelKey] ?? option.label ?? String(option.value);
    };

    const getOptionValue = (option: SelectableOption | string | number): string | number => {
        if (typeof option === 'string' || typeof option === 'number') return option;
        return option[optionValueKey] ?? option.value;
    };

    const getLabel = (value: string | number): string => {
        const option = normalizedOptions.value.find(opt => getOptionValue(opt) === value);
        return option ? getOptionLabel(option) : String(value);
    };

    const isSelected = (value: string | number): boolean => {
        return selectedValues.value.includes(value);
    };

    // Toggle selection of an option, returns the new value to emit
    const toggleOption = (option: SelectableOption | string | number): SelectableValue => {
        const optionValue = getOptionValue(option);

        if (props.multiple) {
            return isSelected(optionValue)
                ? selectedValues.value.filter(v => v !== optionValue)
                : [...selectedValues.value, optionValue];
        } else {
            return isSelected(optionValue) ? null : optionValue;
        }
    };

    // Remove a value from selection, returns the new value to emit
    const removeValue = (value: string | number): SelectableValue => {
        if (props.multiple) {
            return selectedValues.value.filter(v => v !== value);
        }
        return null;
    };

    // Clear all selections, returns the new value to emit
    const clearAll = (): SelectableValue => {
        return props.multiple ? [] : null;
    };

    return {
        normalizedOptions,
        selectedValues,
        hasValue,
        getOptionLabel,
        getOptionValue,
        getLabel,
        isSelected,
        toggleOption,
        removeValue,
        clearAll,
    };
}
