import { describe, it, expect } from 'vitest';
import { isValidEmail } from '@/utils/validate/isValidEmail';

describe('isValidEmail', () => {
    // Happy paths - valid emails
    it('returns true for standard email', () => {
        expect(isValidEmail('user@example.com')).toBe(true);
    });

    it('returns true for email with subdomain', () => {
        expect(isValidEmail('user@mail.example.com')).toBe(true);
    });

    it('returns true for email with plus addressing', () => {
        expect(isValidEmail('user+tag@example.com')).toBe(true);
    });

    it('returns true for email with dots in local part', () => {
        expect(isValidEmail('first.last@example.com')).toBe(true);
    });

    it('returns true for email with numbers', () => {
        expect(isValidEmail('user123@example123.com')).toBe(true);
    });

    it('returns true for email with hyphens in domain', () => {
        expect(isValidEmail('user@my-company.com')).toBe(true);
    });

    // Bad paths - invalid emails
    it('returns false for empty string', () => {
        expect(isValidEmail('')).toBe(false);
    });

    it('returns false for null', () => {
        expect(isValidEmail(null as unknown as string)).toBe(false);
    });

    it('returns false for undefined', () => {
        expect(isValidEmail(undefined as unknown as string)).toBe(false);
    });

    it('returns false for string without @', () => {
        expect(isValidEmail('userexample.com')).toBe(false);
    });

    it('returns false for string without domain', () => {
        expect(isValidEmail('user@')).toBe(false);
    });

    it('returns false for string without local part', () => {
        expect(isValidEmail('@example.com')).toBe(false);
    });

    it('returns false for string without TLD', () => {
        expect(isValidEmail('user@example')).toBe(false);
    });

    it('returns false for email with spaces', () => {
        expect(isValidEmail('user @example.com')).toBe(false);
        expect(isValidEmail('user@ example.com')).toBe(false);
    });

    it('returns false for multiple @ symbols', () => {
        expect(isValidEmail('user@@example.com')).toBe(false);
        expect(isValidEmail('user@example@com')).toBe(false);
    });
});
