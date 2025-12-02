/**
 * Converts a number of bytes into a human-readable file size string.
 * @param bytes - Number of bytes
 * @param decimals - Number of decimal places (default: 2)
 * @returns The formatted size string (e.g. '1.24 MB')
 */
export const formatBytes = (bytes: number, decimals = 2): string => {
    if (typeof bytes !== 'number' || isNaN(bytes)) return '';
    if (bytes === 0) return '0 Bytes';

    // Handle negative values by using absolute value
    const absoluteBytes = Math.abs(bytes);

    const k = 1024;
    const sizes = ['Bytes', 'KB', 'MB', 'GB', 'TB'];
    const i = Math.floor(Math.log(absoluteBytes) / Math.log(k));
    return `${(absoluteBytes / Math.pow(k, i)).toFixed(decimals)} ${sizes[i]}`;
};
