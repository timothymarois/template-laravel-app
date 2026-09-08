/**
 * A zone already stated: a trailing `Z`, or a numeric offset such as `+00:00`.
 *
 * The offset half is why this is a pattern rather than `endsWith('Z')`. Laravel's
 * `toIso8601String()` ends `+00:00`, and appending a `Z` to that yields
 * `...+00:00Z`, which is not a date at all — the parser returns null, the
 * formatter returns an empty string, and the field renders blank with nothing
 * thrown. Fixtures written ending in `Z` never catch it.
 */
const STATES_ITS_ZONE = /(?:Z|[+-]\d{2}:?\d{2})$/i;

/**
 * Parses a UTC datetime (string or Date) into a Date object.
 *
 * A string carrying no zone is read as UTC, which is what the database stores and
 * what a bare `2026-08-16 21:20:05` from the server means. A string that already
 * states its zone is left exactly as it is.
 *
 * @param utcDate - A UTC ISO date string or Date object
 * @returns Parsed Date object or null if invalid
 */
export const parseUtcDate = (utcDate: string | Date): Date | null => {
    if (!utcDate) return null;

    const date =
        utcDate instanceof Date
            ? utcDate
            : typeof utcDate === 'string'
                ? new Date(STATES_ITS_ZONE.test(utcDate) ? utcDate : `${utcDate}Z`)
                : null;

    if (!date || isNaN(date.getTime())) return null;

    return date;
};

