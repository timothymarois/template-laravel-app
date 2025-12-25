/**
 * Input Formatter Utilities - Text transformation formatters for input fields.
 *
 * For mask-based formatting (phone, SSN, dates, etc.), use the MaskInput component instead.
 * @see MaskInput component
 */

/**
 * Interface for input formatters.
 */
export interface InputFormatter {
    /** Transform raw value to display string */
    format: (value: string | number | null | undefined) => string;
    /** Transform display string back to raw value */
    parse: (display: string) => string;
    /** Maximum length of the formatted display string */
    maxLength: number;
}

/**
 * Uppercase transformer - converts all input to uppercase.
 *
 * @example
 * ```vue
 * <Input v-model="code" :formatter="uppercaseFormatter()" />
 * ```
 */
export const uppercaseFormatter = (): InputFormatter => ({
    maxLength: Infinity,
    format: (value) => {
        if (value === null || value === undefined) return '';
        return String(value).toUpperCase();
    },
    parse: (display) => display.toUpperCase(),
});

/**
 * Lowercase transformer - converts all input to lowercase.
 *
 * @example
 * ```vue
 * <Input v-model="email" :formatter="lowercaseFormatter()" />
 * ```
 */
export const lowercaseFormatter = (): InputFormatter => ({
    maxLength: Infinity,
    format: (value) => {
        if (value === null || value === undefined) return '';
        return String(value).toLowerCase();
    },
    parse: (display) => display.toLowerCase(),
});

/**
 * Utility to create a custom formatter.
 *
 * @param format - Function to transform raw value to display
 * @param parse - Function to transform display to raw value
 * @param maxLength - Maximum display length (default: Infinity)
 * @returns An InputFormatter
 *
 * @example
 * ```typescript
 * const hexColorFormatter = createFormatter(
 *     (value) => value ? `#${String(value).toUpperCase()}` : '',
 *     (display) => display.replace(/^#/, '').replace(/[^0-9A-Fa-f]/g, '').toUpperCase().slice(0, 6),
 *     7
 * );
 * ```
 */
export const createFormatter = (
    format: (value: string | number | null | undefined) => string,
    parse: (display: string) => string,
    maxLength: number = Infinity
): InputFormatter => ({ format, parse, maxLength });
