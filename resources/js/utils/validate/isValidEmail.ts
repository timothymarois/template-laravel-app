/**
 * Validates whether a string is a properly formatted email address.
 * @param email - The email address to validate
 * @returns True if valid, false otherwise
 */
export const isValidEmail = (email: string): boolean => {
    if (!email) return false;
    const regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return regex.test(email);
};
