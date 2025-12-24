import { describe, it, expect } from 'vitest';
import { normalizeFiles, getFileNames, hasFiles } from '@/utils/file/normalizeFiles';

// Helper to create mock File
function createMockFile(name: string): File {
    return new File(['content'], name, { type: 'text/plain' });
}

describe('normalizeFiles', () => {
    it('returns array with single file when given a File', () => {
        const file = createMockFile('test.txt');
        const result = normalizeFiles(file);

        expect(result).toHaveLength(1);
        expect(result[0]).toBe(file);
    });

    it('returns same array when given array of files', () => {
        const files = [createMockFile('a.txt'), createMockFile('b.txt')];
        const result = normalizeFiles(files);

        expect(result).toHaveLength(2);
        expect(result).toEqual(files);
    });

    it('returns empty array for null', () => {
        expect(normalizeFiles(null)).toEqual([]);
    });

    it('returns empty array for undefined', () => {
        expect(normalizeFiles(undefined)).toEqual([]);
    });

    it('returns empty array for empty array', () => {
        expect(normalizeFiles([])).toEqual([]);
    });
});

describe('getFileNames', () => {
    it('returns file name for single file', () => {
        const file = createMockFile('document.pdf');
        expect(getFileNames(file)).toBe('document.pdf');
    });

    it('returns comma-separated names for multiple files', () => {
        const files = [
            createMockFile('photo.jpg'),
            createMockFile('document.pdf'),
        ];
        expect(getFileNames(files)).toBe('photo.jpg, document.pdf');
    });

    it('returns empty string for null', () => {
        expect(getFileNames(null)).toBe('');
    });

    it('returns empty string for undefined', () => {
        expect(getFileNames(undefined)).toBe('');
    });

    it('returns empty string for empty array', () => {
        expect(getFileNames([])).toBe('');
    });
});

describe('hasFiles', () => {
    it('returns true for single file', () => {
        const file = createMockFile('test.txt');
        expect(hasFiles(file)).toBe(true);
    });

    it('returns true for array with files', () => {
        const files = [createMockFile('a.txt'), createMockFile('b.txt')];
        expect(hasFiles(files)).toBe(true);
    });

    it('returns false for null', () => {
        expect(hasFiles(null)).toBe(false);
    });

    it('returns false for undefined', () => {
        expect(hasFiles(undefined)).toBe(false);
    });

    it('returns false for empty array', () => {
        expect(hasFiles([])).toBe(false);
    });
});
