import { describe, it, expect } from 'vitest';
import { getFileExtension } from '@/utils/file/getFileExtension';

describe('getFileExtension', () => {
    it('returns extension with dot for standard filename', () => {
        expect(getFileExtension('photo.jpg')).toBe('.jpg');
    });

    it('returns lowercase extension', () => {
        expect(getFileExtension('photo.JPG')).toBe('.jpg');
        expect(getFileExtension('document.PDF')).toBe('.pdf');
    });

    it('returns last extension for multiple dots', () => {
        expect(getFileExtension('archive.tar.gz')).toBe('.gz');
        expect(getFileExtension('file.backup.2024.txt')).toBe('.txt');
    });

    it('returns empty string for no extension', () => {
        expect(getFileExtension('filename')).toBe('');
        expect(getFileExtension('Makefile')).toBe('');
    });

    it('returns empty string for trailing dot', () => {
        expect(getFileExtension('filename.')).toBe('');
    });

    it('returns empty string for empty input', () => {
        expect(getFileExtension('')).toBe('');
    });

    it('returns empty string for null', () => {
        expect(getFileExtension(null as unknown as string)).toBe('');
    });

    it('returns empty string for undefined', () => {
        expect(getFileExtension(undefined as unknown as string)).toBe('');
    });

    it('handles dotfiles', () => {
        expect(getFileExtension('.gitignore')).toBe('.gitignore');
        expect(getFileExtension('.env.local')).toBe('.local');
    });

    it('handles complex extensions', () => {
        expect(getFileExtension('component.vue')).toBe('.vue');
        expect(getFileExtension('types.d.ts')).toBe('.ts');
    });
});
