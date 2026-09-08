import { reactive, computed, onUnmounted, getCurrentInstance, type WritableComputedRef, type ComputedRef } from 'vue';

type ModalData = unknown;

interface ModalState {
    open: boolean;
    data: ModalData;
}

type ModalCallback = (data: ModalData) => void;

const modals = reactive(new Map<string, ModalState>());
const openListeners = new Map<string, ModalCallback[]>();
const closeListeners = new Map<string, ModalCallback[]>();

// Schedule removal of `callback` from `map[name]` when the calling component
// unmounts. Safe to call outside a setup context — the `getCurrentInstance`
// check makes it a no-op there.
function registerCleanup(map: Map<string, ModalCallback[]>, name: string, callback: ModalCallback): void {
    if (!getCurrentInstance()) return;

    onUnmounted(() => {
        const list = map.get(name);
        if (!list) return;

        const idx = list.indexOf(callback);
        if (idx !== -1) list.splice(idx, 1);
        if (list.length === 0) map.delete(name);
    });
}

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

    const activeState = (name: string): WritableComputedRef<boolean> => computed({
        get: () => modals.get(name)?.open === true,
        set: (value: boolean) => {
            if (!value) {
                close(name);

                return;
            }

            // Writing `true` while already open is deliberately a no-op. An overlay syncs
            // its own open state back through v-model as it appears; without this guard that
            // write reaches open(name) with no data, re-firing every onOpen listener with
            // null and wiping whatever the caller passed a moment earlier.
            if (modals.get(name)?.open === true) {
                return;
            }

            open(name, modals.get(name)?.data ?? null);
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
        registerCleanup(openListeners, name, callback);
    };

    const onClose = (name: string, callback: ModalCallback): void => {
        if (!closeListeners.has(name)) {
            closeListeners.set(name, []);
        }
        closeListeners.get(name)!.push(callback);
        registerCleanup(closeListeners, name, callback);
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
