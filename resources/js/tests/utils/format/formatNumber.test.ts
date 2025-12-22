import { describe, it, expect } from 'vitest';
import { formatNumber } from '@/utils/format/formatNumber';

describe('formatNumber', () => {
    // Happy paths
    it('formats integers with thousands separators', () => {
        expect(formatNumber(1000)).toBe('1,000');
        expect(formatNumber(1000000)).toBe('1,000,000');
    });

    it('formats small numbers without separators', () => {
        expect(formatNumber(100)).toBe('100');
        expect(formatNumber(999)).toBe('999');
    });

    it('formats zero correctly', () => {
        expect(formatNumber(0)).toBe('0');
    });

    it('formats negative numbers correctly', () => {
        expect(formatNumber(-1000)).toBe('-1,000');
    });

    it('respects decimal places parameter', () => {
        expect(formatNumber(1234.567, 2)).toBe('1,234.57');
        expect(formatNumber(1000, 2)).toBe('1,000.00');
    });

    it('formats numeric strings correctly', () => {
        expect(formatNumber('1234567')).toBe('1,234,567');
    });

    // Bad paths
    it('returns empty string for empty string input', () => {
        expect(formatNumber('')).toBe('');
    });

    it('returns empty string for null', () => {
        expect(formatNumber(null as unknown as number)).toBe('');
    });

    it('returns empty string for undefined', () => {
        expect(formatNumber(undefined as unknown as number)).toBe('');
    });

    it('returns Invalid for non-numeric strings', () => {
        expect(formatNumber('abc')).toBe('Invalid');
    });
});
