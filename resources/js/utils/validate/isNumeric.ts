/**
 * Checks if a value is a numeric string or number (integer or float).
 * @param value - The value to test
 * @returns True if numeric, false otherwise
 */
export const isNumeric = (value: string | number): boolean => {
    return !isNaN(Number(value)) && !isNaN(parseFloat(String(value)));
};
