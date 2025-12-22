import { describe, it, expect, beforeEach, vi } from 'vitest';
import { useModal } from '@/composables/useModal';

describe('useModal', () => {
    let modal: ReturnType<typeof useModal>;

    beforeEach(() => {
        // Get a fresh instance for each test
        modal = useModal();
        // Close all modals from previous tests
        modal.closeAll();
    });

    describe('open and close', () => {
        it('opens a modal', () => {
            modal.open('test-modal');
            expect(modal.activeState('test-modal').value).toBe(true);
        });

        it('opens a modal with data', () => {
            const testData = { id: 1, name: 'Test' };
            modal.open('test-modal', testData);

            expect(modal.activeState('test-modal').value).toBe(true);
            expect(modal.data('test-modal').value).toEqual(testData);
        });

        it('closes a modal', () => {
            modal.open('test-modal');
            modal.close('test-modal');

            expect(modal.activeState('test-modal').value).toBe(false);
        });

        it('preserves data after closing', () => {
            const testData = { id: 1 };
            modal.open('test-modal', testData);
            modal.close('test-modal');

            expect(modal.data('test-modal').value).toEqual(testData);
        });

        it('does nothing when closing non-existent modal', () => {
            // Should not throw
            expect(() => modal.close('non-existent')).not.toThrow();
        });
    });

    describe('closeAll', () => {
        it('closes all open modals', () => {
            modal.open('modal-1');
            modal.open('modal-2');
            modal.open('modal-3');

            modal.closeAll();

            expect(modal.activeState('modal-1').value).toBe(false);
            expect(modal.activeState('modal-2').value).toBe(false);
            expect(modal.activeState('modal-3').value).toBe(false);
        });
    });

    describe('activeState', () => {
        it('returns false for non-existent modal', () => {
            expect(modal.activeState('non-existent').value).toBe(false);
        });

        it('can be used as v-model setter to open', () => {
            const state = modal.activeState('test-modal');
            state.value = true;

            expect(modal.activeState('test-modal').value).toBe(true);
        });

        it('can be used as v-model setter to close', () => {
            modal.open('test-modal');
            const state = modal.activeState('test-modal');
            state.value = false;

            expect(modal.activeState('test-modal').value).toBe(false);
        });
    });

    describe('data', () => {
        it('returns null for non-existent modal', () => {
            expect(modal.data('non-existent').value).toBeNull();
        });

        it('returns null for modal opened without data', () => {
            modal.open('test-modal');
            expect(modal.data('test-modal').value).toBeNull();
        });
    });

    describe('isOpen', () => {
        it('returns false when no modals are open', () => {
            expect(modal.isOpen.value).toBe(false);
        });

        it('returns true when any modal is open', () => {
            modal.open('test-modal');
            expect(modal.isOpen.value).toBe(true);
        });

        it('returns false when all modals are closed', () => {
            modal.open('modal-1');
            modal.open('modal-2');
            modal.close('modal-1');
            modal.close('modal-2');

            expect(modal.isOpen.value).toBe(false);
        });
    });

    describe('callbacks', () => {
        it('calls onOpen callback when modal opens', () => {
            const callback = vi.fn();
            const testData = { id: 1 };

            modal.onOpen('test-modal', callback);
            modal.open('test-modal', testData);

            expect(callback).toHaveBeenCalledWith(testData);
        });

        it('calls onClose callback when modal closes', () => {
            const callback = vi.fn();
            const testData = { id: 1 };

            modal.onClose('test-modal', callback);
            modal.open('test-modal', testData);
            modal.close('test-modal');

            expect(callback).toHaveBeenCalledWith(testData);
        });

        it('calls multiple onOpen callbacks', () => {
            const callback1 = vi.fn();
            const callback2 = vi.fn();

            modal.onOpen('test-modal', callback1);
            modal.onOpen('test-modal', callback2);
            modal.open('test-modal');

            expect(callback1).toHaveBeenCalled();
            expect(callback2).toHaveBeenCalled();
        });
    });
});
