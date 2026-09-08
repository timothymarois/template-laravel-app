import { describe, it, expect } from 'vitest';
import { shouldConfirmOnEnter } from '@/components/ui/dialog/dialogUtils';

const element = (tag: string): EventTarget => document.createElement(tag);

describe('shouldConfirmOnEnter', () => {
    it('confirms on Enter from the dialog body', () => {
        expect(shouldConfirmOnEnter(element('div'))).toBe(true);
        expect(shouldConfirmOnEnter(element('p'))).toBe(true);
        expect(shouldConfirmOnEnter(element('button'))).toBe(true);
    });

    it('does not confirm while the dialog is busy', () => {
        // Guards against a held Enter submitting the same action twice.
        expect(shouldConfirmOnEnter(element('div'), true)).toBe(false);
    });

    it('does not confirm from a field', () => {
        // The default slot can carry a "type the name to confirm" input; the
        // keystroke meant to fill it must not also accept the dialog.
        for (const tag of ['input', 'textarea', 'select']) {
            expect(shouldConfirmOnEnter(element(tag))).toBe(false);
        }
    });

    it('confirms when there is no target at all', () => {
        expect(shouldConfirmOnEnter(null)).toBe(true);
    });
});
