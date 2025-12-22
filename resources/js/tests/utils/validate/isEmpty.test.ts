import { describe, it, expect } from 'vitest';
import { isEmpty } from '@/utils/validate/isEmpty';

describe('isEmpty', () => {
    // Happy paths - empty values
    it('returns true for null', () => {
        expect(isEmpty(null)).toBe(true);
    });

    it('returns true for undefined', () => {
        expect(isEmpty(undefined)).toBe(true);
    });

    it('returns true for empty string', () => {
        expect(isEmpty('')).toBe(true);
    });

    it('returns true for empty array', () => {
        expect(isEmpty([])).toBe(true);
    });

    it('returns true for empty object', () => {
        expect(isEmpty({})).toBe(true);
    });

    // Happy paths - non-empty values
    it('returns false for non-empty string', () => {
        expect(isEmpty('hello')).toBe(false);
        expect(isEmpty(' ')).toBe(false); // whitespace is not empty
    });

    it('returns false for non-empty array', () => {
        expect(isEmpty([1, 2, 3])).toBe(false);
        expect(isEmpty([null])).toBe(false);
    });

    it('returns false for non-empty object', () => {
        expect(isEmpty({ key: 'value' })).toBe(false);
        expect(isEmpty({ key: null })).toBe(false);
    });

    // Edge cases
    it('returns false for numbers (including 0)', () => {
        expect(isEmpty(0)).toBe(false);
        expect(isEmpty(42)).toBe(false);
    });

    it('returns false for booleans', () => {
        expect(isEmpty(false)).toBe(false);
        expect(isEmpty(true)).toBe(false);
    });

    it('returns false for functions', () => {
        expect(isEmpty(() => {})).toBe(false);
    });
});
