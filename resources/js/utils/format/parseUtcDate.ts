/**
 * Parses a UTC datetime (string or Date) into a Date object.
 * Appends a "Z" suffix for strings missing it to ensure UTC interpretation.
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
                ? new Date(utcDate.endsWith('Z') ? utcDate : `${utcDate}Z`)
                : null;

    if (!date || isNaN(date.getTime())) return null;

    return date;
};

