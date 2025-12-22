/**
 * Validates whether a string is a valid HTTP or HTTPS URL.
 * @param url - The URL string to validate
 * @returns True if valid, false otherwise
 */
export const isValidURL = (url: string): boolean => {
    try {
        const parsed = new URL(url);
        // Check protocol is http or https
        if (!/^(http|https):/.test(parsed.protocol)) {
            return false;
        }
        // Ensure the hostname has at least one dot and something after the last dot
        // This catches cases like 'https://example.'
        const hostnameParts = parsed.hostname.split('.');
        if (hostnameParts.length < 2 || hostnameParts[hostnameParts.length - 1] === '') {
            return false;
        }
        return true;
    } catch {
        return false;
    }
};
