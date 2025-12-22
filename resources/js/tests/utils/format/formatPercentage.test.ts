import { describe, it, expect } from 'vitest';
import { formatPercentage } from '@/utils/format/formatPercentage';

describe('formatPercentage', () => {
    // Happy paths
    it('formats decimal as percentage', () => {
        expect(formatPercentage(0.5)).toBe('50%');
        expect(formatPercentage(0.25)).toBe('25%');
        expect(formatPercentage(1)).toBe('100%');
    });

    it('formats zero correctly', () => {
        expect(formatPercentage(0)).toBe('0%');
    });

    it('formats values over 100% correctly', () => {
        expect(formatPercentage(1.5)).toBe('150%');
        expect(formatPercentage(2)).toBe('200%');
    });

    it('formats negative percentages correctly', () => {
        expect(formatPercentage(-0.25)).toBe('-25%');
    });

    it('respects decimal places parameter', () => {
        expect(formatPercentage(0.3333, 2)).toBe('33.33%');
        expect(formatPercentage(0.5, 1)).toBe('50.0%');
    });

    it('handles small decimals', () => {
        expect(formatPercentage(0.001)).toBe('0%');
        expect(formatPercentage(0.001, 1)).toBe('0.1%');
    });

    // Bad paths
    it('returns empty string for null', () => {
        expect(formatPercentage(null as unknown as number)).toBe('');
    });

    it('returns empty string for undefined', () => {
        expect(formatPercentage(undefined as unknown as number)).toBe('');
    });

    it('returns empty string for NaN', () => {
        expect(formatPercentage(NaN)).toBe('');
    });
});
