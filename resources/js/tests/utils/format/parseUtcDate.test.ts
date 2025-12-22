import { describe, it, expect } from 'vitest';
import { parseUtcDate } from '@/utils/format/parseUtcDate';

describe('parseUtcDate', () => {
    // Happy paths
    it('parses ISO string with Z suffix', () => {
        const result = parseUtcDate('2024-07-22T16:30:00Z');
        expect(result).toBeInstanceOf(Date);
        expect(result?.toISOString()).toBe('2024-07-22T16:30:00.000Z');
    });

    it('parses ISO string without Z suffix and treats as UTC', () => {
        const result = parseUtcDate('2024-07-22T16:30:00');
        expect(result).toBeInstanceOf(Date);
        expect(result?.toISOString()).toBe('2024-07-22T16:30:00.000Z');
    });

    it('accepts Date object and returns it', () => {
        const input = new Date('2024-07-22T16:30:00Z');
        const result = parseUtcDate(input);
        expect(result).toBe(input);
    });

    it('parses date-only string', () => {
        const result = parseUtcDate('2024-07-22');
        expect(result).toBeInstanceOf(Date);
        expect(result?.getUTCFullYear()).toBe(2024);
        expect(result?.getUTCMonth()).toBe(6); // July is month 6 (0-indexed)
        expect(result?.getUTCDate()).toBe(22);
    });

    it('parses ISO string with milliseconds', () => {
        const result = parseUtcDate('2024-07-22T16:30:00.123Z');
        expect(result).toBeInstanceOf(Date);
        expect(result?.getUTCMilliseconds()).toBe(123);
    });

    // Bad paths
    it('returns null for empty string', () => {
        expect(parseUtcDate('')).toBeNull();
    });

    it('returns null for null', () => {
        expect(parseUtcDate(null as unknown as string)).toBeNull();
    });

    it('returns null for undefined', () => {
        expect(parseUtcDate(undefined as unknown as string)).toBeNull();
    });

    it('returns null for invalid date string', () => {
        expect(parseUtcDate('not-a-date')).toBeNull();
        expect(parseUtcDate('2024-99-99')).toBeNull();
    });

    it('returns null for non-string non-Date input', () => {
        expect(parseUtcDate(12345 as unknown as string)).toBeNull();
        expect(parseUtcDate({} as unknown as string)).toBeNull();
    });
});
