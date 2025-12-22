import { reactive, computed, Ref } from 'vue';
import { isClient } from '../utils';

interface ScrollState {
    isTop: boolean;
    lockScroll: boolean;
}

const scrollStates = reactive(new Map<string, ScrollState>());

export function useScroll(id: string) {
    if (!scrollStates.has(id)) {
        scrollStates.set(id, {
            isTop: true,
            lockScroll: false,
        });
    }

    const state = scrollStates.get(id)!;

    const isTop = computed(() => state.isTop);

    const setTop = (value: boolean): void => {
        state.isTop = value;
    };

    const lockScroll = (): void => {
        if (!isClient) return;
        state.lockScroll = true;
        document.documentElement.style.overflowY = 'hidden';
        document.body.style.overflowY = 'hidden';
    };

    const unlockScroll = (): void => {
        if (!isClient) return;
        state.lockScroll = false;
        document.documentElement.style.overflowY = '';
        document.body.style.overflowY = '';
    };

    const bindScrollHandler = (elementRef: Ref<HTMLElement | null>) => {
        const handleScroll = () => {
            if (!isClient || !elementRef.value) return;
            setTop(elementRef.value.scrollTop <= 1);
        };

        const add = () => {
            if (!isClient || !elementRef.value) return;
            elementRef.value.addEventListener('scroll', handleScroll);
            handleScroll(); // initialize state
        };

        const remove = () => {
            if (!isClient || !elementRef.value) return;
            elementRef.value.removeEventListener('scroll', handleScroll);
        };

        return { add, remove };
    };

    return {
        isTop,
        setTop,
        bindScrollHandler,
        lockScroll,
        unlockScroll,
    };
}
