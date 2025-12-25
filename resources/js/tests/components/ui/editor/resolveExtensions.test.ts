import { describe, it, expect } from 'vitest';
import { resolveExtensions } from '@/components/ui/editor/resolveExtensions';
import type { Extension } from '@tiptap/core';

// Mock extensions for testing (we only need objects that satisfy the Extension type structure)
const mockExtension = (name: string): Extension => ({ name }) as Extension;

const Document = mockExtension('Document');
const Paragraph = mockExtension('Paragraph');
const Text = mockExtension('Text');
const HardBreak = mockExtension('HardBreak');
const StarterKit = mockExtension('StarterKit');
const Placeholder = mockExtension('Placeholder');
const Underline = mockExtension('Underline');
const Image = mockExtension('Image');
const CustomExtension = mockExtension('CustomExtension');

const textOnlyDefaults = [Document, Paragraph, Text, HardBreak, Placeholder];
const fullDefaults = [StarterKit, Placeholder];

describe('resolveExtensions', () => {
    describe('default extensions only', () => {
        it('returns full defaults when textOnly is false', () => {
            const result = resolveExtensions({
                textOnlyDefaults,
                fullDefaults,
                textOnly: false,
            });

            expect(result).toEqual(fullDefaults);
        });

        it('returns textOnly defaults when textOnly is true', () => {
            const result = resolveExtensions({
                textOnlyDefaults,
                fullDefaults,
                textOnly: true,
            });

            expect(result).toEqual(textOnlyDefaults);
        });

        it('defaults textOnly to false when not specified', () => {
            const result = resolveExtensions({
                textOnlyDefaults,
                fullDefaults,
            });

            expect(result).toEqual(fullDefaults);
        });
    });

    describe('merging custom extensions', () => {
        it('merges custom extensions after full defaults', () => {
            const customExtensions = [Underline, Image];

            const result = resolveExtensions({
                customExtensions,
                textOnlyDefaults,
                fullDefaults,
                textOnly: false,
            });

            expect(result).toEqual([...fullDefaults, ...customExtensions]);
        });

        it('merges custom extensions after textOnly defaults', () => {
            const customExtensions = [Underline];

            const result = resolveExtensions({
                customExtensions,
                textOnlyDefaults,
                fullDefaults,
                textOnly: true,
            });

            expect(result).toEqual([...textOnlyDefaults, ...customExtensions]);
        });

        it('preserves order of custom extensions', () => {
            const customExtensions = [Underline, Image, CustomExtension];

            const result = resolveExtensions({
                customExtensions,
                textOnlyDefaults,
                fullDefaults,
            });

            expect(result).toEqual([StarterKit, Placeholder, Underline, Image, CustomExtension]);
        });
    });

    describe('replacing defaults', () => {
        it('returns only custom extensions when replaceDefaults is true', () => {
            const customExtensions = [Document, Paragraph, CustomExtension];

            const result = resolveExtensions({
                customExtensions,
                replaceDefaults: true,
                textOnlyDefaults,
                fullDefaults,
            });

            expect(result).toEqual(customExtensions);
        });

        it('ignores textOnly when replaceDefaults is true', () => {
            const customExtensions = [CustomExtension];

            const result = resolveExtensions({
                customExtensions,
                replaceDefaults: true,
                textOnlyDefaults,
                fullDefaults,
                textOnly: true,
            });

            expect(result).toEqual(customExtensions);
        });

        it('uses defaults when replaceDefaults is true but customExtensions is null', () => {
            const result = resolveExtensions({
                customExtensions: null,
                replaceDefaults: true,
                textOnlyDefaults,
                fullDefaults,
            });

            expect(result).toEqual(fullDefaults);
        });

        it('uses defaults when replaceDefaults is true but customExtensions is undefined', () => {
            const result = resolveExtensions({
                customExtensions: undefined,
                replaceDefaults: true,
                textOnlyDefaults,
                fullDefaults,
            });

            expect(result).toEqual(fullDefaults);
        });
    });

    describe('edge cases', () => {
        it('returns defaults when customExtensions is null', () => {
            const result = resolveExtensions({
                customExtensions: null,
                textOnlyDefaults,
                fullDefaults,
            });

            expect(result).toEqual(fullDefaults);
        });

        it('returns defaults when customExtensions is empty array', () => {
            const result = resolveExtensions({
                customExtensions: [],
                textOnlyDefaults,
                fullDefaults,
            });

            expect(result).toEqual(fullDefaults);
        });

        it('handles single custom extension', () => {
            const result = resolveExtensions({
                customExtensions: [Underline],
                textOnlyDefaults,
                fullDefaults,
            });

            expect(result).toEqual([...fullDefaults, Underline]);
        });

        it('defaults replaceDefaults to false', () => {
            const customExtensions = [Underline];

            const result = resolveExtensions({
                customExtensions,
                textOnlyDefaults,
                fullDefaults,
            });

            // Should merge, not replace
            expect(result).toEqual([...fullDefaults, ...customExtensions]);
        });
    });

    describe('immutability', () => {
        it('does not mutate the input arrays', () => {
            const customExtensions = [Underline];
            const originalCustom = [...customExtensions];
            const originalTextOnly = [...textOnlyDefaults];
            const originalFull = [...fullDefaults];

            resolveExtensions({
                customExtensions,
                textOnlyDefaults,
                fullDefaults,
            });

            expect(customExtensions).toEqual(originalCustom);
            expect(textOnlyDefaults).toEqual(originalTextOnly);
            expect(fullDefaults).toEqual(originalFull);
        });

        it('returns a new array when merging custom extensions', () => {
            const customExtensions = [Underline];

            const result = resolveExtensions({
                customExtensions,
                textOnlyDefaults,
                fullDefaults,
            });

            // Should be a new array, not the same reference as fullDefaults
            expect(result).not.toBe(fullDefaults);
            expect(result).not.toBe(customExtensions);
        });
    });
});
