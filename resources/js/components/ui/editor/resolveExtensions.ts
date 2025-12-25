import type { Extension } from '@tiptap/core';

/**
 * Options for resolving editor extensions.
 */
export interface ResolveExtensionsOptions {
    /** Custom extensions to add or use exclusively */
    customExtensions?: Extension[] | null;
    /** When true, use only customExtensions (ignore defaults) */
    replaceDefaults?: boolean;
    /** Default extensions for text-only mode */
    textOnlyDefaults: Extension[];
    /** Default extensions for full editor mode */
    fullDefaults: Extension[];
    /** Whether the editor is in text-only mode */
    textOnly?: boolean;
}

/**
 * Resolves the final array of TipTap extensions based on configuration.
 *
 * Resolution logic:
 * 1. If `replaceDefaults` is true and `customExtensions` is provided, use only custom extensions
 * 2. Otherwise, select defaults based on `textOnly` mode
 * 3. If `customExtensions` has items, merge them after defaults
 *
 * @param options - Configuration for extension resolution
 * @returns Array of resolved extensions
 *
 * @example
 * ```ts
 * // Use defaults only
 * resolveExtensions({
 *     textOnlyDefaults: [Document, Paragraph, Text],
 *     fullDefaults: [StarterKit],
 *     textOnly: false,
 * }); // Returns [StarterKit]
 *
 * // Merge custom with defaults
 * resolveExtensions({
 *     customExtensions: [Underline, Image],
 *     textOnlyDefaults: [Document, Paragraph, Text],
 *     fullDefaults: [StarterKit],
 *     textOnly: false,
 * }); // Returns [StarterKit, Underline, Image]
 *
 * // Replace defaults entirely
 * resolveExtensions({
 *     customExtensions: [Document, Paragraph, CustomExtension],
 *     replaceDefaults: true,
 *     textOnlyDefaults: [Document, Paragraph, Text],
 *     fullDefaults: [StarterKit],
 * }); // Returns [Document, Paragraph, CustomExtension]
 * ```
 */
export function resolveExtensions(options: ResolveExtensionsOptions): Extension[] {
    const {
        customExtensions,
        replaceDefaults = false,
        textOnlyDefaults,
        fullDefaults,
        textOnly = false,
    } = options;

    // If replacing all extensions, use only what's provided
    if (replaceDefaults && customExtensions) {
        return customExtensions;
    }

    // Select default extensions based on textOnly mode
    const defaults = textOnly ? textOnlyDefaults : fullDefaults;

    // Merge with custom extensions if provided
    if (customExtensions && customExtensions.length > 0) {
        return [...defaults, ...customExtensions];
    }

    return defaults;
}
