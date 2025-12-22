import { describe, it, expect } from 'vitest';
import { roundTo } from '@/utils/math/roundTo';

describe('roundTo', () => {
    // Happy paths
    it('rounds to 2 decimal places by default', () => {
        expect(roundTo(3.14159)).toBe(3.14);
        expect(roundTo(2.555)).toBe(2.56);
        expect(roundTo(1.001)).toBe(1);
    });

    it('rounds to specified decimal places', () => {
        expect(roundTo(3.14159, 0)).toBe(3);
        expect(roundTo(3.14159, 1)).toBe(3.1);
        expect(roundTo(3.14159, 3)).toBe(3.142);
        expect(roundTo(3.14159, 4)).toBe(3.1416);
    });

    it('rounds up correctly', () => {
        expect(roundTo(2.555, 2)).toBe(2.56);
        expect(roundTo(2.5, 0)).toBe(3);
    });

    it('rounds down correctly', () => {
        expect(roundTo(2.544, 2)).toBe(2.54);
        expect(roundTo(2.4, 0)).toBe(2);
    });

    it('handles negative numbers', () => {
        expect(roundTo(-3.14159, 2)).toBe(-3.14);
        expect(roundTo(-2.555, 2)).toBe(-2.55); // Note: rounds toward zero
    });

    it('handles zero', () => {
        expect(roundTo(0)).toBe(0);
        expect(roundTo(0, 5)).toBe(0);
    });

    it('handles whole numbers', () => {
        expect(roundTo(5, 2)).toBe(5);
        expect(roundTo(100, 0)).toBe(100);
    });

    it('handles floating point precision issues', () => {
        // Classic floating point issue: 0.1 + 0.2 !== 0.3
        expect(roundTo(0.1 + 0.2, 1)).toBe(0.3);
    });

    // Edge cases
    it('handles very small numbers', () => {
        expect(roundTo(0.0001, 4)).toBe(0.0001);
        expect(roundTo(0.0001, 2)).toBe(0);
    });

    it('handles very large numbers', () => {
        expect(roundTo(1234567.89, 1)).toBe(1234567.9);
    });

    it('returns NaN for NaN input', () => {
        expect(roundTo(NaN)).toBeNaN();
    });
});
