import { reactive, computed, ComputedRef, type Ref } from 'vue';

type ModalData = unknown;

interface ModalState {
    open: boolean;
    data: ModalData;
}

type ModalCallback = (data: ModalData) => void;

const modals = reactive(new Map<string, ModalState>());
const openListeners = new Map<string, ModalCallback[]>();
const closeListeners = new Map<string, ModalCallback[]>();

export function useModal() {
    const open = (name: string, data: ModalData = null): void => {
        modals.set(name, { open: true, data });

        openListeners.get(name)?.forEach(callback => callback(data));
    };

    const close = (name: string): void => {
        if (!modals.has(name)) return;

        const previous = modals.get(name);
        modals.set(name, { ...previous!, open: false });

        closeListeners.get(name)?.forEach(callback => callback(previous?.data ?? null));
    };

    const closeAll = (): void => {
        for (const [name, modal] of modals.entries()) {
            if (modal.open) close(name);
        }
    };

    const activeState = (name: string): Ref<boolean> => computed({
        get: () => modals.get(name)?.open === true,
        set: (value: boolean) => {
            value ? open(name) : close(name);
        }
    });

    const data = (name: string): ComputedRef<ModalData> => computed(() => modals.get(name)?.data ?? null);

    const isOpen = computed(() => {
        for (const modal of modals.values()) {
            if (modal.open) return true;
        }
        return false;
    });

    const onOpen = (name: string, callback: ModalCallback): void => {
        if (!openListeners.has(name)) {
            openListeners.set(name, []);
        }
        openListeners.get(name)!.push(callback);
    };

    const onClose = (name: string, callback: ModalCallback): void => {
        if (!closeListeners.has(name)) {
            closeListeners.set(name, []);
        }
        closeListeners.get(name)!.push(callback);
    };

    return {
        open,
        close,
        closeAll,
        activeState,
        data,
        isOpen,
        onOpen,
        onClose,
    };
}
