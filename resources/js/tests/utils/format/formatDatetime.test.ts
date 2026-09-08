import { describe, it, expect } from 'vitest';
import { formatDatetime } from '@/utils/format/formatDatetime';

describe('formatDatetime', () => {
    // Happy paths
    it('formats UTC datetime string to localized datetime', () => {
        const result = formatDatetime('2024-07-22T16:30:00Z');
        expect(result).toMatch(/7\/22\/2024.*4:30.*PM/);
    });

    it('formats Date object to localized datetime', () => {
        const date = new Date('2024-07-22T16:30:00Z');
        const result = formatDatetime(date);
        expect(result).toMatch(/7\/22\/2024.*4:30.*PM/);
    });

    it('handles date string without Z suffix', () => {
        const result = formatDatetime('2024-07-22T16:30:00');
        expect(result).toMatch(/7\/22\/2024.*4:30.*PM/);
    });

    it('converts to different timezone', () => {
        // 4:30 PM UTC = 12:30 PM Eastern (during EDT)
        const result = formatDatetime('2024-07-22T16:30:00Z', 'America/New_York');
        expect(result).toMatch(/12:30.*PM/);
    });

    it('uses different locale', () => {
        const result = formatDatetime('2024-07-22T16:30:00Z', 'UTC', 'en-GB');
        expect(result).toMatch(/22\/07\/2024/);
    });

    it('formats morning time with AM', () => {
        const result = formatDatetime('2024-07-22T09:15:00Z');
        expect(result).toMatch(/9:15.*AM/);
    });

    // Bad paths
    it('returns empty string for empty string', () => {
        expect(formatDatetime('')).toBe('');
    });

    it('returns empty string for null', () => {
        expect(formatDatetime(null as unknown as string)).toBe('');
    });

    it('returns empty string for undefined', () => {
        expect(formatDatetime(undefined as unknown as string)).toBe('');
    });

    it('returns empty string for invalid date string', () => {
        expect(formatDatetime('not-a-date')).toBe('');
        expect(formatDatetime('2024-99-99')).toBe('');
    });
});

describe('formatDatetime — time zones', () => {
    it('converts a real instant into the viewer zone', () => {
        expect(formatDatetime('2026-01-01T23:30:00Z', 'UTC')).toBe('1/1/2026, 11:30 PM');
        expect(formatDatetime('2026-01-01T23:30:00Z', 'America/New_York')).toBe('1/1/2026, 6:30 PM');
        expect(formatDatetime('2026-01-01T23:30:00Z', 'Asia/Tokyo')).toBe('1/2/2026, 8:30 AM');
    });

    it('handles a US daylight-saving boundary', () => {
        // 2026-03-08 07:00 UTC is 02:00 EST; the US spring-forward is at 07:00 UTC.
        expect(formatDatetime('2026-03-08T06:59:00Z', 'America/New_York')).toBe('3/8/2026, 1:59 AM');
        expect(formatDatetime('2026-03-08T07:00:00Z', 'America/New_York')).toBe('3/8/2026, 3:00 AM');
    });

    it('does not shift a date-only value', () => {
        expect(formatDatetime('2026-01-01', 'America/New_York')).toBe('1/1/2026, 12:00 AM');
    });

    it('falls back to UTC instead of throwing on an unknown zone', () => {
        expect(() => formatDatetime('2026-01-01T12:00:00Z', 'Not/AZone')).not.toThrow();
        expect(formatDatetime('2026-01-01T12:00:00Z', 'Not/AZone')).toBe('1/1/2026, 12:00 PM');
    });

    it('accepts an ISO8601 offset, not only a trailing Z', () => {
        expect(formatDatetime('2026-01-01T23:30:00+00:00', 'UTC')).toBe('1/1/2026, 11:30 PM');
        expect(formatDatetime('2026-01-01T18:30:00-05:00', 'UTC')).toBe('1/1/2026, 11:30 PM');
    });
});
