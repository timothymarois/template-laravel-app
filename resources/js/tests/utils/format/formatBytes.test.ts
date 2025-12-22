import { describe, it, expect } from 'vitest';
import { formatBytes } from '@/utils/format/formatBytes';

describe('formatBytes', () => {
    // Happy paths
    it('formats 0 bytes correctly', () => {
        expect(formatBytes(0)).toBe('0 Bytes');
    });

    it('formats bytes correctly', () => {
        expect(formatBytes(500)).toBe('500.00 Bytes');
    });

    it('formats kilobytes correctly', () => {
        expect(formatBytes(1024)).toBe('1.00 KB');
        expect(formatBytes(1536)).toBe('1.50 KB');
    });

    it('formats megabytes correctly', () => {
        expect(formatBytes(1048576)).toBe('1.00 MB');
        expect(formatBytes(1572864)).toBe('1.50 MB');
    });

    it('formats gigabytes correctly', () => {
        expect(formatBytes(1073741824)).toBe('1.00 GB');
    });

    it('formats terabytes correctly', () => {
        expect(formatBytes(1099511627776)).toBe('1.00 TB');
    });

    it('respects custom decimal places', () => {
        expect(formatBytes(1536, 0)).toBe('2 KB');
        expect(formatBytes(1536, 1)).toBe('1.5 KB');
        expect(formatBytes(1536, 3)).toBe('1.500 KB');
    });

    it('handles negative bytes by using absolute value', () => {
        expect(formatBytes(-1024)).toBe('1.00 KB');
    });

    // Bad paths
    it('returns empty string for NaN', () => {
        expect(formatBytes(NaN)).toBe('');
    });

    it('returns empty string for undefined', () => {
        expect(formatBytes(undefined as unknown as number)).toBe('');
    });

    it('returns empty string for null', () => {
        expect(formatBytes(null as unknown as number)).toBe('');
    });

    it('returns empty string for non-numeric values', () => {
        expect(formatBytes('abc' as unknown as number)).toBe('');
    });
});
