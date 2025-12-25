/**
 * Processes dropzone file input into validated files, errors, and the next model value.
 *
 * This keeps validation and max-files logic pure so it can be unit-tested without
 * mounting UI components.
 */
import { formatBytes } from '@/utils/format';
import { validateFileSize, validateFileType } from '@/utils/file';

export interface DropzoneFileError {
    type: 'size' | 'type' | 'maxFiles';
    message: string;
    file?: File;
}

export interface ProcessDropzoneFilesParams {
    newFiles: File[];
    existingFiles?: File[];
    accept?: string;
    maxSize?: number;
    maxFiles?: number;
    multiple?: boolean;
}

export interface ProcessDropzoneFilesResult {
    validFiles: File[];
    errors: DropzoneFileError[];
    nextValue: File | File[] | null | undefined;
}

export const processDropzoneFiles = ({
    newFiles,
    existingFiles = [],
    accept,
    maxSize,
    maxFiles,
    multiple = false,
}: ProcessDropzoneFilesParams): ProcessDropzoneFilesResult => {
    const errors: DropzoneFileError[] = [];

    const validFiles = newFiles.filter((file) => {
        if (maxSize && !validateFileSize(file, maxSize)) {
            errors.push({
                type: 'size',
                message: `File "${file.name}" exceeds maximum size of ${formatBytes(maxSize, 1)}`,
                file,
            });
            return false;
        }

        if (accept && !validateFileType(file, accept)) {
            errors.push({
                type: 'type',
                message: `File "${file.name}" is not an accepted file type`,
                file,
            });
            return false;
        }

        return true;
    });

    if (validFiles.length === 0) {
        return {
            validFiles: [],
            errors,
            nextValue: undefined,
        };
    }

    let trimmedValidFiles = validFiles;

    if (multiple && maxFiles) {
        const availableSlots = maxFiles - existingFiles.length;

        if (availableSlots <= 0) {
            return {
                validFiles: [],
                errors: [
                    ...errors,
                    {
                        type: 'maxFiles',
                        message: `Maximum of ${maxFiles} files allowed`,
                    },
                ],
                nextValue: undefined,
            };
        }

        if (validFiles.length > availableSlots) {
            trimmedValidFiles = validFiles.slice(0, availableSlots);
        }
    }

    const nextValue = multiple ? [...existingFiles, ...trimmedValidFiles] : trimmedValidFiles[0];

    return {
        validFiles: trimmedValidFiles,
        errors,
        nextValue,
    };
};
