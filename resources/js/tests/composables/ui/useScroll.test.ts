import { describe, it, expect, beforeEach, afterEach, vi } from 'vitest';
import { ref } from 'vue';
import { useScroll } from '@/composables/ui/useScroll';

describe('useScroll', () => {
    beforeEach(() => {
        // Reset body styles
        document.documentElement.style.overflowY = '';
        document.body.style.overflowY = '';
    });

    afterEach(() => {
        // Clean up
        document.documentElement.style.overflowY = '';
        document.body.style.overflowY = '';
    });

    describe('isTop', () => {
        it('initializes isTop as true', () => {
            const { isTop } = useScroll('test-1');
            expect(isTop.value).toBe(true);
        });

        it('setTop updates isTop value', () => {
            const { isTop, setTop } = useScroll('test-2');

            setTop(false);
            expect(isTop.value).toBe(false);

            setTop(true);
            expect(isTop.value).toBe(true);
        });
    });

    describe('lockScroll', () => {
        it('locks scroll by setting overflow hidden', () => {
            const { lockScroll } = useScroll('test-3');

            lockScroll();

            expect(document.documentElement.style.overflowY).toBe('hidden');
            expect(document.body.style.overflowY).toBe('hidden');
        });

        it('unlocks scroll by removing overflow hidden', () => {
            const { lockScroll, unlockScroll } = useScroll('test-4');

            lockScroll();
            unlockScroll();

            expect(document.documentElement.style.overflowY).toBe('');
            expect(document.body.style.overflowY).toBe('');
        });
    });

    describe('bindScrollHandler', () => {
        it('returns add and remove functions', () => {
            const { bindScrollHandler } = useScroll('test-5');
            const elementRef = ref<HTMLElement | null>(null);

            const handler = bindScrollHandler(elementRef);

            expect(handler).toHaveProperty('add');
            expect(handler).toHaveProperty('remove');
            expect(typeof handler.add).toBe('function');
            expect(typeof handler.remove).toBe('function');
        });

        it('adds scroll event listener', () => {
            const { bindScrollHandler } = useScroll('test-6');
            const mockElement = {
                addEventListener: vi.fn(),
                removeEventListener: vi.fn(),
                scrollTop: 0,
            } as unknown as HTMLElement;
            const elementRef = ref<HTMLElement | null>(mockElement);

            const { add } = bindScrollHandler(elementRef);
            add();

            expect(mockElement.addEventListener).toHaveBeenCalledWith('scroll', expect.any(Function));
        });

        it('removes scroll event listener', () => {
            const { bindScrollHandler } = useScroll('test-7');
            const mockElement = {
                addEventListener: vi.fn(),
                removeEventListener: vi.fn(),
                scrollTop: 0,
            } as unknown as HTMLElement;
            const elementRef = ref<HTMLElement | null>(mockElement);

            const { add, remove } = bindScrollHandler(elementRef);
            add();
            remove();

            expect(mockElement.removeEventListener).toHaveBeenCalledWith('scroll', expect.any(Function));
        });

        it('does nothing when elementRef is null', () => {
            const { bindScrollHandler } = useScroll('test-8');
            const elementRef = ref<HTMLElement | null>(null);

            const { add, remove } = bindScrollHandler(elementRef);

            // Should not throw
            expect(() => add()).not.toThrow();
            expect(() => remove()).not.toThrow();
        });
    });

    describe('shared state', () => {
        it('maintains separate state for different IDs', () => {
            const scroll1 = useScroll('unique-1');
            const scroll2 = useScroll('unique-2');

            scroll1.setTop(false);

            expect(scroll1.isTop.value).toBe(false);
            expect(scroll2.isTop.value).toBe(true);
        });

        it('shares state for same ID across calls', () => {
            const scroll1 = useScroll('shared-id');
            const scroll2 = useScroll('shared-id');

            scroll1.setTop(false);

            expect(scroll2.isTop.value).toBe(false);
        });
    });
});
