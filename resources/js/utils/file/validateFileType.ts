import { getFileExtension } from './getFileExtension';

/**
 * Validates that a file matches the accepted file types.
 * Supports MIME types, wildcards (image/*), and extensions (.jpg).
 *
 * @param file - The file to validate
 * @param accept - Comma-separated list of accepted types (e.g., "image/*,.pdf,application/json")
 * @returns true if file type is accepted, false otherwise
 *
 * @example
 * validateFileType(file, 'image/*,.pdf') // accepts images and PDFs
 * validateFileType(file, '.jpg,.png') // accepts only jpg and png
 */
export function validateFileType(file: File, accept: string): boolean {
    if (!file || !accept || typeof accept !== 'string') {
        return false;
    }

    const acceptedTypes = accept.split(',').map(t => t.trim().toLowerCase());
    const fileType = file.type.toLowerCase();
    const fileExtension = getFileExtension(file.name);

    return acceptedTypes.some(type => {
        // Extension match (e.g., ".jpg")
        if (type.startsWith('.')) {
            return fileExtension === type;
        }
        // Wildcard MIME type (e.g., "image/*")
        if (type.endsWith('/*')) {
            const baseType = type.replace('/*', '/');
            return fileType.startsWith(baseType);
        }
        // Exact MIME type match
        return fileType === type;
    });
}
