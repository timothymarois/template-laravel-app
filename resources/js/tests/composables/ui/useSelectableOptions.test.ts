import { describe, it, expect } from 'vitest';
import { ref } from 'vue';
import { useSelectableOptions } from '@/composables/ui/useSelectableOptions';

describe('useSelectableOptions', () => {
    describe('normalizedOptions', () => {
        it('normalizes string options to {label, value} objects', () => {
            const { normalizedOptions } = useSelectableOptions({
                modelValue: ref(null),
                options: ref(['apple', 'banana', 'cherry']),
            });

            expect(normalizedOptions.value).toEqual([
                { label: 'apple', value: 'apple' },
                { label: 'banana', value: 'banana' },
                { label: 'cherry', value: 'cherry' },
            ]);
        });

        it('normalizes number options to {label, value} objects', () => {
            const { normalizedOptions } = useSelectableOptions({
                modelValue: ref(null),
                options: ref([1, 2, 3]),
            });

            expect(normalizedOptions.value).toEqual([
                { label: '1', value: 1 },
                { label: '2', value: 2 },
                { label: '3', value: 3 },
            ]);
        });

        it('passes through object options unchanged', () => {
            const options = [
                { label: 'Apple', value: 'apple' },
                { label: 'Banana', value: 'banana' },
            ];
            const { normalizedOptions } = useSelectableOptions({
                modelValue: ref(null),
                options: ref(options),
            });

            expect(normalizedOptions.value).toEqual(options);
        });

        it('handles mixed option types', () => {
            const { normalizedOptions } = useSelectableOptions({
                modelValue: ref(null),
                options: ref([
                    'simple',
                    42,
                    { label: 'Complex', value: 'complex' },
                ]),
            });

            expect(normalizedOptions.value).toEqual([
                { label: 'simple', value: 'simple' },
                { label: '42', value: 42 },
                { label: 'Complex', value: 'complex' },
            ]);
        });

        it('is reactive to options changes', () => {
            const options = ref(['a', 'b']);
            const { normalizedOptions } = useSelectableOptions({
                modelValue: ref(null),
                options,
            });

            expect(normalizedOptions.value).toHaveLength(2);

            options.value = ['x', 'y', 'z'];
            expect(normalizedOptions.value).toHaveLength(3);
            expect(normalizedOptions.value[2]).toEqual({ label: 'z', value: 'z' });
        });
    });

    describe('selectedValues', () => {
        it('returns empty array for null modelValue', () => {
            const { selectedValues } = useSelectableOptions({
                modelValue: ref(null),
                options: ref(['a', 'b']),
            });

            expect(selectedValues.value).toEqual([]);
        });

        it('returns empty array for undefined modelValue', () => {
            const { selectedValues } = useSelectableOptions({
                modelValue: ref(undefined),
                options: ref(['a', 'b']),
            });

            expect(selectedValues.value).toEqual([]);
        });

        it('returns empty array for empty string modelValue', () => {
            const { selectedValues } = useSelectableOptions({
                modelValue: ref(''),
                options: ref(['a', 'b']),
            });

            expect(selectedValues.value).toEqual([]);
        });

        it('wraps single value in array', () => {
            const { selectedValues } = useSelectableOptions({
                modelValue: ref('apple'),
                options: ref(['apple', 'banana']),
            });

            expect(selectedValues.value).toEqual(['apple']);
        });

        it('wraps single number value in array', () => {
            const { selectedValues } = useSelectableOptions({
                modelValue: ref(42),
                options: ref([1, 2, 42]),
            });

            expect(selectedValues.value).toEqual([42]);
        });

        it('returns array values as-is', () => {
            const { selectedValues } = useSelectableOptions({
                modelValue: ref(['apple', 'banana']),
                options: ref(['apple', 'banana', 'cherry']),
                multiple: true,
            });

            expect(selectedValues.value).toEqual(['apple', 'banana']);
        });

        it('filters out empty/null/undefined values from array', () => {
            const { selectedValues } = useSelectableOptions({
                modelValue: ref(['apple', '', null, undefined, 'banana'] as any),
                options: ref(['apple', 'banana', 'cherry']),
                multiple: true,
            });

            expect(selectedValues.value).toEqual(['apple', 'banana']);
        });

        it('is reactive to modelValue changes', () => {
            const modelValue = ref<string | null>('apple');
            const { selectedValues } = useSelectableOptions({
                modelValue,
                options: ref(['apple', 'banana']),
            });

            expect(selectedValues.value).toEqual(['apple']);

            modelValue.value = 'banana';
            expect(selectedValues.value).toEqual(['banana']);

            modelValue.value = null;
            expect(selectedValues.value).toEqual([]);
        });
    });

    describe('hasValue', () => {
        it('returns false when no value is selected', () => {
            const { hasValue } = useSelectableOptions({
                modelValue: ref(null),
                options: ref(['a', 'b']),
            });

            expect(hasValue.value).toBe(false);
        });

        it('returns true when a single value is selected', () => {
            const { hasValue } = useSelectableOptions({
                modelValue: ref('a'),
                options: ref(['a', 'b']),
            });

            expect(hasValue.value).toBe(true);
        });

        it('returns true when multiple values are selected', () => {
            const { hasValue } = useSelectableOptions({
                modelValue: ref(['a', 'b']),
                options: ref(['a', 'b', 'c']),
                multiple: true,
            });

            expect(hasValue.value).toBe(true);
        });

        it('returns false for empty array', () => {
            const { hasValue } = useSelectableOptions({
                modelValue: ref([]),
                options: ref(['a', 'b']),
                multiple: true,
            });

            expect(hasValue.value).toBe(false);
        });
    });

    describe('getOptionLabel', () => {
        it('returns string option as label', () => {
            const { getOptionLabel } = useSelectableOptions({
                modelValue: ref(null),
                options: ref([]),
            });

            expect(getOptionLabel('apple')).toBe('apple');
        });

        it('returns stringified number as label', () => {
            const { getOptionLabel } = useSelectableOptions({
                modelValue: ref(null),
                options: ref([]),
            });

            expect(getOptionLabel(42)).toBe('42');
        });

        it('returns label property from object', () => {
            const { getOptionLabel } = useSelectableOptions({
                modelValue: ref(null),
                options: ref([]),
            });

            expect(getOptionLabel({ label: 'Apple', value: 'apple' })).toBe('Apple');
        });

        it('uses custom optionLabel key', () => {
            const { getOptionLabel } = useSelectableOptions({
                modelValue: ref(null),
                options: ref([]),
                optionLabel: 'name',
            });

            expect(getOptionLabel({ name: 'Apple', id: 'apple' } as any)).toBe('Apple');
        });

        it('falls back to label property when custom key is missing', () => {
            const { getOptionLabel } = useSelectableOptions({
                modelValue: ref(null),
                options: ref([]),
                optionLabel: 'name',
            });

            expect(getOptionLabel({ label: 'Fallback', value: 'test' })).toBe('Fallback');
        });

        it('falls back to stringified value when no label', () => {
            const { getOptionLabel } = useSelectableOptions({
                modelValue: ref(null),
                options: ref([]),
            });

            expect(getOptionLabel({ value: 'test-value' } as any)).toBe('test-value');
        });
    });

    describe('getOptionValue', () => {
        it('returns string option as value', () => {
            const { getOptionValue } = useSelectableOptions({
                modelValue: ref(null),
                options: ref([]),
            });

            expect(getOptionValue('apple')).toBe('apple');
        });

        it('returns number option as value', () => {
            const { getOptionValue } = useSelectableOptions({
                modelValue: ref(null),
                options: ref([]),
            });

            expect(getOptionValue(42)).toBe(42);
        });

        it('returns value property from object', () => {
            const { getOptionValue } = useSelectableOptions({
                modelValue: ref(null),
                options: ref([]),
            });

            expect(getOptionValue({ label: 'Apple', value: 'apple' })).toBe('apple');
        });

        it('uses custom optionValue key', () => {
            const { getOptionValue } = useSelectableOptions({
                modelValue: ref(null),
                options: ref([]),
                optionValue: 'id',
            });

            expect(getOptionValue({ name: 'Apple', id: 123 } as any)).toBe(123);
        });

        it('falls back to value property when custom key is missing', () => {
            const { getOptionValue } = useSelectableOptions({
                modelValue: ref(null),
                options: ref([]),
                optionValue: 'id',
            });

            expect(getOptionValue({ label: 'Test', value: 'fallback' })).toBe('fallback');
        });
    });

    describe('getLabel', () => {
        it('returns label for existing value', () => {
            const { getLabel } = useSelectableOptions({
                modelValue: ref(null),
                options: ref([
                    { label: 'Apple', value: 'apple' },
                    { label: 'Banana', value: 'banana' },
                ]),
            });

            expect(getLabel('apple')).toBe('Apple');
            expect(getLabel('banana')).toBe('Banana');
        });

        it('returns stringified value when not found', () => {
            const { getLabel } = useSelectableOptions({
                modelValue: ref(null),
                options: ref([{ label: 'Apple', value: 'apple' }]),
            });

            expect(getLabel('unknown')).toBe('unknown');
            expect(getLabel(123)).toBe('123');
        });

        it('works with number values', () => {
            const { getLabel } = useSelectableOptions({
                modelValue: ref(null),
                options: ref([
                    { label: 'One', value: 1 },
                    { label: 'Two', value: 2 },
                ]),
            });

            expect(getLabel(1)).toBe('One');
            expect(getLabel(2)).toBe('Two');
        });
    });

    describe('isSelected', () => {
        it('returns true for selected single value', () => {
            const { isSelected } = useSelectableOptions({
                modelValue: ref('apple'),
                options: ref(['apple', 'banana']),
            });

            expect(isSelected('apple')).toBe(true);
            expect(isSelected('banana')).toBe(false);
        });

        it('returns true for values in selected array', () => {
            const { isSelected } = useSelectableOptions({
                modelValue: ref(['apple', 'cherry']),
                options: ref(['apple', 'banana', 'cherry']),
                multiple: true,
            });

            expect(isSelected('apple')).toBe(true);
            expect(isSelected('banana')).toBe(false);
            expect(isSelected('cherry')).toBe(true);
        });

        it('returns false when nothing is selected', () => {
            const { isSelected } = useSelectableOptions({
                modelValue: ref(null),
                options: ref(['apple', 'banana']),
            });

            expect(isSelected('apple')).toBe(false);
        });
    });

    describe('toggleOption - single mode', () => {
        it('selects unselected option', () => {
            const { toggleOption } = useSelectableOptions({
                modelValue: ref(null),
                options: ref([{ label: 'Apple', value: 'apple' }]),
                multiple: false,
            });

            const result = toggleOption({ label: 'Apple', value: 'apple' });
            expect(result).toBe('apple');
        });

        it('deselects selected option', () => {
            const { toggleOption } = useSelectableOptions({
                modelValue: ref('apple'),
                options: ref([{ label: 'Apple', value: 'apple' }]),
                multiple: false,
            });

            const result = toggleOption({ label: 'Apple', value: 'apple' });
            expect(result).toBeNull();
        });

        it('works with string options', () => {
            const { toggleOption } = useSelectableOptions({
                modelValue: ref(null),
                options: ref(['apple', 'banana']),
                multiple: false,
            });

            expect(toggleOption('apple')).toBe('apple');
        });

        it('works with number options', () => {
            const { toggleOption } = useSelectableOptions({
                modelValue: ref(null),
                options: ref([1, 2, 3]),
                multiple: false,
            });

            expect(toggleOption(2)).toBe(2);
        });
    });

    describe('toggleOption - multiple mode', () => {
        it('adds unselected option to array', () => {
            const { toggleOption } = useSelectableOptions({
                modelValue: ref(['apple']),
                options: ref(['apple', 'banana', 'cherry']),
                multiple: true,
            });

            const result = toggleOption({ label: 'Banana', value: 'banana' });
            expect(result).toEqual(['apple', 'banana']);
        });

        it('removes selected option from array', () => {
            const { toggleOption } = useSelectableOptions({
                modelValue: ref(['apple', 'banana']),
                options: ref(['apple', 'banana', 'cherry']),
                multiple: true,
            });

            const result = toggleOption({ label: 'Apple', value: 'apple' });
            expect(result).toEqual(['banana']);
        });

        it('adds first option to empty selection', () => {
            const { toggleOption } = useSelectableOptions({
                modelValue: ref([]),
                options: ref(['apple', 'banana']),
                multiple: true,
            });

            const result = toggleOption('apple');
            expect(result).toEqual(['apple']);
        });

        it('returns empty array when removing last selection', () => {
            const { toggleOption } = useSelectableOptions({
                modelValue: ref(['apple']),
                options: ref(['apple', 'banana']),
                multiple: true,
            });

            const result = toggleOption('apple');
            expect(result).toEqual([]);
        });
    });

    describe('removeValue', () => {
        it('removes value from multiple selection', () => {
            const { removeValue } = useSelectableOptions({
                modelValue: ref(['apple', 'banana', 'cherry']),
                options: ref(['apple', 'banana', 'cherry']),
                multiple: true,
            });

            const result = removeValue('banana');
            expect(result).toEqual(['apple', 'cherry']);
        });

        it('returns empty array when removing last item', () => {
            const { removeValue } = useSelectableOptions({
                modelValue: ref(['apple']),
                options: ref(['apple', 'banana']),
                multiple: true,
            });

            const result = removeValue('apple');
            expect(result).toEqual([]);
        });

        it('returns null in single mode', () => {
            const { removeValue } = useSelectableOptions({
                modelValue: ref('apple'),
                options: ref(['apple', 'banana']),
                multiple: false,
            });

            const result = removeValue('apple');
            expect(result).toBeNull();
        });

        it('handles removing non-existent value gracefully', () => {
            const { removeValue } = useSelectableOptions({
                modelValue: ref(['apple', 'banana']),
                options: ref(['apple', 'banana', 'cherry']),
                multiple: true,
            });

            const result = removeValue('cherry');
            expect(result).toEqual(['apple', 'banana']);
        });
    });

    describe('clearAll', () => {
        it('returns empty array in multiple mode', () => {
            const { clearAll } = useSelectableOptions({
                modelValue: ref(['apple', 'banana']),
                options: ref(['apple', 'banana', 'cherry']),
                multiple: true,
            });

            expect(clearAll()).toEqual([]);
        });

        it('returns null in single mode', () => {
            const { clearAll } = useSelectableOptions({
                modelValue: ref('apple'),
                options: ref(['apple', 'banana']),
                multiple: false,
            });

            expect(clearAll()).toBeNull();
        });

        it('returns empty array when already empty in multiple mode', () => {
            const { clearAll } = useSelectableOptions({
                modelValue: ref([]),
                options: ref(['apple', 'banana']),
                multiple: true,
            });

            expect(clearAll()).toEqual([]);
        });

        it('returns null when already null in single mode', () => {
            const { clearAll } = useSelectableOptions({
                modelValue: ref(null),
                options: ref(['apple', 'banana']),
                multiple: false,
            });

            expect(clearAll()).toBeNull();
        });
    });

    describe('custom keys', () => {
        it('works with custom optionLabel and optionValue', () => {
            const options = ref([
                { name: 'Apple', id: 1 },
                { name: 'Banana', id: 2 },
                { name: 'Cherry', id: 3 },
            ]);

            const {
                normalizedOptions,
                getOptionLabel,
                getOptionValue,
                getLabel,
                isSelected,
                toggleOption,
            } = useSelectableOptions({
                modelValue: ref(1),
                options: options as any,
                optionLabel: 'name',
                optionValue: 'id',
            });

            // normalizedOptions should pass through objects
            expect(normalizedOptions.value).toEqual(options.value);

            // getOptionLabel should use 'name' key
            expect(getOptionLabel(options.value[0])).toBe('Apple');

            // getOptionValue should use 'id' key
            expect(getOptionValue(options.value[0])).toBe(1);

            // getLabel should find by id and return name
            expect(getLabel(1)).toBe('Apple');
            expect(getLabel(2)).toBe('Banana');

            // isSelected should work with id
            expect(isSelected(1)).toBe(true);
            expect(isSelected(2)).toBe(false);

            // toggleOption should work with id
            const result = toggleOption(options.value[1]);
            expect(result).toBe(2);
        });
    });

    describe('edge cases', () => {
        it('handles empty options array', () => {
            const { normalizedOptions, selectedValues, hasValue, getLabel } = useSelectableOptions({
                modelValue: ref('test'),
                options: ref([]),
            });

            expect(normalizedOptions.value).toEqual([]);
            expect(selectedValues.value).toEqual(['test']);
            expect(hasValue.value).toBe(true);
            expect(getLabel('test')).toBe('test');
        });

        it('handles 0 as valid value', () => {
            const { selectedValues, hasValue, isSelected } = useSelectableOptions({
                modelValue: ref(0),
                options: ref([0, 1, 2]),
            });

            expect(selectedValues.value).toEqual([0]);
            expect(hasValue.value).toBe(true);
            expect(isSelected(0)).toBe(true);
        });

        it('handles options with extra properties', () => {
            const { normalizedOptions, getOptionLabel } = useSelectableOptions({
                modelValue: ref(null),
                options: ref([
                    { label: 'Test', value: 'test', icon: 'star', disabled: true, extra: 'data' },
                ]),
            });

            expect(normalizedOptions.value[0]).toHaveProperty('icon', 'star');
            expect(normalizedOptions.value[0]).toHaveProperty('disabled', true);
            expect(normalizedOptions.value[0]).toHaveProperty('extra', 'data');
            expect(getOptionLabel(normalizedOptions.value[0])).toBe('Test');
        });
    });
});
