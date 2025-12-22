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
