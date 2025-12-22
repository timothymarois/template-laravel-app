import { describe, it, expect } from 'vitest';
import { formatUSPhoneNumber } from '@/utils/format/formatUSPhoneNumber';

describe('formatUSPhoneNumber', () => {
    // Happy paths
    it('formats 10-digit phone number', () => {
        expect(formatUSPhoneNumber('5551234567')).toBe('(555) 123-4567');
    });

    it('formats phone number with existing formatting', () => {
        expect(formatUSPhoneNumber('555-123-4567')).toBe('(555) 123-4567');
        expect(formatUSPhoneNumber('(555) 123-4567')).toBe('(555) 123-4567');
    });

    it('formats numeric input', () => {
        expect(formatUSPhoneNumber(5551234567)).toBe('(555) 123-4567');
    });

    it('strips leading 1 from 11-digit numbers', () => {
        expect(formatUSPhoneNumber('15551234567')).toBe('(555) 123-4567');
        expect(formatUSPhoneNumber('1-555-123-4567')).toBe('(555) 123-4567');
    });

    it('adds country code when requested', () => {
        expect(formatUSPhoneNumber('5551234567', true)).toBe('+1 (555) 123-4567');
    });

    it('adds country code and strips leading 1', () => {
        expect(formatUSPhoneNumber('15551234567', true)).toBe('+1 (555) 123-4567');
    });

    // Bad paths
    it('returns empty string for empty input', () => {
        expect(formatUSPhoneNumber('')).toBe('');
    });

    it('returns empty string for null', () => {
        expect(formatUSPhoneNumber(null as unknown as string)).toBe('');
    });

    it('returns empty string for undefined', () => {
        expect(formatUSPhoneNumber(undefined as unknown as string)).toBe('');
    });

    it('returns empty string for too few digits', () => {
        expect(formatUSPhoneNumber('555123456')).toBe(''); // 9 digits
        expect(formatUSPhoneNumber('12345')).toBe('');
    });

    it('returns empty string for too many digits', () => {
        expect(formatUSPhoneNumber('555123456789')).toBe(''); // 12 digits
    });

    it('returns empty string for non-1 prefix with 11 digits', () => {
        expect(formatUSPhoneNumber('25551234567')).toBe(''); // 11 digits but starts with 2
    });
});
