import { twMerge } from 'tailwind-merge';
import { mergeProps } from 'vue';

/**
 * Merges global and local PT (PassThrough) props with Tailwind classes.
 * Supports tailwind-merge and custom dataset attributes.
 *
 * @param globalPTProps - Global PT prop object
 * @param selfPTProps - Component-specific PT props
 * @param datasets - Additional data-* attributes
 * @returns Merged props object
 */
export const ptViewMerge = (
    globalPTProps: Record<string, unknown> = {},
    selfPTProps: Record<string, unknown> = {},
    datasets: Record<string, unknown> = {}
): Record<string, unknown> => {
    const { class: globalClass, ...globalRest } = globalPTProps;
    const { class: selfClass, ...selfRest } = selfPTProps;

    const mergedClass =
        !globalClass && !selfClass
            ? undefined
            : twMerge(String(globalClass ?? ''), String(selfClass ?? ''));

    return mergeProps({ class: mergedClass }, globalRest, selfRest, datasets);
};
