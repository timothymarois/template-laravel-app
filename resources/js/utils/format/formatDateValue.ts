import type { DateValue } from 'reka-ui';

/**
 * Formats a DateValue object (from @internationalized/date) to a localized date string.
 * @param date - A DateValue object with year, month, and day properties
 * @param format - Output format: 'short' (MM/DD/YYYY) or 'long' (Month DD, YYYY)
 * @param locale - Output locale (default: 'en-US')
 * @returns Formatted date string
 */
export const formatDateValue = (
    date: DateValue | undefined | null,
    format: 'short' | 'long' = 'short',
    locale = 'en-US'
): string => {
    if (!date) return '';

    if (format === 'long') {
        const jsDate = new Date(date.year, date.month - 1, date.day);
        return jsDate.toLocaleDateString(locale, {
            year: 'numeric',
            month: 'long',
            day: 'numeric',
        });
    }

    // Short format: MM/DD/YYYY
    const month = String(date.month).padStart(2, '0');
    const day = String(date.day).padStart(2, '0');
    return `${month}/${day}/${date.year}`;
};
