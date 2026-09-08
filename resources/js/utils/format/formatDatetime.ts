import { parseUtcDate } from './parseUtcDate';
import { isDateOnly, resolveTimeZone } from './timezone';

/**
 * Converts a UTC datetime string to a localized date + time string (no seconds).
 * @param utcDatetime - The UTC ISO date string
 * A real instant is converted into `userTimezone`. A date-only input carries no time,
 * so it is rendered as stored rather than shifted. An unknown zone falls back to UTC.
 *
 * @param userTimezone - Target IANA time zone (default: 'UTC')
 * @param locale - Output locale (default: 'en-US')
 * @returns Localized datetime string (e.g. '7/22/2025, 4:13 PM')
 */
export const formatDatetime = (
    utcDatetime: string | Date,
    userTimezone = 'UTC',
    locale = 'en-US'
): string => {
    if (!utcDatetime) return '';

    const date = parseUtcDate(utcDatetime);

    if (!date) return '';

    return date.toLocaleString(locale, {
        timeZone: isDateOnly(utcDatetime) ? 'UTC' : resolveTimeZone(userTimezone),
        year: 'numeric',
        month: 'numeric',
        day: 'numeric',
        hour: 'numeric',
        minute: 'numeric',
        hour12: true
    });
};
