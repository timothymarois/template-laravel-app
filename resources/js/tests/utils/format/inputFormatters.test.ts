import { describe, it, expect } from 'vitest';
import {
    uppercaseFormatter,
    lowercaseFormatter,
    createFormatter,
    type InputFormatter,
} from '@/utils/format/inputFormatters';

// ============================================
// UPPERCASE FORMATTER
// ============================================

describe('uppercaseFormatter', () => {
    it('converts lowercase to uppercase', () => {
        const formatter = uppercaseFormatter();
        expect(formatter.format('hello')).toBe('HELLO');
    });

    it('handles mixed case', () => {
        const formatter = uppercaseFormatter();
        expect(formatter.format('HeLLo WoRLd')).toBe('HELLO WORLD');
    });

    it('handles already uppercase', () => {
        const formatter = uppercaseFormatter();
        expect(formatter.format('HELLO')).toBe('HELLO');
    });

    it('preserves special characters', () => {
        const formatter = uppercaseFormatter();
        expect(formatter.format('hello-world_123!')).toBe('HELLO-WORLD_123!');
    });

    it('handles empty string', () => {
        const formatter = uppercaseFormatter();
        expect(formatter.format('')).toBe('');
    });

    it('handles null', () => {
        const formatter = uppercaseFormatter();
        expect(formatter.format(null)).toBe('');
    });

    it('handles undefined', () => {
        const formatter = uppercaseFormatter();
        expect(formatter.format(undefined)).toBe('');
    });

    it('handles numbers', () => {
        const formatter = uppercaseFormatter();
        expect(formatter.format(123)).toBe('123');
    });

    it('has infinite maxLength', () => {
        const formatter = uppercaseFormatter();
        expect(formatter.maxLength).toBe(Infinity);
    });

    it('parse also uppercases', () => {
        const formatter = uppercaseFormatter();
        expect(formatter.parse('hello')).toBe('HELLO');
    });

    it('handles unicode characters', () => {
        const formatter = uppercaseFormatter();
        expect(formatter.format('café')).toBe('CAFÉ');
    });

    it('handles whitespace', () => {
        const formatter = uppercaseFormatter();
        expect(formatter.format('  hello  ')).toBe('  HELLO  ');
    });
});

// ============================================
// LOWERCASE FORMATTER
// ============================================

describe('lowercaseFormatter', () => {
    it('converts uppercase to lowercase', () => {
        const formatter = lowercaseFormatter();
        expect(formatter.format('HELLO')).toBe('hello');
    });

    it('handles mixed case', () => {
        const formatter = lowercaseFormatter();
        expect(formatter.format('HeLLo WoRLd')).toBe('hello world');
    });

    it('handles already lowercase', () => {
        const formatter = lowercaseFormatter();
        expect(formatter.format('hello')).toBe('hello');
    });

    it('preserves special characters', () => {
        const formatter = lowercaseFormatter();
        expect(formatter.format('HELLO-WORLD_123!')).toBe('hello-world_123!');
    });

    it('handles empty string', () => {
        const formatter = lowercaseFormatter();
        expect(formatter.format('')).toBe('');
    });

    it('handles null', () => {
        const formatter = lowercaseFormatter();
        expect(formatter.format(null)).toBe('');
    });

    it('handles undefined', () => {
        const formatter = lowercaseFormatter();
        expect(formatter.format(undefined)).toBe('');
    });

    it('handles numbers', () => {
        const formatter = lowercaseFormatter();
        expect(formatter.format(123)).toBe('123');
    });

    it('has infinite maxLength', () => {
        const formatter = lowercaseFormatter();
        expect(formatter.maxLength).toBe(Infinity);
    });

    it('parse also lowercases', () => {
        const formatter = lowercaseFormatter();
        expect(formatter.parse('HELLO')).toBe('hello');
    });

    it('handles unicode characters', () => {
        const formatter = lowercaseFormatter();
        expect(formatter.format('CAFÉ')).toBe('café');
    });

    it('handles whitespace', () => {
        const formatter = lowercaseFormatter();
        expect(formatter.format('  HELLO  ')).toBe('  hello  ');
    });
});

// ============================================
// CREATE FORMATTER
// ============================================

describe('createFormatter', () => {
    it('creates a custom formatter', () => {
        const formatter = createFormatter(
            (value) => `[${value}]`,
            (display) => display.replace(/^\[|\]$/g, '')
        );
        expect(formatter.format('test')).toBe('[test]');
        expect(formatter.parse('[test]')).toBe('test');
    });

    it('uses default maxLength of Infinity', () => {
        const formatter = createFormatter(
            (value) => String(value),
            (display) => display
        );
        expect(formatter.maxLength).toBe(Infinity);
    });

    it('accepts custom maxLength', () => {
        const formatter = createFormatter(
            (value) => String(value),
            (display) => display,
            10
        );
        expect(formatter.maxLength).toBe(10);
    });

    it('can create hex color formatter', () => {
        const hexFormatter = createFormatter(
            (value) => (value ? `#${String(value).toUpperCase()}` : ''),
            (display) =>
                display
                    .replace(/^#/, '')
                    .replace(/[^0-9A-Fa-f]/g, '')
                    .toUpperCase()
                    .slice(0, 6),
            7
        );
        expect(hexFormatter.format('ff0000')).toBe('#FF0000');
        expect(hexFormatter.parse('#ff0000')).toBe('FF0000');
        expect(hexFormatter.parse('invalid#chars!')).toBe('ADCA'); // extracts valid hex chars: a, d, c, a
        expect(hexFormatter.maxLength).toBe(7);
    });

    it('can create slug formatter', () => {
        const slugFormatter = createFormatter(
            (value) => String(value || ''),
            (display) =>
                display
                    .toLowerCase()
                    .replace(/\s+/g, '-')
                    .replace(/[^a-z0-9-]/g, '')
                    .replace(/--+/g, '-')
        );
        expect(slugFormatter.parse('Hello World')).toBe('hello-world');
        expect(slugFormatter.parse('Hello  World')).toBe('hello-world');
        expect(slugFormatter.parse('Hello@World!')).toBe('helloworld');
    });

    it('handles null in custom format', () => {
        const formatter = createFormatter(
            (value) => (value === null ? 'NULL' : String(value)),
            (display) => display
        );
        expect(formatter.format(null)).toBe('NULL');
    });

    it('handles undefined in custom format', () => {
        const formatter = createFormatter(
            (value) => (value === undefined ? 'UNDEFINED' : String(value)),
            (display) => display
        );
        expect(formatter.format(undefined)).toBe('UNDEFINED');
    });
});

// ============================================
// INPUT FORMATTER INTERFACE COMPLIANCE
// ============================================

describe('InputFormatter interface compliance', () => {
    const formatters: [string, () => InputFormatter][] = [
        ['uppercaseFormatter', uppercaseFormatter],
        ['lowercaseFormatter', lowercaseFormatter],
        [
            'createFormatter (custom)',
            () =>
                createFormatter(
                    (v) => String(v ?? ''),
                    (d) => d
                ),
        ],
    ];

    formatters.forEach(([name, createFn]) => {
        describe(name, () => {
            it('has format function', () => {
                const formatter = createFn();
                expect(typeof formatter.format).toBe('function');
            });

            it('has parse function', () => {
                const formatter = createFn();
                expect(typeof formatter.parse).toBe('function');
            });

            it('has maxLength property', () => {
                const formatter = createFn();
                expect(typeof formatter.maxLength).toBe('number');
            });

            it('format returns string', () => {
                const formatter = createFn();
                expect(typeof formatter.format('test')).toBe('string');
            });

            it('parse returns string', () => {
                const formatter = createFn();
                expect(typeof formatter.parse('test')).toBe('string');
            });

            it('handles empty format gracefully', () => {
                const formatter = createFn();
                const result = formatter.format('');
                expect(typeof result).toBe('string');
            });

            it('handles empty parse gracefully', () => {
                const formatter = createFn();
                const result = formatter.parse('');
                expect(typeof result).toBe('string');
            });

            it('maxLength is positive or Infinity', () => {
                const formatter = createFn();
                expect(formatter.maxLength > 0 || formatter.maxLength === Infinity).toBe(true);
            });
        });
    });
});
