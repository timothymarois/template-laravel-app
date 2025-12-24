/**
 * Normalizes a file input value to always be an array.
 *
 * @param value - A File, array of Files, or null/undefined
 * @returns An array of Files (empty array if no files)
 *
 * @example
 * normalizeFiles(singleFile) // [singleFile]
 * normalizeFiles([file1, file2]) // [file1, file2]
 * normalizeFiles(null) // []
 */
export function normalizeFiles(value: File | File[] | null | undefined): File[] {
    if (!value) return [];
    if (Array.isArray(value)) return value;
    return [value];
}

/**
 * Gets a comma-separated string of file names.
 *
 * @param value - A File, array of Files, or null/undefined
 * @returns Comma-separated file names or empty string
 *
 * @example
 * getFileNames(file) // 'document.pdf'
 * getFileNames([file1, file2]) // 'photo.jpg, document.pdf'
 * getFileNames(null) // ''
 */
export function getFileNames(value: File | File[] | null | undefined): string {
    const files = normalizeFiles(value);
    return files.map(f => f.name).join(', ');
}

/**
 * Checks if a file value contains any files.
 *
 * @param value - A File, array of Files, or null/undefined
 * @returns true if there are files, false otherwise
 *
 * @example
 * hasFiles(file) // true
 * hasFiles([]) // false
 * hasFiles(null) // false
 */
export function hasFiles(value: File | File[] | null | undefined): boolean {
    return normalizeFiles(value).length > 0;
}
