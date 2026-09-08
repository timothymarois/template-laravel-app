import { describe, it, expect } from 'vitest';
import { formatDate } from '@/utils/format/formatDate';

describe('formatDate', () => {
    // Happy paths
    it('formats UTC date string to localized date', () => {
        expect(formatDate('2024-07-22T12:00:00Z')).toBe('7/22/2024');
    });

    it('formats Date object to localized date', () => {
        const date = new Date('2024-07-22T12:00:00Z');
        expect(formatDate(date)).toBe('7/22/2024');
    });

    it('handles date string without Z suffix', () => {
        expect(formatDate('2024-07-22T12:00:00')).toBe('7/22/2024');
    });

    it('converts to different timezone', () => {
        // Midnight UTC on July 22 is still July 21 in Pacific time
        const result = formatDate('2024-07-22T00:00:00Z', 'America/Los_Angeles');
        expect(result).toBe('7/21/2024');
    });

    it('uses different locale', () => {
        const result = formatDate('2024-07-22T12:00:00Z', 'UTC', 'en-GB');
        expect(result).toBe('22/07/2024');
    });

    // Bad paths
    it('returns empty string for empty string', () => {
        expect(formatDate('')).toBe('');
    });

    it('returns empty string for null', () => {
        expect(formatDate(null as unknown as string)).toBe('');
    });

    it('returns empty string for undefined', () => {
        expect(formatDate(undefined as unknown as string)).toBe('');
    });

    it('returns empty string for invalid date string', () => {
        expect(formatDate('not-a-date')).toBe('');
        expect(formatDate('2024-99-99')).toBe('');
    });
});

describe('formatDate — time zones', () => {
    it('does not shift a date-only value into a western zone', () => {
        // 2026-01-01 read as UTC midnight and rendered in New York used to become
        // 12/31/2025. A date-only value is a calendar date, not an instant.
        expect(formatDate('2026-01-01', 'America/New_York')).toBe('1/1/2026');
    });

    it.each(['UTC', 'America/New_York', 'Asia/Tokyo', 'Australia/Sydney', 'Pacific/Kiritimati'])(
        'renders a date-only value identically in %s',
        (zone) => {
            expect(formatDate('2026-01-01', zone)).toBe('1/1/2026');
        },
    );

    it('does shift a real instant, which is the correct behaviour', () => {
        // 23:30 UTC is still the 1st in UTC and the 1st in New York (18:30),
        // but the 2nd in Tokyo (08:30).
        expect(formatDate('2026-01-01T23:30:00Z', 'UTC')).toBe('1/1/2026');
        expect(formatDate('2026-01-01T23:30:00Z', 'Asia/Tokyo')).toBe('1/2/2026');
    });

    it('falls back to UTC instead of throwing on an unknown zone', () => {
        expect(() => formatDate('2026-01-01T12:00:00Z', 'America/Nowhere')).not.toThrow();
        expect(formatDate('2026-01-01T12:00:00Z', 'America/Nowhere')).toBe('1/1/2026');
    });

    it('honours the locale independently of the zone', () => {
        expect(formatDate('2026-01-02T12:00:00Z', 'UTC', 'en-GB')).toBe('02/01/2026');
    });
});
