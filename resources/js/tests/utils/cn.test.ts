import { describe, it, expect } from 'vitest';
import { cn } from '@/utils/cn';

describe('cn', () => {
    // Happy paths
    it('combines multiple class strings', () => {
        expect(cn('foo', 'bar')).toBe('foo bar');
    });

    it('handles conditional classes', () => {
        expect(cn('base', true && 'active')).toBe('base active');
        expect(cn('base', false && 'active')).toBe('base');
    });

    it('handles array of classes', () => {
        expect(cn(['foo', 'bar'])).toBe('foo bar');
    });

    it('handles object notation', () => {
        expect(cn({ foo: true, bar: false })).toBe('foo');
        expect(cn({ foo: true, bar: true })).toBe('foo bar');
    });

    it('merges conflicting Tailwind classes', () => {
        // tailwind-merge should keep last conflicting class
        expect(cn('px-2', 'px-4')).toBe('px-4');
        expect(cn('text-red-500', 'text-blue-500')).toBe('text-blue-500');
        expect(cn('bg-red-500', 'bg-blue-500')).toBe('bg-blue-500');
    });

    it('handles Tailwind variants correctly', () => {
        expect(cn('hover:bg-red-500', 'hover:bg-blue-500')).toBe('hover:bg-blue-500');
    });

    it('preserves non-conflicting classes', () => {
        expect(cn('px-2', 'py-4')).toBe('px-2 py-4');
        expect(cn('text-sm', 'font-bold')).toBe('text-sm font-bold');
    });

    it('handles mixed input types', () => {
        expect(cn('foo', ['bar', 'baz'], { qux: true })).toBe('foo bar baz qux');
    });

    // Edge cases
    it('handles empty inputs', () => {
        expect(cn()).toBe('');
        expect(cn('')).toBe('');
        expect(cn('', '')).toBe('');
    });

    it('handles null and undefined', () => {
        expect(cn(null)).toBe('');
        expect(cn(undefined)).toBe('');
        expect(cn('foo', null, 'bar')).toBe('foo bar');
    });

    it('handles whitespace', () => {
        expect(cn('  foo  ', '  bar  ')).toBe('foo bar');
    });

    it('preserves duplicate non-Tailwind classes', () => {
        // cn uses clsx + tailwind-merge; it deduplicates Tailwind conflicts, not arbitrary classes
        expect(cn('foo', 'foo')).toBe('foo foo');
    });
});
