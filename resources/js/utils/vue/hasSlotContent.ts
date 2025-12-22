import { Slot } from 'vue';

/**
 * Checks whether a Vue 3 slot contains any content.
 *
 * @param slot - The slot function to evaluate
 * @returns True if the slot contains visible content
 */
export const hasSlotContent = (slot?: Slot): boolean => {
    if (!slot) return false;

    const content = slot();
    if (!Array.isArray(content) || content.length === 0) return false;

    return content.some((node) => {
        return (
            typeof node.type === 'object' ||
            (node.children && String(node.children).trim() !== '')
        );
    });
};
