/**
 * Extracts the file extension from a filename, including the dot.
 *
 * @param filename - The filename to extract extension from
 * @returns The lowercase extension with dot (e.g., ".jpg") or empty string if none
 *
 * @example
 * getFileExtension('photo.JPG') // '.jpg'
 * getFileExtension('document.tar.gz') // '.gz'
 * getFileExtension('noextension') // ''
 */
export function getFileExtension(filename: string): string {
    if (!filename || typeof filename !== 'string') {
        return '';
    }

    const lastDotIndex = filename.lastIndexOf('.');
    if (lastDotIndex === -1 || lastDotIndex === filename.length - 1) {
        return '';
    }

    return '.' + filename.slice(lastDotIndex + 1).toLowerCase();
}
