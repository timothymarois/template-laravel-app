import { describe, it, expect } from 'vitest';
import { validateFileType } from '@/utils/file/validateFileType';

// Helper to create mock File with type
function createMockFile(name: string, type: string): File {
    return new File(['content'], name, { type });
}

describe('validateFileType', () => {
    describe('extension matching', () => {
        it('returns true for matching extension', () => {
            const file = createMockFile('photo.jpg', 'image/jpeg');
            expect(validateFileType(file, '.jpg')).toBe(true);
        });

        it('returns true for case-insensitive extension', () => {
            const file = createMockFile('photo.JPG', 'image/jpeg');
            expect(validateFileType(file, '.jpg')).toBe(true);
        });

        it('returns false for non-matching extension', () => {
            const file = createMockFile('photo.jpg', 'image/jpeg');
            expect(validateFileType(file, '.png')).toBe(false);
        });

        it('handles multiple extensions', () => {
            const file = createMockFile('photo.jpg', 'image/jpeg');
            expect(validateFileType(file, '.png,.jpg,.gif')).toBe(true);
        });
    });

    describe('MIME type matching', () => {
        it('returns true for exact MIME type match', () => {
            const file = createMockFile('data.json', 'application/json');
            expect(validateFileType(file, 'application/json')).toBe(true);
        });

        it('returns false for non-matching MIME type', () => {
            const file = createMockFile('data.json', 'application/json');
            expect(validateFileType(file, 'text/plain')).toBe(false);
        });
    });

    describe('wildcard MIME type matching', () => {
        it('returns true for matching wildcard', () => {
            const file = createMockFile('photo.jpg', 'image/jpeg');
            expect(validateFileType(file, 'image/*')).toBe(true);
        });

        it('returns true for any image type with wildcard', () => {
            expect(validateFileType(createMockFile('a.png', 'image/png'), 'image/*')).toBe(true);
            expect(validateFileType(createMockFile('b.gif', 'image/gif'), 'image/*')).toBe(true);
            expect(validateFileType(createMockFile('c.webp', 'image/webp'), 'image/*')).toBe(true);
        });

        it('returns false for non-matching wildcard', () => {
            const file = createMockFile('doc.pdf', 'application/pdf');
            expect(validateFileType(file, 'image/*')).toBe(false);
        });
    });

    describe('combined accept strings', () => {
        it('handles mixed extensions and MIME types', () => {
            const accept = 'image/*,.pdf,application/json';

            expect(validateFileType(createMockFile('a.jpg', 'image/jpeg'), accept)).toBe(true);
            expect(validateFileType(createMockFile('b.pdf', 'application/pdf'), accept)).toBe(true);
            expect(validateFileType(createMockFile('c.json', 'application/json'), accept)).toBe(true);
            expect(validateFileType(createMockFile('d.txt', 'text/plain'), accept)).toBe(false);
        });

        it('handles whitespace in accept string', () => {
            const file = createMockFile('photo.jpg', 'image/jpeg');
            expect(validateFileType(file, '.png, .jpg, .gif')).toBe(true);
        });
    });

    describe('edge cases', () => {
        it('returns false for null file', () => {
            expect(validateFileType(null as unknown as File, '.jpg')).toBe(false);
        });

        it('returns false for empty accept string', () => {
            const file = createMockFile('photo.jpg', 'image/jpeg');
            expect(validateFileType(file, '')).toBe(false);
        });

        it('returns false for null accept', () => {
            const file = createMockFile('photo.jpg', 'image/jpeg');
            expect(validateFileType(file, null as unknown as string)).toBe(false);
        });
    });
});
