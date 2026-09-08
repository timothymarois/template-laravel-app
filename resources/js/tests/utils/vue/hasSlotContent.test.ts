import { describe, expect, it } from 'vitest';
import { h } from 'vue';
import { hasSlotContent } from '@/utils/vue/hasSlotContent';

describe('hasSlotContent', () => {
    it('returns false when no slot is passed', () => {
        expect(hasSlotContent()).toBe(false);
        expect(hasSlotContent(undefined)).toBe(false);
    });

    it('returns false for a slot rendering nothing', () => {
        expect(hasSlotContent(() => [])).toBe(false);
    });

    it('returns false when the slot does not return an array', () => {
        expect(hasSlotContent((() => null) as never)).toBe(false);
    });

    it('returns true for text content', () => {
        expect(hasSlotContent(() => [h('span', 'Hello')])).toBe(true);
    });

    it('returns true for a component node even with no children', () => {
        // A component's type is an object, which counts as content on its own.
        expect(hasSlotContent(() => [h({ render: () => h('i') })])).toBe(true);
    });

    it('returns false for whitespace-only text', () => {
        expect(hasSlotContent(() => [{ type: 'span', children: '   ' } as never])).toBe(false);
    });

    it('returns true when any node has content', () => {
        expect(
            hasSlotContent(() => [
                { type: 'span', children: '  ' } as never,
                { type: 'span', children: 'real' } as never,
            ]),
        ).toBe(true);
    });
});
