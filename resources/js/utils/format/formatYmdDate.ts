/**
 * Converts a date string from `YYYY-MM-DD` format to `MM/DD/YYYY`.
 * @param dateString - The date string in `YYYY-MM-DD` format
 * @returns The formatted date string or an empty string for invalid input
 */
export const formatYmdDate = (dateString: string): string => {
    if (typeof dateString !== 'string') return '';
    const match = /^(\d{4})-(\d{2})-(\d{2})$/.exec(dateString);
    if (!match) return '';
    const [, year, month, day] = match;
    return `${month}/${day}/${year}`;
};
