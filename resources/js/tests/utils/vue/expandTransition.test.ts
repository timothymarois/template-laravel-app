import { describe, it, expect, vi, beforeEach } from 'vitest';
import { createExpandTransition } from '@/utils/vue/expandTransition';

describe('createExpandTransition', () => {
    let mockElement: HTMLElement;

    beforeEach(() => {
        // Create a mock element with scrollHeight
        mockElement = document.createElement('div');
        Object.defineProperty(mockElement, 'scrollHeight', {
            value: 200,
            writable: true,
        });
        Object.defineProperty(mockElement, 'offsetHeight', {
            value: 0,
            writable: true,
        });
    });

    describe('expandEnter', () => {
        it('sets initial height to 0', () => {
            const { expandEnter } = createExpandTransition();
            expandEnter(mockElement);

            expect(mockElement.style.height).toBe('200px');
        });

        it('sets overflow to hidden', () => {
            const { expandEnter } = createExpandTransition();
            expandEnter(mockElement);

            expect(mockElement.style.overflow).toBe('hidden');
        });

        it('applies transition with default duration', () => {
            const { expandEnter } = createExpandTransition();
            expandEnter(mockElement);

            expect(mockElement.style.transition).toBe('height 200ms ease');
        });

        it('applies transition with custom duration', () => {
            const { expandEnter } = createExpandTransition(300);
            expandEnter(mockElement);

            expect(mockElement.style.transition).toBe('height 300ms ease');
        });

        it('sets height to scrollHeight', () => {
            const { expandEnter } = createExpandTransition();
            expandEnter(mockElement);

            expect(mockElement.style.height).toBe('200px');
        });
    });

    describe('expandLeave', () => {
        it('sets overflow to hidden', () => {
            const { expandLeave } = createExpandTransition();
            expandLeave(mockElement);

            expect(mockElement.style.overflow).toBe('hidden');
        });

        it('applies transition', () => {
            const { expandLeave } = createExpandTransition();
            expandLeave(mockElement);

            expect(mockElement.style.transition).toBe('height 200ms ease');
        });

        it('sets height to 0', () => {
            const { expandLeave } = createExpandTransition();
            expandLeave(mockElement);

            expect(mockElement.style.height).toBe('0px');
        });
    });

    describe('expandAfterEnter', () => {
        it('resets height to auto', () => {
            const { expandAfterEnter } = createExpandTransition();
            mockElement.style.height = '200px';
            expandAfterEnter(mockElement);

            expect(mockElement.style.height).toBe('auto');
        });

        it('clears overflow', () => {
            const { expandAfterEnter } = createExpandTransition();
            mockElement.style.overflow = 'hidden';
            expandAfterEnter(mockElement);

            expect(mockElement.style.overflow).toBe('');
        });

        it('clears transition', () => {
            const { expandAfterEnter } = createExpandTransition();
            mockElement.style.transition = 'height 200ms ease';
            expandAfterEnter(mockElement);

            expect(mockElement.style.transition).toBe('');
        });
    });

    describe('expandAfterLeave', () => {
        it('clears height', () => {
            const { expandAfterLeave } = createExpandTransition();
            mockElement.style.height = '0';
            expandAfterLeave(mockElement);

            expect(mockElement.style.height).toBe('');
        });

        it('clears overflow', () => {
            const { expandAfterLeave } = createExpandTransition();
            mockElement.style.overflow = 'hidden';
            expandAfterLeave(mockElement);

            expect(mockElement.style.overflow).toBe('');
        });

        it('clears transition', () => {
            const { expandAfterLeave } = createExpandTransition();
            mockElement.style.transition = 'height 200ms ease';
            expandAfterLeave(mockElement);

            expect(mockElement.style.transition).toBe('');
        });
    });

    it('returns all four functions', () => {
        const result = createExpandTransition();

        expect(result).toHaveProperty('expandEnter');
        expect(result).toHaveProperty('expandLeave');
        expect(result).toHaveProperty('expandAfterEnter');
        expect(result).toHaveProperty('expandAfterLeave');
        expect(typeof result.expandEnter).toBe('function');
        expect(typeof result.expandLeave).toBe('function');
        expect(typeof result.expandAfterEnter).toBe('function');
        expect(typeof result.expandAfterLeave).toBe('function');
    });
});
