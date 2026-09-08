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

    it('keeps a numeric UTC offset instead of appending Z to it', () => {
        // Laravel's toIso8601String() ends `+00:00`. Appending `Z` produced
        // `...+00:00Z` — an invalid date, so the field rendered blank with nothing
        // thrown. Fixtures ending in `Z` never exercised this.
        const parsed = parseUtcDate('2026-08-16T21:20:05+00:00');

        expect(parsed).not.toBeNull();
        expect(parsed!.toISOString()).toBe('2026-08-16T21:20:05.000Z');
    });

    it('keeps a non-zero offset and converts it to the right instant', () => {
        const parsed = parseUtcDate('2026-08-16T21:20:05-04:00');

        expect(parsed!.toISOString()).toBe('2026-08-17T01:20:05.000Z');
    });

    it('still reads a zoneless string as UTC', () => {
        const parsed = parseUtcDate('2026-08-16T21:20:05');

        expect(parsed!.toISOString()).toBe('2026-08-16T21:20:05.000Z');
    });

    it('returns null for non-string non-Date input', () => {
        expect(parseUtcDate(12345 as unknown as string)).toBeNull();
        expect(parseUtcDate({} as unknown as string)).toBeNull();
    });
});
