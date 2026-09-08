/**
 * A calendar date with no time and no zone: `2026-01-01`.
 *
 * Such a value is a date on a wall calendar, not an instant. Reading it as UTC
 * midnight and then rendering it in a zone west of UTC moves it to the previous
 * day — `2026-01-01` shown in `America/New_York` becomes `12/31/2025`. A birthday
 * or a due date must never do that, so these are rendered in UTC regardless of the
 * viewer's zone, which reproduces the date exactly as stored.
 */
const DATE_ONLY = /^\d{4}-\d{2}-\d{2}$/;

export const isDateOnly = (value: unknown): boolean =>
    typeof value === 'string' && DATE_ONLY.test(value.trim());

/**
 * Returns `timeZone` when the runtime accepts it as an IANA zone, and `'UTC'`
 * otherwise.
 *
 * `toLocaleDateString` throws a RangeError on an unknown or empty zone rather than
 * degrading, and the zone usually arrives from stored user data — so an account
 * carrying a stale or empty value would crash the render of every date on the page.
 *
 * @param timeZone - An IANA zone name such as `America/New_York`
 */
export const resolveTimeZone = (timeZone: string | undefined | null): string => {
    if (!timeZone) return 'UTC';

    try {
        new Intl.DateTimeFormat('en-US', { timeZone }).format();

        return timeZone;
    } catch {
        return 'UTC';
    }
};
