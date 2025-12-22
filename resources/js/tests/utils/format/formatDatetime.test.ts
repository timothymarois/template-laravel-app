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
