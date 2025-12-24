import { describe, it, expect, vi } from 'vitest';
import { useModelValue } from '@/composables/ui/useModelValue';

describe('useModelValue', () => {
    it('returns the modelValue from props', () => {
        const props = { modelValue: 'test' };
        const emit = vi.fn();

        const value = useModelValue<string>({ props, emit });

        expect(value.value).toBe('test');
    });

    it('emits update:modelValue when set', () => {
        const props = { modelValue: 'initial' };
        const emit = vi.fn();

        const value = useModelValue<string>({ props, emit });
        value.value = 'updated';

        expect(emit).toHaveBeenCalledWith('update:modelValue', 'updated');
    });

    it('works with boolean values', () => {
        const props = { modelValue: false };
        const emit = vi.fn();

        const value = useModelValue<boolean>({ props, emit });
        expect(value.value).toBe(false);

        value.value = true;
        expect(emit).toHaveBeenCalledWith('update:modelValue', true);
    });

    it('works with object values', () => {
        const obj = { id: 1, name: 'test' };
        const props = { modelValue: obj };
        const emit = vi.fn();

        const value = useModelValue<typeof obj>({ props, emit });
        expect(value.value).toEqual(obj);

        const newObj = { id: 2, name: 'updated' };
        value.value = newObj;
        expect(emit).toHaveBeenCalledWith('update:modelValue', newObj);
    });

    it('works with array values', () => {
        const arr = [1, 2, 3];
        const props = { modelValue: arr };
        const emit = vi.fn();

        const value = useModelValue<number[]>({ props, emit });
        expect(value.value).toEqual(arr);

        const newArr = [4, 5, 6];
        value.value = newArr;
        expect(emit).toHaveBeenCalledWith('update:modelValue', newArr);
    });

    it('handles undefined modelValue', () => {
        const props = { modelValue: undefined };
        const emit = vi.fn();

        const value = useModelValue<undefined>({ props, emit });
        expect(value.value).toBeUndefined();
    });

    it('handles null modelValue', () => {
        const props = { modelValue: null };
        const emit = vi.fn();

        const value = useModelValue<null>({ props, emit });
        expect(value.value).toBeNull();
    });

    it('supports custom prop names', () => {
        const props = { visible: true };
        const emit = vi.fn();

        const value = useModelValue<boolean, 'visible'>({ props, emit, name: 'visible' });
        expect(value.value).toBe(true);

        value.value = false;
        expect(emit).toHaveBeenCalledWith('update:visible', false);
    });
});
