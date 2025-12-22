import { describe, it, expect } from 'vitest';
import { formatValidURL } from '@/utils/format/formatValidURL';

describe('formatValidURL', () => {
    // Happy paths
    it('adds http protocol to bare domain', () => {
        expect(formatValidURL('google.com')).toBe('http://google.com');
        expect(formatValidURL('example.org')).toBe('http://example.org');
    });

    it('preserves existing http protocol', () => {
        expect(formatValidURL('http://google.com')).toBe('http://google.com');
    });

    it('preserves existing https protocol', () => {
        expect(formatValidURL('https://google.com')).toBe('https://google.com');
    });

    it('forces https when requested', () => {
        expect(formatValidURL('google.com', true)).toBe('https://google.com');
        expect(formatValidURL('http://google.com', true)).toBe('https://google.com');
    });

    it('lowercases hostname', () => {
        expect(formatValidURL('GOOGLE.COM')).toBe('http://google.com');
        expect(formatValidURL('Google.Com')).toBe('http://google.com');
    });

    it('preserves path and query parameters', () => {
        expect(formatValidURL('google.com/search?q=test')).toBe('http://google.com/search?q=test');
        expect(formatValidURL('example.com/path/to/page')).toBe('http://example.com/path/to/page');
    });

    it('preserves hash fragments', () => {
        expect(formatValidURL('example.com#section')).toBe('http://example.com#section');
    });

    it('trims whitespace', () => {
        expect(formatValidURL('  google.com  ')).toBe('http://google.com');
    });

    it('handles case-insensitive protocol', () => {
        expect(formatValidURL('HTTP://google.com')).toBe('http://google.com');
        expect(formatValidURL('HTTPS://google.com')).toBe('https://google.com');
    });

    // Bad paths
    it('returns empty string for empty input', () => {
        expect(formatValidURL('')).toBe('');
    });

    it('returns empty string for null', () => {
        expect(formatValidURL(null as unknown as string)).toBe('');
    });

    it('returns empty string for undefined', () => {
        expect(formatValidURL(undefined as unknown as string)).toBe('');
    });
});
