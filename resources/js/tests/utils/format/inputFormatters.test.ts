import { describe, it, expect } from 'vitest';
import {
    numberFormatter,
    currencyFormatter,
    creditCardFormatter,
    phoneFormatter,
    ssnFormatter,
    einFormatter,
    zipCodeFormatter,
    dateFormatter,
    timeFormatter,
    percentageFormatter,
    uppercaseFormatter,
    lowercaseFormatter,
    patternFormatter,
    createFormatter,
    type InputFormatter,
} from '@/utils/format/inputFormatters';

describe('numberFormatter', () => {
    describe('format', () => {
        it('formats integers with thousand separators', () => {
            const formatter = numberFormatter();
            expect(formatter.format(1000)).toBe('1,000');
            expect(formatter.format(1234567)).toBe('1,234,567');
        });

        it('formats numbers with decimals', () => {
            const formatter = numberFormatter(2);
            expect(formatter.format(1234.56)).toBe('1,234.56');
            expect(formatter.format(1000)).toBe('1,000.00');
        });

        it('handles string numbers', () => {
            const formatter = numberFormatter();
            expect(formatter.format('1234567')).toBe('1,234,567');
        });

        it('returns empty string for null/undefined/empty', () => {
            const formatter = numberFormatter();
            expect(formatter.format(null)).toBe('');
            expect(formatter.format(undefined)).toBe('');
            expect(formatter.format('')).toBe('');
        });

        it('handles negative numbers', () => {
            const formatter = numberFormatter(2, true);
            expect(formatter.format(-1234.56)).toBe('-1,234.56');
        });
    });

    describe('parse', () => {
        it('removes thousand separators', () => {
            const formatter = numberFormatter();
            expect(formatter.parse('1,234,567')).toBe('1234567');
        });

        it('preserves decimals when configured', () => {
            const formatter = numberFormatter(2);
            expect(formatter.parse('1,234.56')).toBe('1234.56');
        });

        it('handles empty input', () => {
            const formatter = numberFormatter();
            expect(formatter.parse('')).toBe('');
        });

        it('strips non-numeric characters', () => {
            const formatter = numberFormatter();
            expect(formatter.parse('$1,234')).toBe('1234');
        });

        it('handles negative when allowed', () => {
            const formatter = numberFormatter(0, true);
            expect(formatter.parse('-1234')).toBe('-1234');
        });

        it('strips negative when not allowed', () => {
            const formatter = numberFormatter(0, false);
            expect(formatter.parse('-1234')).toBe('1234');
        });
    });
});

describe('currencyFormatter', () => {
    it('is a number formatter with 2 decimals', () => {
        const formatter = currencyFormatter();
        expect(formatter.format(1234)).toBe('1,234.00');
        expect(formatter.parse('1,234.56')).toBe('1234.56');
    });

    it('disallows negative by default', () => {
        const formatter = currencyFormatter();
        expect(formatter.parse('-100')).toBe('100');
    });

    it('allows negative when configured', () => {
        const formatter = currencyFormatter(true);
        expect(formatter.parse('-100.50')).toBe('-100.50');
    });
});

describe('creditCardFormatter', () => {
    describe('format', () => {
        it('groups digits in fours', () => {
            const formatter = creditCardFormatter();
            expect(formatter.format('4111111111111111')).toBe('4111 1111 1111 1111');
        });

        it('handles partial input', () => {
            const formatter = creditCardFormatter();
            expect(formatter.format('411111')).toBe('4111 11');
        });

        it('limits to 16 digits', () => {
            const formatter = creditCardFormatter();
            expect(formatter.format('41111111111111119999')).toBe('4111 1111 1111 1111');
        });

        it('strips non-digits', () => {
            const formatter = creditCardFormatter();
            expect(formatter.format('4111-1111-1111-1111')).toBe('4111 1111 1111 1111');
        });

        it('returns empty for null/undefined', () => {
            const formatter = creditCardFormatter();
            expect(formatter.format(null)).toBe('');
            expect(formatter.format(undefined)).toBe('');
        });

        it('supports custom separator', () => {
            const formatter = creditCardFormatter('-');
            expect(formatter.format('4111111111111111')).toBe('4111-1111-1111-1111');
        });
    });

    describe('parse', () => {
        it('extracts digits only', () => {
            const formatter = creditCardFormatter();
            expect(formatter.parse('4111 1111 1111 1111')).toBe('4111111111111111');
        });

        it('limits to 16 digits', () => {
            const formatter = creditCardFormatter();
            expect(formatter.parse('4111 1111 1111 1111 9999')).toBe('4111111111111111');
        });
    });
});

describe('phoneFormatter', () => {
    describe('format', () => {
        it('formats 10-digit phone numbers', () => {
            const formatter = phoneFormatter();
            expect(formatter.format('5551234567')).toBe('(555) 123-4567');
        });

        it('handles partial input progressively', () => {
            const formatter = phoneFormatter();
            expect(formatter.format('5')).toBe('(5');
            expect(formatter.format('555')).toBe('(555');
            expect(formatter.format('5551')).toBe('(555) 1');
            expect(formatter.format('555123')).toBe('(555) 123');
            expect(formatter.format('5551234')).toBe('(555) 123-4');
        });

        it('limits to 10 digits', () => {
            const formatter = phoneFormatter();
            expect(formatter.format('55512345679999')).toBe('(555) 123-4567');
        });

        it('returns empty for null/undefined/empty', () => {
            const formatter = phoneFormatter();
            expect(formatter.format(null)).toBe('');
            expect(formatter.format('')).toBe('');
        });
    });

    describe('parse', () => {
        it('extracts digits only', () => {
            const formatter = phoneFormatter();
            expect(formatter.parse('(555) 123-4567')).toBe('5551234567');
        });
    });
});

describe('ssnFormatter', () => {
    describe('format', () => {
        it('formats 9-digit SSN', () => {
            const formatter = ssnFormatter();
            expect(formatter.format('123456789')).toBe('123-45-6789');
        });

        it('handles partial input', () => {
            const formatter = ssnFormatter();
            expect(formatter.format('123')).toBe('123');
            expect(formatter.format('12345')).toBe('123-45');
            expect(formatter.format('123456')).toBe('123-45-6');
        });

        it('limits to 9 digits', () => {
            const formatter = ssnFormatter();
            expect(formatter.format('1234567890')).toBe('123-45-6789');
        });
    });

    describe('parse', () => {
        it('extracts digits only', () => {
            const formatter = ssnFormatter();
            expect(formatter.parse('123-45-6789')).toBe('123456789');
        });
    });
});

describe('einFormatter', () => {
    describe('format', () => {
        it('formats 9-digit EIN', () => {
            const formatter = einFormatter();
            expect(formatter.format('123456789')).toBe('12-3456789');
        });

        it('handles partial input', () => {
            const formatter = einFormatter();
            expect(formatter.format('12')).toBe('12');
            expect(formatter.format('123')).toBe('12-3');
        });
    });

    describe('parse', () => {
        it('extracts digits only', () => {
            const formatter = einFormatter();
            expect(formatter.parse('12-3456789')).toBe('123456789');
        });
    });
});

describe('zipCodeFormatter', () => {
    describe('5-digit format', () => {
        it('formats 5-digit ZIP', () => {
            const formatter = zipCodeFormatter();
            expect(formatter.format('12345')).toBe('12345');
        });

        it('limits to 5 digits', () => {
            const formatter = zipCodeFormatter();
            expect(formatter.format('123456789')).toBe('12345');
        });
    });

    describe('ZIP+4 format', () => {
        it('formats 9-digit ZIP+4', () => {
            const formatter = zipCodeFormatter(true);
            expect(formatter.format('123456789')).toBe('12345-6789');
        });

        it('handles partial input', () => {
            const formatter = zipCodeFormatter(true);
            expect(formatter.format('12345')).toBe('12345');
            expect(formatter.format('123456')).toBe('12345-6');
        });
    });
});

describe('dateFormatter', () => {
    describe('format', () => {
        it('formats as MM/DD/YYYY', () => {
            const formatter = dateFormatter();
            expect(formatter.format('12252024')).toBe('12/25/2024');
        });

        it('handles partial input', () => {
            const formatter = dateFormatter();
            expect(formatter.format('12')).toBe('12');
            expect(formatter.format('1225')).toBe('12/25');
            expect(formatter.format('12252')).toBe('12/25/2');
        });
    });

    describe('parse', () => {
        it('keeps formatted when configured', () => {
            const formatter = dateFormatter(true);
            expect(formatter.parse('12/25/2024')).toBe('12/25/2024');
        });

        it('returns digits when not keeping format', () => {
            const formatter = dateFormatter(false);
            expect(formatter.parse('12/25/2024')).toBe('12252024');
        });
    });
});

describe('timeFormatter', () => {
    describe('format', () => {
        it('formats as HH:MM', () => {
            const formatter = timeFormatter();
            expect(formatter.format('0930')).toBe('09:30');
        });

        it('handles partial input', () => {
            const formatter = timeFormatter();
            expect(formatter.format('09')).toBe('09');
            expect(formatter.format('093')).toBe('09:3');
        });
    });

    describe('parse - 12 hour', () => {
        it('limits hours to 12', () => {
            const formatter = timeFormatter(false);
            expect(formatter.parse('15:30')).toBe('12:30');
        });
    });

    describe('parse - 24 hour', () => {
        it('limits hours to 23', () => {
            const formatter = timeFormatter(true);
            expect(formatter.parse('25:30')).toBe('23:30');
        });

        it('allows valid 24-hour times', () => {
            const formatter = timeFormatter(true);
            expect(formatter.parse('15:30')).toBe('15:30');
        });
    });
});

describe('percentageFormatter', () => {
    describe('format', () => {
        it('appends % symbol', () => {
            const formatter = percentageFormatter();
            expect(formatter.format(50)).toBe('50%');
        });

        it('respects decimal places', () => {
            const formatter = percentageFormatter(2);
            expect(formatter.format(12.5)).toBe('12.50%');
        });
    });

    describe('parse', () => {
        it('removes % and returns number', () => {
            const formatter = percentageFormatter();
            expect(formatter.parse('50%')).toBe('50');
        });

        it('preserves decimals', () => {
            const formatter = percentageFormatter(2);
            expect(formatter.parse('12.50%')).toBe('12.50');
        });
    });
});

describe('uppercaseFormatter', () => {
    it('formats to uppercase', () => {
        const formatter = uppercaseFormatter();
        expect(formatter.format('hello')).toBe('HELLO');
    });

    it('parses to uppercase', () => {
        const formatter = uppercaseFormatter();
        expect(formatter.parse('hello')).toBe('HELLO');
    });

    it('handles null/undefined', () => {
        const formatter = uppercaseFormatter();
        expect(formatter.format(null)).toBe('');
        expect(formatter.format(undefined)).toBe('');
    });
});

describe('lowercaseFormatter', () => {
    it('formats to lowercase', () => {
        const formatter = lowercaseFormatter();
        expect(formatter.format('HELLO')).toBe('hello');
    });

    it('parses to lowercase', () => {
        const formatter = lowercaseFormatter();
        expect(formatter.parse('HELLO')).toBe('hello');
    });
});

describe('patternFormatter', () => {
    describe('digit patterns', () => {
        it('formats with # for digits', () => {
            const formatter = patternFormatter('##-###');
            expect(formatter.format('12345')).toBe('12-345');
        });

        it('handles partial input', () => {
            const formatter = patternFormatter('##-###');
            expect(formatter.format('12')).toBe('12');
            expect(formatter.format('123')).toBe('12-3');
        });

        it('ignores non-digits', () => {
            const formatter = patternFormatter('##-###');
            expect(formatter.format('1a2b3c4d5e')).toBe('12-345');
        });
    });

    describe('letter patterns', () => {
        it('formats with A for letters', () => {
            const formatter = patternFormatter('AAA-####');
            expect(formatter.format('ABC1234')).toBe('ABC-1234');
        });

        it('uppercases letters', () => {
            const formatter = patternFormatter('AAA-####');
            expect(formatter.format('abc1234')).toBe('ABC-1234');
        });
    });

    describe('mixed patterns', () => {
        it('handles mixed digit and letter patterns', () => {
            const formatter = patternFormatter('AA##-####');
            expect(formatter.format('ab123456')).toBe('AB12-3456');
        });
    });

    describe('parse', () => {
        it('extracts values without separators', () => {
            const formatter = patternFormatter('##-###');
            expect(formatter.parse('12-345')).toBe('12345');
        });

        it('extracts letters correctly', () => {
            const formatter = patternFormatter('AAA-####');
            expect(formatter.parse('ABC-1234')).toBe('ABC1234');
        });
    });
});

describe('createFormatter', () => {
    it('creates a formatter from functions', () => {
        const formatter = createFormatter(
            (value) => `$${value}`,
            (display) => display.replace('$', '')
        );

        expect(formatter.format(100)).toBe('$100');
        expect(formatter.parse('$100')).toBe('100');
    });

    it('returns a valid InputFormatter', () => {
        const formatter = createFormatter(
            (v) => String(v),
            (d) => d
        );

        expect(typeof formatter.format).toBe('function');
        expect(typeof formatter.parse).toBe('function');
    });
});

// ============================================
// NEGATIVE PATH / EDGE CASE TESTS
// ============================================

describe('numberFormatter - edge cases', () => {
    describe('format - negative paths', () => {
        it('returns empty for NaN', () => {
            const formatter = numberFormatter();
            expect(formatter.format('abc')).toBe('');
            expect(formatter.format('not a number')).toBe('');
        });

        it('handles zero correctly', () => {
            const formatter = numberFormatter();
            expect(formatter.format(0)).toBe('0');
            expect(formatter.format('0')).toBe('0');
        });

        it('handles very large numbers', () => {
            const formatter = numberFormatter();
            expect(formatter.format(1234567890123)).toBe('1,234,567,890,123');
        });

        it('handles very small decimals', () => {
            const formatter = numberFormatter(6);
            expect(formatter.format(0.000001)).toBe('0.000001');
        });

        it('handles negative zero', () => {
            const formatter = numberFormatter();
            expect(formatter.format(-0)).toBe('0');
        });
    });

    describe('parse - negative paths', () => {
        it('handles multiple decimal points', () => {
            const formatter = numberFormatter(2);
            // Parser keeps all valid numeric characters including multiple dots
            // The resulting string may not be a valid number but parse doesn't validate
            expect(formatter.parse('1.2.3')).toBe('1.2.3');
        });

        it('handles only special characters', () => {
            const formatter = numberFormatter();
            expect(formatter.parse('$$$')).toBe('');
            expect(formatter.parse('...')).toBe('');
        });

        it('handles whitespace', () => {
            const formatter = numberFormatter();
            expect(formatter.parse('  1234  ')).toBe('1234');
        });

        it('handles mixed valid/invalid', () => {
            const formatter = numberFormatter();
            expect(formatter.parse('abc123def456')).toBe('123456');
        });
    });
});

describe('creditCardFormatter - edge cases', () => {
    describe('format - negative paths', () => {
        it('handles letters in input', () => {
            const formatter = creditCardFormatter();
            expect(formatter.format('4111ABCD11111111')).toBe('4111 1111 1111');
        });

        it('handles very short input', () => {
            const formatter = creditCardFormatter();
            expect(formatter.format('4')).toBe('4');
            expect(formatter.format('')).toBe('');
        });

        it('handles spaces in input', () => {
            const formatter = creditCardFormatter();
            expect(formatter.format('4111 1111')).toBe('4111 1111');
        });
    });

    describe('parse - negative paths', () => {
        it('strips all non-digits', () => {
            const formatter = creditCardFormatter();
            expect(formatter.parse('XXXX-XXXX-XXXX-XXXX')).toBe('');
        });

        it('handles mixed letters and digits', () => {
            const formatter = creditCardFormatter();
            // Input has 15 digits: 4,1,1,1,1,1,1,1,1,1,1,1,1,1,1
            expect(formatter.parse('4A1B1C1 1D1E1F1 1G1H1I1 1J1K1L')).toBe('411111111111111');
        });
    });
});

describe('phoneFormatter - edge cases', () => {
    describe('format - negative paths', () => {
        it('handles letters', () => {
            const formatter = phoneFormatter();
            expect(formatter.format('555ABC4567')).toBe('(555) 456-7');
        });

        it('handles single digit', () => {
            const formatter = phoneFormatter();
            expect(formatter.format('5')).toBe('(5');
        });

        it('handles empty after stripping', () => {
            const formatter = phoneFormatter();
            expect(formatter.format('ABCDEFGHIJ')).toBe('');
        });
    });

    describe('parse - negative paths', () => {
        it('handles incomplete phone', () => {
            const formatter = phoneFormatter();
            expect(formatter.parse('(555) 123')).toBe('555123');
        });

        it('handles random formatting', () => {
            const formatter = phoneFormatter();
            expect(formatter.parse('555.123.4567')).toBe('5551234567');
        });
    });
});

describe('ssnFormatter - edge cases', () => {
    describe('format - negative paths', () => {
        it('handles letters', () => {
            const formatter = ssnFormatter();
            expect(formatter.format('123AB6789')).toBe('123-67-89');
        });

        it('handles empty', () => {
            const formatter = ssnFormatter();
            expect(formatter.format('')).toBe('');
        });
    });

    describe('parse - negative paths', () => {
        it('handles various separators', () => {
            const formatter = ssnFormatter();
            expect(formatter.parse('123.45.6789')).toBe('123456789');
            expect(formatter.parse('123 45 6789')).toBe('123456789');
        });
    });
});

describe('einFormatter - edge cases', () => {
    describe('format - negative paths', () => {
        it('handles single digit', () => {
            const formatter = einFormatter();
            expect(formatter.format('1')).toBe('1');
        });

        it('handles letters', () => {
            const formatter = einFormatter();
            expect(formatter.format('12ABC6789')).toBe('12-6789');
        });
    });
});

describe('zipCodeFormatter - edge cases', () => {
    describe('format - negative paths', () => {
        it('handles letters', () => {
            const formatter = zipCodeFormatter();
            expect(formatter.format('123AB')).toBe('123');
        });

        it('handles empty', () => {
            const formatter = zipCodeFormatter();
            expect(formatter.format('')).toBe('');
        });
    });

    describe('parse - negative paths', () => {
        it('handles non-standard formatting', () => {
            const formatter = zipCodeFormatter(true);
            expect(formatter.parse('12345 6789')).toBe('123456789');
        });
    });
});

describe('dateFormatter - edge cases', () => {
    describe('format - negative paths', () => {
        it('handles letters', () => {
            const formatter = dateFormatter();
            expect(formatter.format('12AB2024')).toBe('12/20/24');
        });

        it('handles empty', () => {
            const formatter = dateFormatter();
            expect(formatter.format('')).toBe('');
            expect(formatter.format(null)).toBe('');
        });

        it('handles single digit', () => {
            const formatter = dateFormatter();
            expect(formatter.format('1')).toBe('1');
        });
    });

    describe('parse - negative paths', () => {
        it('handles various date formats', () => {
            const formatter = dateFormatter(false);
            expect(formatter.parse('12-25-2024')).toBe('12252024');
            expect(formatter.parse('12.25.2024')).toBe('12252024');
        });
    });
});

describe('timeFormatter - edge cases', () => {
    describe('format - negative paths', () => {
        it('handles empty', () => {
            const formatter = timeFormatter();
            expect(formatter.format('')).toBe('');
            expect(formatter.format(null)).toBe('');
        });

        it('handles single digit', () => {
            const formatter = timeFormatter();
            expect(formatter.format('1')).toBe('1');
        });

        it('handles letters', () => {
            const formatter = timeFormatter();
            expect(formatter.format('09AB')).toBe('09');
        });
    });

    describe('parse - negative paths', () => {
        it('handles hours at boundary', () => {
            const formatter = timeFormatter(false);
            expect(formatter.parse('12:59')).toBe('12:59');
            expect(formatter.parse('00:00')).toBe('00:00');
        });

        it('handles 24-hour boundary', () => {
            const formatter = timeFormatter(true);
            expect(formatter.parse('23:59')).toBe('23:59');
            expect(formatter.parse('00:00')).toBe('00:00');
        });

        it('handles single digit hours', () => {
            const formatter = timeFormatter(false);
            expect(formatter.parse('9')).toBe('9');
        });
    });
});

describe('percentageFormatter - edge cases', () => {
    describe('format - negative paths', () => {
        it('returns empty for NaN', () => {
            const formatter = percentageFormatter();
            expect(formatter.format('abc')).toBe('');
            expect(formatter.format('')).toBe('');
        });

        it('handles zero', () => {
            const formatter = percentageFormatter();
            expect(formatter.format(0)).toBe('0%');
        });

        it('handles negative', () => {
            const formatter = percentageFormatter();
            expect(formatter.format(-50)).toBe('-50%');
        });

        it('handles large numbers', () => {
            const formatter = percentageFormatter();
            expect(formatter.format(1000)).toBe('1000%');
        });
    });

    describe('parse - negative paths', () => {
        it('handles no % symbol', () => {
            const formatter = percentageFormatter();
            expect(formatter.parse('50')).toBe('50');
        });

        it('handles multiple % symbols', () => {
            const formatter = percentageFormatter();
            expect(formatter.parse('50%%')).toBe('50');
        });

        it('handles letters', () => {
            const formatter = percentageFormatter();
            expect(formatter.parse('abc')).toBe('');
        });
    });
});

describe('uppercaseFormatter - edge cases', () => {
    it('handles numbers (unchanged)', () => {
        const formatter = uppercaseFormatter();
        expect(formatter.format('abc123')).toBe('ABC123');
    });

    it('handles special characters', () => {
        const formatter = uppercaseFormatter();
        expect(formatter.format('hello-world!')).toBe('HELLO-WORLD!');
    });

    it('handles already uppercase', () => {
        const formatter = uppercaseFormatter();
        expect(formatter.format('HELLO')).toBe('HELLO');
    });

    it('handles mixed case', () => {
        const formatter = uppercaseFormatter();
        expect(formatter.format('HeLLo WoRLd')).toBe('HELLO WORLD');
    });
});

describe('lowercaseFormatter - edge cases', () => {
    it('handles numbers (unchanged)', () => {
        const formatter = lowercaseFormatter();
        expect(formatter.format('ABC123')).toBe('abc123');
    });

    it('handles special characters', () => {
        const formatter = lowercaseFormatter();
        expect(formatter.format('HELLO-WORLD!')).toBe('hello-world!');
    });

    it('handles null/undefined', () => {
        const formatter = lowercaseFormatter();
        expect(formatter.format(null)).toBe('');
        expect(formatter.format(undefined)).toBe('');
    });
});

describe('patternFormatter - edge cases', () => {
    describe('format - negative paths', () => {
        it('handles empty input', () => {
            const formatter = patternFormatter('##-##');
            expect(formatter.format('')).toBe('');
            expect(formatter.format(null)).toBe('');
        });

        it('handles wrong character types', () => {
            // Pattern expects letters but gets digits
            const formatter = patternFormatter('AAA');
            expect(formatter.format('123')).toBe('');
        });

        it('handles mixed when expecting specific type', () => {
            const formatter = patternFormatter('###');
            expect(formatter.format('a1b2c3')).toBe('123');
        });

        it('handles too short input', () => {
            const formatter = patternFormatter('####-####');
            expect(formatter.format('12')).toBe('12');
        });
    });

    describe('parse - negative paths', () => {
        it('handles missing separators', () => {
            const formatter = patternFormatter('##-##');
            // Without proper separator, parse extracts what it can match
            expect(formatter.parse('1234')).toBe('1234');
        });

        it('handles wrong separators', () => {
            const formatter = patternFormatter('##-##');
            // With wrong separator, still extracts valid chars
            expect(formatter.parse('12/34')).toBe('123');
        });

        it('handles empty', () => {
            const formatter = patternFormatter('##-##');
            expect(formatter.parse('')).toBe('');
        });
    });
});

describe('createFormatter - edge cases', () => {
    it('handles format throwing error gracefully', () => {
        const formatter = createFormatter(
            () => { throw new Error('test'); },
            (d) => d
        );
        expect(() => formatter.format('test')).toThrow();
    });

    it('handles parse throwing error gracefully', () => {
        const formatter = createFormatter(
            (v) => String(v),
            () => { throw new Error('test'); }
        );
        expect(() => formatter.parse('test')).toThrow();
    });

    it('handles complex transformation', () => {
        const formatter = createFormatter(
            (value) => {
                if (!value) return '';
                return `PREFIX_${String(value).toUpperCase()}_SUFFIX`;
            },
            (display) => {
                return display.replace('PREFIX_', '').replace('_SUFFIX', '').toLowerCase();
            }
        );

        expect(formatter.format('hello')).toBe('PREFIX_HELLO_SUFFIX');
        expect(formatter.parse('PREFIX_HELLO_SUFFIX')).toBe('hello');
    });
});

describe('InputFormatter interface compliance', () => {
    const formatters: Record<string, InputFormatter> = {
        number: numberFormatter(),
        currency: currencyFormatter(),
        creditCard: creditCardFormatter(),
        phone: phoneFormatter(),
        ssn: ssnFormatter(),
        ein: einFormatter(),
        zip: zipCodeFormatter(),
        date: dateFormatter(),
        time: timeFormatter(),
        percentage: percentageFormatter(),
        uppercase: uppercaseFormatter(),
        lowercase: lowercaseFormatter(),
        pattern: patternFormatter('##-##'),
    };

    Object.entries(formatters).forEach(([name, formatter]) => {
        describe(name, () => {
            it('has format function', () => {
                expect(typeof formatter.format).toBe('function');
            });

            it('has parse function', () => {
                expect(typeof formatter.parse).toBe('function');
            });

            it('format returns string', () => {
                const result = formatter.format('test');
                expect(typeof result).toBe('string');
            });

            it('handles empty format gracefully', () => {
                expect(() => formatter.format('')).not.toThrow();
                expect(() => formatter.format(null)).not.toThrow();
                expect(() => formatter.format(undefined)).not.toThrow();
            });

            it('handles empty parse gracefully', () => {
                expect(() => formatter.parse('')).not.toThrow();
            });
        });
    });
});
