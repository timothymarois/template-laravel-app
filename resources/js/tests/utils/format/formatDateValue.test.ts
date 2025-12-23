import { describe, it, expect } from 'vitest';
import { formatDateValue } from '@/utils/format/formatDateValue';

// Mock DateValue object (from @internationalized/date)
const createDateValue = (year: number, month: number, day: number) => ({
    year,
    month,
    day,
    calendar: { identifier: 'gregory' },
    era: 'AD',
});

describe('formatDateValue', () => {
    // Happy paths - short format (default)
    it('formats DateValue to short format (MM/DD/YYYY)', () => {
        const date = createDateValue(2024, 7, 22);
        expect(formatDateValue(date)).toBe('07/22/2024');
    });

    it('pads single digit month with leading zero', () => {
        const date = createDateValue(2024, 1, 15);
        expect(formatDateValue(date)).toBe('01/15/2024');
    });

    it('pads single digit day with leading zero', () => {
        const date = createDateValue(2024, 12, 5);
        expect(formatDateValue(date)).toBe('12/05/2024');
    });

    it('handles end of year date', () => {
        const date = createDateValue(2024, 12, 31);
        expect(formatDateValue(date)).toBe('12/31/2024');
    });

    it('handles beginning of year date', () => {
        const date = createDateValue(2024, 1, 1);
        expect(formatDateValue(date)).toBe('01/01/2024');
    });

    // Long format
    it('formats DateValue to long format', () => {
        const date = createDateValue(2024, 7, 22);
        expect(formatDateValue(date, 'long')).toBe('July 22, 2024');
    });

    it('formats DateValue to long format with different month', () => {
        const date = createDateValue(2024, 1, 1);
        expect(formatDateValue(date, 'long')).toBe('January 1, 2024');
    });

    it('formats DateValue to long format with en-GB locale', () => {
        const date = createDateValue(2024, 7, 22);
        expect(formatDateValue(date, 'long', 'en-GB')).toBe('22 July 2024');
    });

    // Edge cases
    it('returns empty string for null', () => {
        expect(formatDateValue(null)).toBe('');
    });

    it('returns empty string for undefined', () => {
        expect(formatDateValue(undefined)).toBe('');
    });

    // Different years
    it('handles dates in the past', () => {
        const date = createDateValue(1999, 12, 31);
        expect(formatDateValue(date)).toBe('12/31/1999');
    });

    it('handles dates in the future', () => {
        const date = createDateValue(2099, 6, 15);
        expect(formatDateValue(date)).toBe('06/15/2099');
    });
});
