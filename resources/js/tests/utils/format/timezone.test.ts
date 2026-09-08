import { describe, expect, it } from 'vitest';
import { isDateOnly, resolveTimeZone } from '@/utils/format/timezone';

describe('isDateOnly', () => {
    it.each(['2026-01-01', '1999-12-31', '  2026-06-15  '])('accepts %s', (value) => {
        expect(isDateOnly(value)).toBe(true);
    });

    it.each([
        '2026-01-01T00:00:00',
        '2026-01-01T00:00:00Z',
        '2026-01-01 00:00:00',
        '2026-1-1',
        '',
        'not-a-date',
    ])('rejects %s', (value) => {
        expect(isDateOnly(value)).toBe(false);
    });

    it.each([null, undefined, 20260101, new Date(), {}, []])('rejects non-strings', (value) => {
        expect(isDateOnly(value)).toBe(false);
    });
});

describe('resolveTimeZone', () => {
    it.each(['UTC', 'America/New_York', 'Asia/Tokyo', 'Europe/London', 'Australia/Sydney'])(
        'keeps the valid zone %s',
        (zone) => {
            expect(resolveTimeZone(zone)).toBe(zone);
        },
    );

    it.each(['America/Nowhere', 'Not/AZone', 'XYZ'])('falls back to UTC for %s', (zone) => {
        // Intl throws a RangeError on an unknown zone. The value usually comes from stored
        // user data, so throwing would crash the render of every date on the page.
        expect(resolveTimeZone(zone)).toBe('UTC');
    });

    it.each([null, undefined, ''])('falls back to UTC for an absent zone', (zone) => {
        expect(resolveTimeZone(zone as string | null | undefined)).toBe('UTC');
    });

    it('never throws for any input', () => {
        expect(() => resolveTimeZone('💥')).not.toThrow();
    });
});
