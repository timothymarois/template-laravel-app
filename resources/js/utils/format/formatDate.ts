import { parseUtcDate } from './parseUtcDate';
import { isDateOnly, resolveTimeZone } from './timezone';

/**
 * Converts a UTC datetime (string or Date) to a localized date string.
 * @param utcDatetime - A UTC ISO date string or Date object
 * A date-only input (`2026-01-01`) is a calendar date, so it is rendered as stored
 * and never shifted into `userTimezone`. An unknown zone falls back to UTC.
 *
 * @param userTimezone - Target IANA time zone (default: 'UTC')
 * @param locale - Output locale (default: 'en-US')
 * @returns Localized date string (e.g. '7/22/2025')
 */
export const formatDate = (
    utcDatetime: string | Date,
    userTimezone = 'UTC',
    locale = 'en-US'
): string => {
    if (!utcDatetime) return '';

    const date = parseUtcDate(utcDatetime);

    if (!date) return '';

    const timeZone = isDateOnly(utcDatetime) ? 'UTC' : resolveTimeZone(userTimezone);

    return date.toLocaleDateString(locale, { timeZone });
};
