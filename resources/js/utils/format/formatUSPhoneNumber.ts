/**
 * Format a US-based phone number from 9999999999 to (999) 999-9999
 *
 * Optionally include the country code, returning +1 (999) 999-9999.
 * If the input includes a leading "1" (e.g. 19995551234), the extra
 * digit is stripped before formatting.
 *
 * @param phoneNumber - The phone number to format
 * @param withCountryCode - When true, prefix the formatted number with +1
 * @returns The formatted phone number string
 */
export const formatUSPhoneNumber = (
    phoneNumber: string | number,
    withCountryCode = false,
): string => {
    if (!phoneNumber) return '';

    const digits = phoneNumber.toString().replace(/\D/g, '');
    const normalized = digits.length === 11 && digits.startsWith('1') ? digits.slice(1) : digits;

    if (normalized.length !== 10) return '';

    const formatted = `(${normalized.slice(0, 3)}) ${normalized.slice(3, 6)}-${normalized.slice(6)}`;
    return withCountryCode ? `+1 ${formatted}` : formatted;
};
