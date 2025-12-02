/**
 * Formats a number with thousands separators and optional decimal places.
 * @param n - The number to format
 * @param decimals - Number of decimal places (default: 0)
 * @returns The formatted number string
 */
export const formatNumber = (n: string | number, decimals = 0): string => {
    if (n === '' || n === null || n === undefined) return '';
    if (isNaN(Number(n))) return 'Invalid';
    return Number(n)
        .toFixed(decimals)
        .replace(/\B(?=(\d{3})+(?!\d))/g, ',');
};
