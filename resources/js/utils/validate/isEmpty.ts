/**
 * Determines if a value is considered "empty".
 * Supports strings, arrays, objects, null, and undefined.
 * @param value - The value to evaluate
 * @returns True if empty, false otherwise
 */
export const isEmpty = (value: unknown): boolean => {
    if (value == null) return true;

    if (typeof value === 'string' || Array.isArray(value)) {
        return value.length === 0;
    }

    if (typeof value === 'object') {
        return Object.keys(value as object).length === 0;
    }

    return false;
};
