import type { DateValue } from 'reka-ui';
import { CalendarDate } from '@internationalized/date';

/**
 * Utilities for parsing and formatting MM/DD/YYYY date inputs.
 *
 * These helpers keep date input logic pure so it can be unit-tested without
 * mounting UI components.
 */

export interface DateInputParseOptions {
    minYear?: number;
    maxYear?: number;
}

export const formatDateInput = (value: string): string => {
    const digits = value.replace(/\D/g, '');
    const limited = digits.slice(0, 8);

    if (limited.length <= 2) return limited;
    if (limited.length <= 4) return `${limited.slice(0, 2)}/${limited.slice(2)}`;
    return `${limited.slice(0, 2)}/${limited.slice(2, 4)}/${limited.slice(4)}`;
};

export const parseDateInput = (
    value: string,
    { minYear = 1900, maxYear = 2100 }: DateInputParseOptions = {}
): DateValue | null => {
    const cleaned = value.replace(/[^\d/]/g, '');
    const parts = cleaned.split('/');

    if (parts.length !== 3) return null;

    const month = parseInt(parts[0], 10);
    const day = parseInt(parts[1], 10);
    const year = parseInt(parts[2], 10);

    if (isNaN(month) || isNaN(day) || isNaN(year)) return null;
    if (month < 1 || month > 12) return null;
    if (day < 1 || day > 31) return null;
    if (year < minYear || year > maxYear) return null;

    const daysInMonth = new Date(year, month, 0).getDate();
    if (day > daysInMonth) return null;

    try {
        return new CalendarDate(year, month, day);
    } catch {
        return null;
    }
};
