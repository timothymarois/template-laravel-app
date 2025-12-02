/**
 * Formats a numeric decimal into a percentage string.
 * @param value - The decimal value (e.g. 0.25 = 25%)
 * @param decimals - Number of decimal places (default: 0)
 * @returns The formatted percentage string
 */
export const formatPercentage = (value: number, decimals = 0): string => {
    if (value === null || value === undefined || isNaN(value)) return '';
    return `${(value * 100).toFixed(decimals)}%`;
};
