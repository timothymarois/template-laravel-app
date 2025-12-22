import { describe, it, expect } from 'vitest';
import { formatSlug } from '@/utils/format/formatSlug';

describe('formatSlug', () => {
    // Happy paths
    it('converts spaces to hyphens', () => {
        expect(formatSlug('Hello World')).toBe('hello-world');
        expect(formatSlug('My Blog Post')).toBe('my-blog-post');
    });

    it('converts to lowercase', () => {
        expect(formatSlug('HELLO')).toBe('hello');
        expect(formatSlug('HeLLo WoRLD')).toBe('hello-world');
    });

    it('handles accented characters', () => {
        expect(formatSlug('café')).toBe('cafe');
        expect(formatSlug('résumé')).toBe('resume');
        expect(formatSlug('naïve')).toBe('naive');
    });

    it('removes special characters', () => {
        expect(formatSlug('Hello, World!')).toBe('hello-world');
        expect(formatSlug('What?!')).toBe('what');
    });

    it('collapses multiple hyphens', () => {
        expect(formatSlug('Hello   World')).toBe('hello-world');
        expect(formatSlug('Hello---World')).toBe('hello-world');
    });

    it('trims leading and trailing hyphens', () => {
        expect(formatSlug('  Hello World  ')).toBe('hello-world');
        expect(formatSlug('---Hello---')).toBe('hello');
    });

    it('handles numbers', () => {
        expect(formatSlug('Post 123')).toBe('post-123');
        expect(formatSlug('2024 Review')).toBe('2024-review');
    });

    it('returns empty string for empty input', () => {
        expect(formatSlug('')).toBe('');
    });

    // Bad paths
    it('returns "null" for null input', () => {
        expect(formatSlug(null as unknown as string)).toBe('null');
    });

    it('returns "undefined" for undefined input', () => {
        expect(formatSlug(undefined as unknown as string)).toBe('undefined');
    });

    it('handles strings with only special characters', () => {
        expect(formatSlug('!@#$%')).toBe('');
    });
});
