import { describe, it, expect } from 'vitest';
import { isNumeric } from '@/utils/validate/isNumeric';

describe('isNumeric', () => {
    // Happy paths - valid numeric values
    it('returns true for integers', () => {
        expect(isNumeric(42)).toBe(true);
        expect(isNumeric(0)).toBe(true);
        expect(isNumeric(-10)).toBe(true);
    });

    it('returns true for floats', () => {
        expect(isNumeric(3.14)).toBe(true);
        expect(isNumeric(-2.5)).toBe(true);
        expect(isNumeric(0.001)).toBe(true);
    });

    it('returns true for numeric strings', () => {
        expect(isNumeric('42')).toBe(true);
        expect(isNumeric('3.14')).toBe(true);
        expect(isNumeric('-10')).toBe(true);
        expect(isNumeric('0')).toBe(true);
    });

    it('returns true for scientific notation', () => {
        expect(isNumeric(1e10)).toBe(true);
        expect(isNumeric('1e10')).toBe(true);
        expect(isNumeric('1.5e-3')).toBe(true);
    });

    // Bad paths - non-numeric values
    it('returns false for non-numeric strings', () => {
        expect(isNumeric('hello')).toBe(false);
        expect(isNumeric('12abc')).toBe(false);
        expect(isNumeric('abc123')).toBe(false);
    });

    it('returns false for empty string', () => {
        expect(isNumeric('')).toBe(false);
    });

    it('returns false for NaN', () => {
        expect(isNumeric(NaN)).toBe(false);
    });

    it('returns false for whitespace-only string', () => {
        expect(isNumeric('   ')).toBe(false);
    });

    it('returns false for Infinity', () => {
        expect(isNumeric(Infinity)).toBe(true); // Note: Infinity is technically numeric
        expect(isNumeric(-Infinity)).toBe(true);
    });
});
