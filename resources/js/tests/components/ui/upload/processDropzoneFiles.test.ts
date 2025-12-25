import { describe, it, expect } from 'vitest';
import { processDropzoneFiles } from '@/components/ui/upload/processDropzoneFiles';
import { formatBytes } from '@/utils/format/formatBytes';

const createMockFile = (name: string, type: string, size = 1): File => {
    return new File([new Uint8Array(size)], name, { type });
};

describe('processDropzoneFiles', () => {
    it('returns nextValue for single file selection', () => {
        const file = createMockFile('readme.txt', 'text/plain', 12);

        const result = processDropzoneFiles({ newFiles: [file] });

        expect(result.errors).toHaveLength(0);
        expect(result.validFiles).toEqual([file]);
        expect(result.nextValue).toBe(file);
    });

    it('returns size error and no nextValue for oversized files', () => {
        const file = createMockFile('big.bin', 'application/octet-stream', 200);
        const maxSize = 100;

        const result = processDropzoneFiles({ newFiles: [file], maxSize });

        expect(result.validFiles).toEqual([]);
        expect(result.nextValue).toBeUndefined();
        expect(result.errors).toHaveLength(1);
        expect(result.errors[0]).toMatchObject({
            type: 'size',
            file,
        });
        expect(result.errors[0].message).toBe(
            `File "${file.name}" exceeds maximum size of ${formatBytes(maxSize, 1)}`
        );
    });

    it('returns type error and no nextValue for invalid file types', () => {
        const file = createMockFile('photo.jpg', 'image/jpeg', 10);

        const result = processDropzoneFiles({ newFiles: [file], accept: '.png' });

        expect(result.validFiles).toEqual([]);
        expect(result.nextValue).toBeUndefined();
        expect(result.errors).toHaveLength(1);
        expect(result.errors[0]).toMatchObject({
            type: 'type',
            file,
        });
        expect(result.errors[0].message).toBe(
            `File "${file.name}" is not an accepted file type`
        );
    });

    it('returns maxFiles error when no slots remain', () => {
        const existingFiles = [
            createMockFile('a.txt', 'text/plain', 1),
            createMockFile('b.txt', 'text/plain', 1),
        ];
        const incomingFile = createMockFile('c.txt', 'text/plain', 1);

        const result = processDropzoneFiles({
            newFiles: [incomingFile],
            existingFiles,
            multiple: true,
            maxFiles: 2,
        });

        expect(result.validFiles).toEqual([]);
        expect(result.nextValue).toBeUndefined();
        expect(result.errors).toEqual([
            {
                type: 'maxFiles',
                message: 'Maximum of 2 files allowed',
            },
        ]);
    });

    it('trims files when exceeding available slots in multiple mode', () => {
        const existingFiles = [createMockFile('a.txt', 'text/plain', 1)];
        const newFiles = [
            createMockFile('b.txt', 'text/plain', 1),
            createMockFile('c.txt', 'text/plain', 1),
            createMockFile('d.txt', 'text/plain', 1),
        ];

        const result = processDropzoneFiles({
            newFiles,
            existingFiles,
            multiple: true,
            maxFiles: 2,
        });

        expect(result.errors).toHaveLength(0);
        expect(result.validFiles).toEqual([newFiles[0]]);
        expect(result.nextValue).toEqual([...existingFiles, newFiles[0]]);
    });

    it('keeps valid files when some fail validation', () => {
        const validFile = createMockFile('ok.png', 'image/png', 10);
        const invalidFile = createMockFile('bad.jpg', 'image/jpeg', 10);

        const result = processDropzoneFiles({
            newFiles: [invalidFile, validFile],
            accept: '.png',
            multiple: true,
        });

        expect(result.errors).toHaveLength(1);
        expect(result.validFiles).toEqual([validFile]);
        expect(result.nextValue).toEqual([validFile]);
    });

    it('returns the first valid file when multiple is false', () => {
        const firstFile = createMockFile('first.txt', 'text/plain', 1);
        const secondFile = createMockFile('second.txt', 'text/plain', 1);

        const result = processDropzoneFiles({
            newFiles: [firstFile, secondFile],
            multiple: false,
        });

        expect(result.errors).toHaveLength(0);
        expect(result.validFiles).toEqual([firstFile, secondFile]);
        expect(result.nextValue).toBe(firstFile);
    });

    it('appends to existing files when multiple is true and no maxFiles is set', () => {
        const existingFiles = [createMockFile('a.txt', 'text/plain', 1)];
        const newFiles = [
            createMockFile('b.txt', 'text/plain', 1),
            createMockFile('c.txt', 'text/plain', 1),
        ];

        const result = processDropzoneFiles({
            newFiles,
            existingFiles,
            multiple: true,
        });

        expect(result.errors).toHaveLength(0);
        expect(result.validFiles).toEqual(newFiles);
        expect(result.nextValue).toEqual([...existingFiles, ...newFiles]);
    });

    it('returns undefined when no files are provided', () => {
        const result = processDropzoneFiles({ newFiles: [] });

        expect(result.errors).toHaveLength(0);
        expect(result.validFiles).toEqual([]);
        expect(result.nextValue).toBeUndefined();
    });

    it('ignores maxFiles when multiple is false', () => {
        const file = createMockFile('single.txt', 'text/plain', 1);
        const existingFiles = [createMockFile('existing.txt', 'text/plain', 1)];

        const result = processDropzoneFiles({
            newFiles: [file],
            existingFiles,
            multiple: false,
            maxFiles: 1,
        });

        expect(result.errors).toHaveLength(0);
        expect(result.validFiles).toEqual([file]);
        expect(result.nextValue).toBe(file);
    });

    it('accumulates errors for multiple invalid files', () => {
        const oversizedFile = createMockFile('big.bin', 'application/octet-stream', 10);
        const invalidTypeFile = createMockFile('bad.jpg', 'image/jpeg', 1);

        const result = processDropzoneFiles({
            newFiles: [oversizedFile, invalidTypeFile],
            maxSize: 5,
            accept: '.png',
        });

        expect(result.validFiles).toEqual([]);
        expect(result.nextValue).toBeUndefined();
        expect(result.errors).toHaveLength(2);
        expect(result.errors[0]).toMatchObject({
            type: 'size',
            file: oversizedFile,
        });
        expect(result.errors[1]).toMatchObject({
            type: 'type',
            file: invalidTypeFile,
        });
    });
});
