/**
 * Converts a string into a safe, lowercase slug.
 * @param str - The input string
 * @returns A hyphenated slug (e.g. 'my-post-title')
 */
export const formatSlug = (str: string): string => {
    // Handle null/undefined by converting to string
    if (str === null || str === undefined) {
        return String(str);
    }

    // Convert accented characters to ASCII equivalents where possible
    const normalized = str.normalize('NFD').replace(/[\u0300-\u036f]/g, '');

    return normalized
        .toLowerCase()
        .replace(/[^a-z0-9]+/g, '-')
        .replace(/-+/g, '-')
        .replace(/^-+|-+$/g, '');
};
