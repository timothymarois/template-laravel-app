/**
 * Utilities for confirmation-dialog keyboard handling.
 *
 * Extracted as pure helpers so the logic can be unit-tested without mounting the
 * dialog, which reka renders through a portal onto document.body.
 */

/** Elements whose own Enter handling must win over confirming the dialog. */
const FIELD_TAGS = ['INPUT', 'TEXTAREA', 'SELECT'];

/**
 * Whether an Enter keypress inside a confirmation dialog should confirm it.
 *
 * False while the dialog is busy, so a held Enter cannot submit the same action
 * twice, and false when the keypress came from a field — the default slot can
 * carry one (a "type the name to confirm" input), and the keystroke meant to
 * fill it must not also accept the dialog.
 */
export const shouldConfirmOnEnter = (target: EventTarget | null, loading = false): boolean => {
    if (loading) return false;

    const tagName = (target as HTMLElement | null)?.tagName;

    if (tagName && FIELD_TAGS.includes(tagName)) return false;

    return true;
};
