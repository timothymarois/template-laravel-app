import { parseUtcDate } from './parseUtcDate';

/**
 * Converts a UTC datetime string to a localized date + time string (no seconds).
 * @param utcDatetime - The UTC ISO date string
 * @param userTimezone - Target time zone (default: 'America/New_York' or 'UTC')
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
        timeZone: userTimezone,
        year: 'numeric',
        month: 'numeric',
        day: 'numeric',
        hour: 'numeric',
        minute: 'numeric',
        hour12: true
    });
};
