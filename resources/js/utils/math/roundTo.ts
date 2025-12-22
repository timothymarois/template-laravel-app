/**
 * Rounds a number to a specified number of decimal places.
 * @param num - The number to round
 * @param decimals - Number of decimal places (default: 2)
 * @returns The rounded number
 */
export const roundTo = (num: number, decimals = 2): number => {
    const factor = Math.pow(10, decimals);
    return Math.round((num + Number.EPSILON) * factor) / factor;
};
