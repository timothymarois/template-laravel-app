/**
 * Returns a random element from an array.
 * @param items - Array of items
 * @returns A random item from the array or `undefined` if the array is empty or not an array.
 */
export const getRandomItem = <T>(items: T[]): T | undefined => {
    if (!Array.isArray(items) || items.length === 0) return undefined;
    const index = Math.floor(Math.random() * items.length);
    return items[index];
};
