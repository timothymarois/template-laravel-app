/**
 * Input Formatters for visual formatting of input fields.
 *
 * These formatters provide two-way transformation between raw values (used for v-model/submission)
 * and display values (shown to the user in the input field).
 *
 * @example
 * ```vue
 * <Input v-model="price" :formatter="currencyFormatter()" />
 * <!-- User sees: 1,234.56 -->
 * <!-- v-model value: 1234.56 -->
 * ```
 *
 * @example Custom formatter
 * ```typescript
 * const uppercaseFormatter: InputFormatter = {
 *     format: (value) => String(value).toUpperCase(),
 *     parse: (display) => display.toLowerCase(),
 * };
 * ```
 */

/**
 * Interface for input formatters.
 * Formatters transform between raw values and display values.
 */
export interface InputFormatter {
    /**
     * Transform raw value to display string.
     * Called when the input value changes programmatically or needs to be displayed.
     * @param value - The raw value from v-model
     * @returns The formatted display string
     */
    format: (value: string | number | null | undefined) => string;

    /**
     * Transform display string back to raw value.
     * Called when user types in the input to extract the actual value.
     * @param display - The display string from the input
     * @returns The raw value to emit via v-model
     */
    parse: (display: string) => string | number;
}

/**
 * Creates a number formatter with thousand separators.
 * Displays numbers with commas (e.g., 1000 → "1,000") while keeping the raw numeric value.
 *
 * @param decimals - Number of decimal places to allow (default: 0)
 * @param allowNegative - Whether to allow negative numbers (default: true)
 * @returns An InputFormatter for numbers with thousand separators
 *
 * @example
 * ```vue
 * <Input v-model="amount" :formatter="numberFormatter()" />
 * <!-- User sees: 1,234 | v-model: 1234 -->
 *
 * <Input v-model="price" :formatter="numberFormatter(2)" />
 * <!-- User sees: 1,234.56 | v-model: 1234.56 -->
 * ```
 */
export const numberFormatter = (decimals = 0, allowNegative = true): InputFormatter => ({
    format: (value) => {
        if (value === null || value === undefined || value === '') return '';
        const num = typeof value === 'string' ? parseFloat(value) : value;
        if (isNaN(num)) return '';

        const parts = num.toFixed(decimals).split('.');
        parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, ',');
        return parts.join('.');
    },
    parse: (display) => {
        if (!display) return '';
        // Remove commas, keep digits, decimal point, and optionally minus
        const cleaned = display.replace(/,/g, '');
        const pattern = allowNegative ? /[^\d.-]/g : /[^\d.]/g;
        const value = cleaned.replace(pattern, '');

        // Handle edge cases
        if (value === '' || value === '-' || value === '.') return value;

        const num = parseFloat(value);
        return isNaN(num) ? '' : (decimals > 0 ? value : String(Math.floor(num)));
    },
});

/**
 * Creates a currency formatter (numbers with thousand separators, always 2 decimals).
 * Displays currency-like numbers (e.g., 1000 → "1,000.00") while keeping the raw numeric value.
 *
 * @param allowNegative - Whether to allow negative numbers (default: false)
 * @returns An InputFormatter for currency values
 *
 * @example
 * ```vue
 * <Input v-model="price" :formatter="currencyFormatter()" />
 * <!-- User sees: 1,234.00 | v-model: 1234.00 -->
 * ```
 */
export const currencyFormatter = (allowNegative = false): InputFormatter =>
    numberFormatter(2, allowNegative);

/**
 * Creates a credit card formatter.
 * Displays card numbers in groups of 4 (e.g., 4111111111111111 → "4111 1111 1111 1111").
 *
 * @param separator - Character to use between groups (default: ' ')
 * @returns An InputFormatter for credit card numbers
 *
 * @example
 * ```vue
 * <Input v-model="cardNumber" :formatter="creditCardFormatter()" />
 * <!-- User sees: 4111 1111 1111 1111 | v-model: 4111111111111111 -->
 * ```
 */
export const creditCardFormatter = (separator = ' '): InputFormatter => ({
    format: (value) => {
        if (value === null || value === undefined || value === '') return '';
        const digits = String(value).replace(/\D/g, '').slice(0, 16);
        return digits.replace(/(\d{4})(?=\d)/g, `$1${separator}`);
    },
    parse: (display) => {
        return display.replace(/\D/g, '').slice(0, 16);
    },
});

/**
 * Creates a US phone number formatter.
 * Displays phone numbers as (XXX) XXX-XXXX while keeping raw digits.
 *
 * @returns An InputFormatter for US phone numbers
 *
 * @example
 * ```vue
 * <Input v-model="phone" :formatter="phoneFormatter()" />
 * <!-- User sees: (555) 123-4567 | v-model: 5551234567 -->
 * ```
 */
export const phoneFormatter = (): InputFormatter => ({
    format: (value) => {
        if (value === null || value === undefined || value === '') return '';
        const digits = String(value).replace(/\D/g, '').slice(0, 10);

        if (digits.length === 0) return '';
        if (digits.length <= 3) return `(${digits}`;
        if (digits.length <= 6) return `(${digits.slice(0, 3)}) ${digits.slice(3)}`;
        return `(${digits.slice(0, 3)}) ${digits.slice(3, 6)}-${digits.slice(6)}`;
    },
    parse: (display) => {
        return display.replace(/\D/g, '').slice(0, 10);
    },
});

/**
 * Creates a Social Security Number (SSN) formatter.
 * Displays SSN as XXX-XX-XXXX while keeping raw digits.
 *
 * @returns An InputFormatter for SSN
 *
 * @example
 * ```vue
 * <Input v-model="ssn" :formatter="ssnFormatter()" />
 * <!-- User sees: 123-45-6789 | v-model: 123456789 -->
 * ```
 */
export const ssnFormatter = (): InputFormatter => ({
    format: (value) => {
        if (value === null || value === undefined || value === '') return '';
        const digits = String(value).replace(/\D/g, '').slice(0, 9);

        if (digits.length <= 3) return digits;
        if (digits.length <= 5) return `${digits.slice(0, 3)}-${digits.slice(3)}`;
        return `${digits.slice(0, 3)}-${digits.slice(3, 5)}-${digits.slice(5)}`;
    },
    parse: (display) => {
        return display.replace(/\D/g, '').slice(0, 9);
    },
});

/**
 * Creates an EIN (Employer Identification Number) formatter.
 * Displays EIN as XX-XXXXXXX while keeping raw digits.
 *
 * @returns An InputFormatter for EIN
 *
 * @example
 * ```vue
 * <Input v-model="ein" :formatter="einFormatter()" />
 * <!-- User sees: 12-3456789 | v-model: 123456789 -->
 * ```
 */
export const einFormatter = (): InputFormatter => ({
    format: (value) => {
        if (value === null || value === undefined || value === '') return '';
        const digits = String(value).replace(/\D/g, '').slice(0, 9);

        if (digits.length <= 2) return digits;
        return `${digits.slice(0, 2)}-${digits.slice(2)}`;
    },
    parse: (display) => {
        return display.replace(/\D/g, '').slice(0, 9);
    },
});

/**
 * Creates a ZIP code formatter.
 * Supports both 5-digit and ZIP+4 formats (e.g., 12345 or 12345-6789).
 *
 * @param extended - Whether to support ZIP+4 format (default: false)
 * @returns An InputFormatter for ZIP codes
 *
 * @example
 * ```vue
 * <Input v-model="zip" :formatter="zipCodeFormatter()" />
 * <!-- User sees: 12345 | v-model: 12345 -->
 *
 * <Input v-model="zip" :formatter="zipCodeFormatter(true)" />
 * <!-- User sees: 12345-6789 | v-model: 123456789 -->
 * ```
 */
export const zipCodeFormatter = (extended = false): InputFormatter => ({
    format: (value) => {
        if (value === null || value === undefined || value === '') return '';
        const digits = String(value).replace(/\D/g, '').slice(0, extended ? 9 : 5);

        if (!extended || digits.length <= 5) return digits;
        return `${digits.slice(0, 5)}-${digits.slice(5)}`;
    },
    parse: (display) => {
        return display.replace(/\D/g, '').slice(0, extended ? 9 : 5);
    },
});

/**
 * Creates a date formatter for MM/DD/YYYY format.
 * Displays dates with slashes while keeping raw digits or formatted string.
 *
 * @param keepFormatted - If true, parse returns formatted string; if false, returns digits only (default: true)
 * @returns An InputFormatter for dates
 *
 * @example
 * ```vue
 * <Input v-model="date" :formatter="dateFormatter()" />
 * <!-- User sees: 12/25/2024 | v-model: 12/25/2024 -->
 *
 * <Input v-model="date" :formatter="dateFormatter(false)" />
 * <!-- User sees: 12/25/2024 | v-model: 12252024 -->
 * ```
 */
export const dateFormatter = (keepFormatted = true): InputFormatter => ({
    format: (value) => {
        if (value === null || value === undefined || value === '') return '';
        const digits = String(value).replace(/\D/g, '').slice(0, 8);

        if (digits.length <= 2) return digits;
        if (digits.length <= 4) return `${digits.slice(0, 2)}/${digits.slice(2)}`;
        return `${digits.slice(0, 2)}/${digits.slice(2, 4)}/${digits.slice(4)}`;
    },
    parse: (display) => {
        const digits = display.replace(/\D/g, '').slice(0, 8);
        if (!keepFormatted) return digits;

        if (digits.length <= 2) return digits;
        if (digits.length <= 4) return `${digits.slice(0, 2)}/${digits.slice(2)}`;
        return `${digits.slice(0, 2)}/${digits.slice(2, 4)}/${digits.slice(4)}`;
    },
});

/**
 * Creates a time formatter for HH:MM format (12 or 24 hour).
 *
 * @param use24Hour - Whether to use 24-hour format (default: false for 12-hour)
 * @returns An InputFormatter for time
 *
 * @example
 * ```vue
 * <Input v-model="time" :formatter="timeFormatter()" />
 * <!-- User sees: 02:30 | v-model: 02:30 -->
 * ```
 */
export const timeFormatter = (use24Hour = false): InputFormatter => ({
    format: (value) => {
        if (value === null || value === undefined || value === '') return '';
        const digits = String(value).replace(/\D/g, '').slice(0, 4);

        if (digits.length <= 2) return digits;
        return `${digits.slice(0, 2)}:${digits.slice(2)}`;
    },
    parse: (display) => {
        const digits = display.replace(/\D/g, '').slice(0, 4);

        // Validate hours based on format
        if (digits.length >= 2) {
            const hours = parseInt(digits.slice(0, 2), 10);
            const maxHours = use24Hour ? 23 : 12;
            if (hours > maxHours) {
                const correctedHours = String(maxHours).padStart(2, '0');
                const rest = digits.slice(2);
                return rest ? `${correctedHours}:${rest}` : correctedHours;
            }
        }

        if (digits.length <= 2) return digits;
        return `${digits.slice(0, 2)}:${digits.slice(2)}`;
    },
});

/**
 * Creates a percentage formatter.
 * Appends % symbol and formats with optional decimal places.
 *
 * @param decimals - Number of decimal places (default: 0)
 * @returns An InputFormatter for percentages
 *
 * @example
 * ```vue
 * <Input v-model="rate" :formatter="percentageFormatter(2)" />
 * <!-- User sees: 12.50% | v-model: 12.50 -->
 * ```
 */
export const percentageFormatter = (decimals = 0): InputFormatter => ({
    format: (value) => {
        if (value === null || value === undefined || value === '') return '';
        const num = typeof value === 'string' ? parseFloat(value) : value;
        if (isNaN(num)) return '';
        return `${num.toFixed(decimals)}%`;
    },
    parse: (display) => {
        const cleaned = display.replace(/[^0-9.-]/g, '');
        if (!cleaned) return '';
        const num = parseFloat(cleaned);
        return isNaN(num) ? '' : num.toFixed(decimals);
    },
});

/**
 * Creates an uppercase formatter.
 * Transforms input to uppercase while typing.
 *
 * @returns An InputFormatter for uppercase text
 *
 * @example
 * ```vue
 * <Input v-model="code" :formatter="uppercaseFormatter()" />
 * <!-- User types: abc | Sees: ABC | v-model: ABC -->
 * ```
 */
export const uppercaseFormatter = (): InputFormatter => ({
    format: (value) => {
        if (value === null || value === undefined) return '';
        return String(value).toUpperCase();
    },
    parse: (display) => display.toUpperCase(),
});

/**
 * Creates a lowercase formatter.
 * Transforms input to lowercase while typing.
 *
 * @returns An InputFormatter for lowercase text
 *
 * @example
 * ```vue
 * <Input v-model="email" :formatter="lowercaseFormatter()" />
 * <!-- User types: ABC | Sees: abc | v-model: abc -->
 * ```
 */
export const lowercaseFormatter = (): InputFormatter => ({
    format: (value) => {
        if (value === null || value === undefined) return '';
        return String(value).toLowerCase();
    },
    parse: (display) => display.toLowerCase(),
});

/**
 * Creates a custom pattern formatter using a mask.
 * Use # for digits and A for letters.
 *
 * @param pattern - The pattern mask (e.g., "##-###-####" or "AA-####")
 * @returns An InputFormatter for the custom pattern
 *
 * @example
 * ```vue
 * <Input v-model="code" :formatter="patternFormatter('##-###')" />
 * <!-- User sees: 12-345 | v-model: 12345 -->
 *
 * <Input v-model="plate" :formatter="patternFormatter('AAA-####')" />
 * <!-- User sees: ABC-1234 | v-model: ABC1234 -->
 * ```
 */
export const patternFormatter = (pattern: string): InputFormatter => {
    const placeholders = pattern.match(/[#A]/g) || [];
    const maxLength = placeholders.length;

    return {
        format: (value) => {
            if (value === null || value === undefined || value === '') return '';

            let input = String(value);
            let result = '';
            let inputIndex = 0;

            for (let i = 0; i < pattern.length && inputIndex < input.length; i++) {
                const patternChar = pattern[i];

                if (patternChar === '#') {
                    // Find next digit
                    while (inputIndex < input.length && !/\d/.test(input[inputIndex])) {
                        inputIndex++;
                    }
                    if (inputIndex < input.length) {
                        result += input[inputIndex++];
                    }
                } else if (patternChar === 'A') {
                    // Find next letter
                    while (inputIndex < input.length && !/[a-zA-Z]/.test(input[inputIndex])) {
                        inputIndex++;
                    }
                    if (inputIndex < input.length) {
                        result += input[inputIndex++].toUpperCase();
                    }
                } else {
                    // Literal character in pattern
                    result += patternChar;
                }
            }

            return result;
        },
        parse: (display) => {
            // Extract only the valid characters (digits and letters based on pattern)
            let result = '';
            let displayIndex = 0;

            for (const patternChar of pattern) {
                if (displayIndex >= display.length) break;

                if (patternChar === '#' || patternChar === 'A') {
                    const char = display[displayIndex];
                    if (patternChar === '#' && /\d/.test(char)) {
                        result += char;
                    } else if (patternChar === 'A' && /[a-zA-Z]/.test(char)) {
                        result += char.toUpperCase();
                    }
                    displayIndex++;
                } else {
                    // Skip literal characters in display
                    if (display[displayIndex] === patternChar) {
                        displayIndex++;
                    }
                }
            }

            return result;
        },
    };
};

/**
 * Utility to create a custom formatter from format/parse functions.
 * Use this to create one-off formatters without implementing the full interface.
 *
 * @param format - Function to transform raw value to display
 * @param parse - Function to transform display to raw value
 * @returns An InputFormatter
 *
 * @example
 * ```typescript
 * const customFormatter = createFormatter(
 *     (value) => `$${value}`,
 *     (display) => display.replace('$', '')
 * );
 * ```
 */
export const createFormatter = (
    format: (value: string | number | null | undefined) => string,
    parse: (display: string) => string | number
): InputFormatter => ({ format, parse });
