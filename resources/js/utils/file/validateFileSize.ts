/**
 * Validates that a file does not exceed the maximum size.
 *
 * @param file - The file to validate
 * @param maxSize - Maximum allowed size in bytes
 * @returns true if file size is within limit, false otherwise
 *
 * @example
 * validateFileSize(file, 5 * 1024 * 1024) // 5MB limit
 */
export function validateFileSize(file: File, maxSize: number): boolean {
    if (!file || typeof maxSize !== 'number' || maxSize <= 0) {
        return false;
    }
    return file.size <= maxSize;
}
