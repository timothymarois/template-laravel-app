import { describe, it, expect } from 'vitest';
import { formatYmdDate } from '@/utils/format/formatYmdDate';

describe('formatYmdDate', () => {
    // Happy paths
    it('converts YYYY-MM-DD to MM/DD/YYYY', () => {
        expect(formatYmdDate('2024-01-15')).toBe('01/15/2024');
        expect(formatYmdDate('2024-12-31')).toBe('12/31/2024');
    });

    it('handles single-digit months and days with leading zeros', () => {
        expect(formatYmdDate('2024-01-01')).toBe('01/01/2024');
        expect(formatYmdDate('2024-09-05')).toBe('09/05/2024');
    });

    it('handles different years', () => {
        expect(formatYmdDate('1999-06-15')).toBe('06/15/1999');
        expect(formatYmdDate('2030-11-20')).toBe('11/20/2030');
    });

    // Bad paths
    it('returns empty string for empty input', () => {
        expect(formatYmdDate('')).toBe('');
    });

    it('returns empty string for invalid format', () => {
        expect(formatYmdDate('01/15/2024')).toBe(''); // Wrong format
        expect(formatYmdDate('2024-1-15')).toBe(''); // Missing leading zero
        expect(formatYmdDate('2024/01/15')).toBe(''); // Wrong separator
    });

    it('returns empty string for non-string input', () => {
        expect(formatYmdDate(null as unknown as string)).toBe('');
        expect(formatYmdDate(undefined as unknown as string)).toBe('');
        expect(formatYmdDate(20240115 as unknown as string)).toBe('');
    });

    it('returns empty string for partial dates', () => {
        expect(formatYmdDate('2024-01')).toBe('');
        expect(formatYmdDate('2024')).toBe('');
    });
});
