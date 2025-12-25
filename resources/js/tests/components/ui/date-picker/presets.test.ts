import { describe, it, expect, vi, beforeEach, afterEach } from 'vitest';
import { defaultPresets, type DateRangePreset } from '@/components/ui/date-picker/presets';

describe('defaultPresets', () => {
    beforeEach(() => {
        // Mock today as 2025-01-15 for consistent tests
        vi.useFakeTimers();
        vi.setSystemTime(new Date(2025, 0, 15)); // Jan 15, 2025
    });

    afterEach(() => {
        vi.useRealTimers();
    });

    it('includes all expected presets', () => {
        const labels = defaultPresets.map(p => p.label);
        expect(labels).toContain('Today');
        expect(labels).toContain('Yesterday');
        expect(labels).toContain('Last 7 days');
        expect(labels).toContain('Last 30 days');
        expect(labels).toContain('Last 90 days');
        expect(labels).toContain('This month');
        expect(labels).toContain('Last month');
        expect(labels).toContain('Year to date');
    });

    it('has 8 default presets', () => {
        expect(defaultPresets).toHaveLength(8);
    });

    describe('Today', () => {
        it('returns today for both start and end', () => {
            const preset = defaultPresets.find(p => p.label === 'Today')!;
            const range = preset.getValue();
            expect(range.start?.toString()).toBe('2025-01-15');
            expect(range.end?.toString()).toBe('2025-01-15');
        });
    });

    describe('Yesterday', () => {
        it('returns yesterday for both start and end', () => {
            const preset = defaultPresets.find(p => p.label === 'Yesterday')!;
            const range = preset.getValue();
            expect(range.start?.toString()).toBe('2025-01-14');
            expect(range.end?.toString()).toBe('2025-01-14');
        });
    });

    describe('Last 7 days', () => {
        it('returns 7 day range ending today', () => {
            const preset = defaultPresets.find(p => p.label === 'Last 7 days')!;
            const range = preset.getValue();
            expect(range.start?.toString()).toBe('2025-01-09');
            expect(range.end?.toString()).toBe('2025-01-15');
        });
    });

    describe('Last 30 days', () => {
        it('returns 30 day range ending today', () => {
            const preset = defaultPresets.find(p => p.label === 'Last 30 days')!;
            const range = preset.getValue();
            expect(range.start?.toString()).toBe('2024-12-17');
            expect(range.end?.toString()).toBe('2025-01-15');
        });
    });

    describe('Last 90 days', () => {
        it('returns 90 day range ending today', () => {
            const preset = defaultPresets.find(p => p.label === 'Last 90 days')!;
            const range = preset.getValue();
            expect(range.start?.toString()).toBe('2024-10-18');
            expect(range.end?.toString()).toBe('2025-01-15');
        });
    });

    describe('This month', () => {
        it('returns first of month to today', () => {
            const preset = defaultPresets.find(p => p.label === 'This month')!;
            const range = preset.getValue();
            expect(range.start?.toString()).toBe('2025-01-01');
            expect(range.end?.toString()).toBe('2025-01-15');
        });
    });

    describe('Last month', () => {
        it('returns full previous month', () => {
            const preset = defaultPresets.find(p => p.label === 'Last month')!;
            const range = preset.getValue();
            expect(range.start?.toString()).toBe('2024-12-01');
            expect(range.end?.toString()).toBe('2024-12-31');
        });
    });

    describe('Year to date', () => {
        it('returns Jan 1 to today', () => {
            const preset = defaultPresets.find(p => p.label === 'Year to date')!;
            const range = preset.getValue();
            expect(range.start?.toString()).toBe('2025-01-01');
            expect(range.end?.toString()).toBe('2025-01-15');
        });
    });
});

describe('edge cases', () => {
    afterEach(() => {
        vi.useRealTimers();
    });

    it('handles month boundary for Last 30 days', () => {
        vi.useFakeTimers();
        vi.setSystemTime(new Date(2025, 0, 5)); // Jan 5
        const preset = defaultPresets.find(p => p.label === 'Last 30 days')!;
        const range = preset.getValue();
        expect(range.start?.month).toBe(12); // December
        expect(range.start?.year).toBe(2024);
    });

    it('handles year boundary for Year to date on Jan 1', () => {
        vi.useFakeTimers();
        vi.setSystemTime(new Date(2025, 0, 1)); // Jan 1
        const preset = defaultPresets.find(p => p.label === 'Year to date')!;
        const range = preset.getValue();
        expect(range.start?.toString()).toBe('2025-01-01');
        expect(range.end?.toString()).toBe('2025-01-01');
    });

    it('handles February in leap year for Last month', () => {
        vi.useFakeTimers();
        vi.setSystemTime(new Date(2024, 2, 15)); // March 15, 2024 (leap year)
        const preset = defaultPresets.find(p => p.label === 'Last month')!;
        const range = preset.getValue();
        expect(range.start?.toString()).toBe('2024-02-01');
        expect(range.end?.toString()).toBe('2024-02-29'); // Feb has 29 days in 2024
    });

    it('handles February in non-leap year for Last month', () => {
        vi.useFakeTimers();
        vi.setSystemTime(new Date(2025, 2, 15)); // March 15, 2025 (not a leap year)
        const preset = defaultPresets.find(p => p.label === 'Last month')!;
        const range = preset.getValue();
        expect(range.start?.toString()).toBe('2025-02-01');
        expect(range.end?.toString()).toBe('2025-02-28'); // Feb has 28 days in 2025
    });

    it('handles This month on the first day', () => {
        vi.useFakeTimers();
        vi.setSystemTime(new Date(2025, 5, 1)); // June 1
        const preset = defaultPresets.find(p => p.label === 'This month')!;
        const range = preset.getValue();
        expect(range.start?.toString()).toBe('2025-06-01');
        expect(range.end?.toString()).toBe('2025-06-01');
    });

    it('handles Yesterday on Jan 1', () => {
        vi.useFakeTimers();
        vi.setSystemTime(new Date(2025, 0, 1)); // Jan 1, 2025
        const preset = defaultPresets.find(p => p.label === 'Yesterday')!;
        const range = preset.getValue();
        expect(range.start?.toString()).toBe('2024-12-31');
        expect(range.end?.toString()).toBe('2024-12-31');
    });
});

describe('DateRangePreset interface', () => {
    it('allows custom preset creation', () => {
        const customPreset: DateRangePreset = {
            label: 'Custom Range',
            getValue: () => ({
                start: { year: 2025, month: 1, day: 1 } as any,
                end: { year: 2025, month: 12, day: 31 } as any,
            }),
        };
        expect(customPreset.label).toBe('Custom Range');
        expect(typeof customPreset.getValue).toBe('function');
    });

    it('all default presets have required properties', () => {
        defaultPresets.forEach((preset) => {
            expect(typeof preset.label).toBe('string');
            expect(preset.label.length).toBeGreaterThan(0);
            expect(typeof preset.getValue).toBe('function');
        });
    });

    it('all default presets return valid date ranges', () => {
        vi.useFakeTimers();
        vi.setSystemTime(new Date(2025, 5, 15)); // June 15, 2025

        defaultPresets.forEach((preset) => {
            const range = preset.getValue();
            expect(range).toBeDefined();
            expect(range.start).toBeDefined();
            expect(range.end).toBeDefined();
            expect(range.start?.year).toBeGreaterThan(2000);
            expect(range.end?.year).toBeGreaterThan(2000);
        });

        vi.useRealTimers();
    });
});
