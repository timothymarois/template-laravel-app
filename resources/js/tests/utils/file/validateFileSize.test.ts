import { describe, it, expect } from 'vitest';
import { validateFileSize } from '@/utils/file/validateFileSize';

// Helper to create mock File
function createMockFile(size: number, name: string = 'test.txt'): File {
    const content = new Array(size).fill('a').join('');
    return new File([content], name, { type: 'text/plain' });
}

describe('validateFileSize', () => {
    it('returns true when file size is under limit', () => {
        const file = createMockFile(100);
        expect(validateFileSize(file, 200)).toBe(true);
    });

    it('returns true when file size equals limit', () => {
        const file = createMockFile(100);
        expect(validateFileSize(file, 100)).toBe(true);
    });

    it('returns false when file size exceeds limit', () => {
        const file = createMockFile(200);
        expect(validateFileSize(file, 100)).toBe(false);
    });

    it('returns false for null file', () => {
        expect(validateFileSize(null as unknown as File, 100)).toBe(false);
    });

    it('returns false for undefined file', () => {
        expect(validateFileSize(undefined as unknown as File, 100)).toBe(false);
    });

    it('returns false for zero maxSize', () => {
        const file = createMockFile(100);
        expect(validateFileSize(file, 0)).toBe(false);
    });

    it('returns false for negative maxSize', () => {
        const file = createMockFile(100);
        expect(validateFileSize(file, -100)).toBe(false);
    });

    it('returns false for non-numeric maxSize', () => {
        const file = createMockFile(100);
        expect(validateFileSize(file, 'abc' as unknown as number)).toBe(false);
    });

    it('handles large file sizes', () => {
        const file = createMockFile(5 * 1024 * 1024); // 5MB
        expect(validateFileSize(file, 10 * 1024 * 1024)).toBe(true); // 10MB limit
        expect(validateFileSize(file, 1 * 1024 * 1024)).toBe(false); // 1MB limit
    });
});
