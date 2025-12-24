import { computed } from 'vue';

/**
 * Creates a computed ref that syncs with v-model.
 * Simplifies the common pattern of creating a two-way binding for modelValue.
 *
 * @example
 * // Standard v-model
 * const isOpen = useModelValue({ props, emit });
 *
 * // Custom prop name (v-model:visible)
 * const isOpen = useModelValue({ props, emit, name: 'visible' });
 */
export function useModelValue<T, N extends string = 'modelValue'>({
    props,
    emit,
    name = 'modelValue' as N,
}: {
    props: Record<string, unknown>;
    emit: (event: `update:${N}`, value: T) => void;
    name?: N;
}) {
    return computed({
        get: () => props[name] as T,
        set: (value: T) => emit(`update:${name}` as `update:${N}`, value),
    });
}
