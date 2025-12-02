import { computed, unref, type Ref } from 'vue';
import { ptMerge } from '../utils';

export function usePrimeBindings<
    P extends { pt?: Record<string, any> },
    A extends Record<string, any>,
    K extends keyof P = never
>(
    props: P,
    attrs: A,
    theme?: Ref<Record<string, any>> | Record<string, any>,
    excludeKeys: ReadonlyArray<K> = []
) {
    const mergedPt = computed(() => ptMerge(unref(theme), props.pt));

    const bindProps = computed<A & Omit<P, 'pt' | K>>(() => {
        const { pt, ...rest } = props;
        const clone: Record<string, any> = { ...rest };
        excludeKeys.forEach((key) => {
            delete clone[key as string];
        });
        return { ...attrs, ...clone } as A & Omit<P, 'pt' | K>;
    });

    return { bindProps, mergedPt };
}

