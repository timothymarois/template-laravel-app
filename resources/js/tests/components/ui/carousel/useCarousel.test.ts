import { beforeEach, describe, expect, it, vi } from 'vitest';
import { defineComponent, h, ref } from 'vue';
import { mount } from '@vue/test-utils';

const scrollPrev = vi.fn();
const scrollNext = vi.fn();
const on = vi.fn();
const canScrollNext = vi.fn(() => true);
const canScrollPrev = vi.fn(() => false);
const emblaFactory = vi.fn();

vi.mock('embla-carousel-vue', () => ({
    default: (...args: unknown[]) => emblaFactory(...args),
}));

import { useCarousel, useProvideCarousel } from '@/components/ui/carousel/useCarousel';

/** A fake embla api that records the handlers the composable registers. */
function fakeApi() {
    return { scrollPrev, scrollNext, on, canScrollNext, canScrollPrev };
}

beforeEach(() => {
    vi.clearAllMocks();
    emblaFactory.mockImplementation(() => [ref(null), ref(fakeApi())]);
});

function mountCarousel(props = {}, emits = vi.fn()) {
    let inner: ReturnType<typeof useCarousel> | undefined;

    const Child = defineComponent({
        setup() {
            inner = useCarousel();

            return () => null;
        },
    });

    const wrapper = mount(
        defineComponent({
            setup() {
                useProvideCarousel({ orientation: 'horizontal', ...props } as never, emits as never);

                return () => h(Child);
            },
        }),
    );

    return { inner: inner!, wrapper, emits };
}

describe('useProvideCarousel', () => {
    it('maps a horizontal orientation onto embla\'s x axis', () => {
        mountCarousel({ orientation: 'horizontal' });

        expect(emblaFactory.mock.calls[0][0]).toMatchObject({ axis: 'x' });
    });

    it('maps a vertical orientation onto embla\'s y axis', () => {
        mountCarousel({ orientation: 'vertical' });

        expect(emblaFactory.mock.calls[0][0]).toMatchObject({ axis: 'y' });
    });

    it('passes caller options and plugins through to embla', () => {
        const plugins = [{ name: 'autoplay' }];
        mountCarousel({ opts: { loop: true }, plugins });

        expect(emblaFactory.mock.calls[0][0]).toMatchObject({ loop: true });
        expect(emblaFactory.mock.calls[0][1]).toBe(plugins);
    });

    it('subscribes to init, reInit and select', () => {
        mountCarousel();

        expect(on.mock.calls.map((call) => call[0])).toEqual(['init', 'reInit', 'select']);
    });

    it('emits init-api once the api exists', () => {
        const emits = vi.fn();
        mountCarousel({}, emits);

        expect(emits).toHaveBeenCalledWith('init-api', expect.anything());
    });

    it('does nothing when embla never produced an api', () => {
        emblaFactory.mockImplementation(() => [ref(null), ref(null)]);
        const emits = vi.fn();

        expect(() => mountCarousel({}, emits)).not.toThrow();
        expect(on).not.toHaveBeenCalled();
        expect(emits).not.toHaveBeenCalled();
    });
});

describe('useCarousel', () => {
    it('delegates scrollPrev and scrollNext to the embla api', () => {
        const { inner } = mountCarousel();

        inner.scrollPrev();
        inner.scrollNext();

        expect(scrollPrev).toHaveBeenCalledTimes(1);
        expect(scrollNext).toHaveBeenCalledTimes(1);
    });

    it('tracks scrollability from the api when a select fires', () => {
        const { inner } = mountCarousel();
        const onSelect = on.mock.calls.find((call) => call[0] === 'select')![1] as (api: unknown) => void;

        onSelect(fakeApi());

        expect(inner.canScrollNext.value).toBe(true);
        expect(inner.canScrollPrev.value).toBe(false);
    });

    it('reports both directions as unscrollable when handed no api', () => {
        const { inner } = mountCarousel();
        const onSelect = on.mock.calls.find((call) => call[0] === 'select')![1] as (api: unknown) => void;

        onSelect(null);

        expect(inner.canScrollNext.value).toBe(false);
        expect(inner.canScrollPrev.value).toBe(false);
    });

    it('throws when used outside a Carousel', () => {
        const Orphan = defineComponent({
            setup() {
                useCarousel();

                return () => null;
            },
        });

        expect(() => mount(Orphan)).toThrow(/must be used within a <Carousel \/>/);
    });
});
