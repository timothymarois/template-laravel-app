import { describe, it, expect } from 'vitest';
import { formatCurrency } from '@/utils/format/formatCurrency';

describe('formatCurrency', () => {
    // Happy paths
    it('formats positive numbers as USD by default', () => {
        expect(formatCurrency(100)).toBe('$100.00');
        expect(formatCurrency(1234.56)).toBe('$1,234.56');
    });

    it('formats negative numbers correctly', () => {
        expect(formatCurrency(-50)).toBe('-$50.00');
    });

    it('formats zero correctly', () => {
        expect(formatCurrency(0)).toBe('$0.00');
    });

    it('formats numeric strings correctly', () => {
        expect(formatCurrency('1234.56')).toBe('$1,234.56');
    });

    it('respects custom decimal places', () => {
        expect(formatCurrency(100, 0)).toBe('$100');
        expect(formatCurrency(100.5, 1)).toBe('$100.5');
        expect(formatCurrency(100, 3)).toBe('$100.000');
    });

    it('supports different currencies', () => {
        expect(formatCurrency(100, 2, 'EUR')).toBe('€100.00');
        expect(formatCurrency(100, 2, 'GBP')).toBe('£100.00');
    });

    it('supports custom invalid message', () => {
        expect(formatCurrency(null, 2, 'USD', 'N/A')).toBe('N/A');
    });

    // Bad paths
    it('returns Invalid for null', () => {
        expect(formatCurrency(null)).toBe('Invalid');
    });

    it('returns Invalid for undefined', () => {
        expect(formatCurrency(undefined)).toBe('Invalid');
    });

    it('returns Invalid for empty string', () => {
        expect(formatCurrency('')).toBe('Invalid');
    });

    it('returns Invalid for non-numeric strings', () => {
        expect(formatCurrency('abc')).toBe('Invalid');
        expect(formatCurrency('$100')).toBe('Invalid');
    });
});
