import { describe, it, expect } from 'vitest';
import { isValidURL } from '@/utils/validate/isValidURL';

describe('isValidURL', () => {
    // Happy paths - valid URLs
    it('returns true for http URL', () => {
        expect(isValidURL('http://example.com')).toBe(true);
    });

    it('returns true for https URL', () => {
        expect(isValidURL('https://example.com')).toBe(true);
    });

    it('returns true for URL with path', () => {
        expect(isValidURL('https://example.com/path/to/page')).toBe(true);
    });

    it('returns true for URL with query parameters', () => {
        expect(isValidURL('https://example.com/search?q=test&page=1')).toBe(true);
    });

    it('returns true for URL with hash fragment', () => {
        expect(isValidURL('https://example.com/page#section')).toBe(true);
    });

    it('returns true for URL with port', () => {
        expect(isValidURL('https://example.com:8080')).toBe(true);
    });

    it('returns true for URL with subdomain', () => {
        expect(isValidURL('https://www.example.com')).toBe(true);
        expect(isValidURL('https://api.v2.example.com')).toBe(true);
    });

    // Bad paths - invalid URLs
    it('returns false for URL without protocol', () => {
        expect(isValidURL('example.com')).toBe(false);
    });

    it('returns false for non-http/https protocols', () => {
        expect(isValidURL('ftp://example.com')).toBe(false);
        expect(isValidURL('file:///path/to/file')).toBe(false);
        expect(isValidURL('javascript:void(0)')).toBe(false);
    });

    it('returns false for URL with trailing dot in hostname', () => {
        expect(isValidURL('https://example.')).toBe(false);
    });

    it('returns false for URL without TLD', () => {
        expect(isValidURL('https://localhost')).toBe(false);
    });

    it('returns false for empty string', () => {
        expect(isValidURL('')).toBe(false);
    });

    it('returns false for malformed URLs', () => {
        expect(isValidURL('not a url')).toBe(false);
        expect(isValidURL('http://')).toBe(false);
    });

    it('returns false for null', () => {
        expect(isValidURL(null as unknown as string)).toBe(false);
    });

    it('returns false for undefined', () => {
        expect(isValidURL(undefined as unknown as string)).toBe(false);
    });
});
