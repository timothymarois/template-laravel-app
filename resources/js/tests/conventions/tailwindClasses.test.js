import { describe, expect, it } from 'vitest';
import { readdirSync, readFileSync, statSync } from 'node:fs';
import { join } from 'node:path';

/**
 * Tailwind emits a class only when its literal text appears in a scanned source file, and
 * emits nothing at all for a name that is not a utility. `text-md` shipped in five places
 * and produced no rule: the element silently inherited its parent's size and no tool
 * complained. These are the names that read as real Tailwind and are not.
 */
const NOT_UTILITIES = [
    'text-md',
    'text-normal',
    'font-regular',
    'font-heavy',
    'flex-center',
    'grid-center',
];

const ROOT = 'resources/js';

function sourceFiles(dir) {
    return readdirSync(dir).flatMap((entry) => {
        const full = join(dir, entry);
        if (statSync(full).isDirectory()) {
            return entry === 'tests' ? [] : sourceFiles(full);
        }
        return /\.(vue|js|ts)$/.test(full) ? [full] : [];
    });
}

describe('Tailwind class names', () => {
    const files = sourceFiles(ROOT);

    it('scans a non-empty set of source files', () => {
        // A discovery bug would make every assertion below vacuously true.
        expect(files.length).toBeGreaterThan(100);
    });

    it.each(NOT_UTILITIES)('does not use %s, which emits no CSS rule', (className) => {
        const pattern = new RegExp(`(?:^|[\\s"'\`:])${className}(?:$|[\\s"'\`])`);

        const offenders = files.filter((file) => pattern.test(readFileSync(file, 'utf8')));

        expect(offenders).toEqual([]);
    });
});
