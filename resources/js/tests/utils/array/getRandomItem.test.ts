import { describe, it, expect, vi } from 'vitest';
import { getRandomItem } from '@/utils/array/getRandomItem';

describe('getRandomItem', () => {
    // Happy paths
    it('returns an item from the array', () => {
        const items = [1, 2, 3, 4, 5];
        const result = getRandomItem(items);
        expect(items).toContain(result);
    });

    it('returns the only item from single-element array', () => {
        expect(getRandomItem([42])).toBe(42);
        expect(getRandomItem(['only'])).toBe('only');
    });

    it('works with different types', () => {
        expect(getRandomItem(['a', 'b', 'c'])).toMatch(/^[abc]$/);
        expect(getRandomItem([true, false])).toBeTypeOf('boolean');
        expect(getRandomItem([{ id: 1 }, { id: 2 }])).toHaveProperty('id');
    });

    it('returns items with expected distribution', () => {
        // Mock Math.random to test specific indices
        vi.spyOn(Math, 'random').mockReturnValue(0);
        expect(getRandomItem([1, 2, 3])).toBe(1);

        vi.spyOn(Math, 'random').mockReturnValue(0.5);
        expect(getRandomItem([1, 2, 3])).toBe(2);

        vi.spyOn(Math, 'random').mockReturnValue(0.99);
        expect(getRandomItem([1, 2, 3])).toBe(3);

        vi.restoreAllMocks();
    });

    // Bad paths
    it('returns undefined for empty array', () => {
        expect(getRandomItem([])).toBeUndefined();
    });

    it('returns undefined for null', () => {
        expect(getRandomItem(null as unknown as unknown[])).toBeUndefined();
    });

    it('returns undefined for undefined', () => {
        expect(getRandomItem(undefined as unknown as unknown[])).toBeUndefined();
    });

    it('returns undefined for non-array input', () => {
        expect(getRandomItem('string' as unknown as unknown[])).toBeUndefined();
        expect(getRandomItem(123 as unknown as unknown[])).toBeUndefined();
        expect(getRandomItem({} as unknown as unknown[])).toBeUndefined();
    });
});
