<template>
    <div class="relative">
        <div
            :class="[
                baseClass,
                heightClass,
                paddingClass,
                sizeClass,
                borderColorClass,
                isDisabled
                    ? 'bg-surface-200 text-surface-700 dark:bg-surface-700 dark:text-surface-400 opacity-70 shadow-none cursor-not-allowed'
                    : 'bg-surface-0 dark:bg-surface-950 text-surface-900 dark:text-surface-0 hover:border-surface-400 dark:hover:border-surface-600 cursor-pointer',
                rootClass,
            ]"
            :style="rootStyle"
            @click="onContainerClick"
        >
            <Button
                type="button"
                :label="chooseLabel"
                @click.stop="choose"
                :disabled="isDisabled"
                size="small"
                class="mr-2 flex-shrink-0"
            />
            <div class="relative flex-1 min-w-0">
                <div :class="[textBase, clearable && hasFile ? 'pr-8' : '', isDisabled ? 'text-surface-700 dark:text-surface-400 bg-surface-200' : hasFile ? 'text-surface-900 dark:text-surface-0' : 'text-surface-500 dark:text-surface-400']">
                    <span class="block truncate">{{ fileNames || 'No file selected' }}</span>
                </div>
                <button
                    v-if="clearable && hasFile"
                    type="button"
                    @click.stop="clear"
                    :disabled="isDisabled"
                    class="absolute top-1/2 -translate-y-1/2 right-3 text-surface-400 hover:text-surface-600 dark:text-surface-300 dark:hover:text-white cursor-pointer disabled:hidden disabled:pointer-events-none"
                >
                    <TimesIcon class="w-4 h-4" />
                </button>
            </div>
        </div>
        <input
            ref="input"
            type="file"
            class="hidden"
            :multiple="multiple"
            @change="onChange"
            v-bind="inputAttrs"
        />
    </div>
</template>

<script setup lang="ts">
import { ref, computed, useAttrs } from 'vue';
import Button from './Button.vue';
import TimesIcon from '@primevue/icons/times';

interface Props {
    modelValue?: File | File[] | null;
    multiple?: boolean;
    clearable?: boolean;
    chooseLabel?: string;
    size?: 'small' | 'large';
    invalid?: boolean;
}
const props = withDefaults(defineProps<Props>(), {
    chooseLabel: 'Choose',
    size: undefined,
    invalid: false,
});
const emit = defineEmits(['update:modelValue']);

const attrs = useAttrs();

const rootClass = computed(() => (attrs as any).class);
const rootStyle = computed(() => (attrs as any).style);

const sizeClass = computed(() => (props.size ? `p-${props.size}` : ''));
const borderColorClass = computed(() =>
    props.invalid ? 'border-red-400 dark:border-red-500' : 'border-surface-300 dark:border-surface-700'
);
const heightClass = 'h-10 p-small:h-[34px] p-large:h-[42px]';
const paddingClass =
    'pl-[3px] pr-3 p-small:pl-0 p-small:pr-[0.625rem] p-large:pl-1 p-large:pr-[0.875rem]';
const baseClass =
    'flex items-center border focus-within:border-primary rounded-[var(--p-content-border-radius)] transition-colors duration-200 overflow-hidden shadow-[0_1px_2px_0_rgba(18,18,23,0.05)]';
const inputAttrs = computed(() => {
    const { class: _c, style: _s, ...rest } = attrs as any;
    return rest;
});

const isDisabled = computed(() => !!inputAttrs.value.disabled);

const input = ref<HTMLInputElement>();
const choose = () => input.value?.click();
const onContainerClick = () => {
    if (!isDisabled.value) choose();
};

const onChange = (e: Event) => {
    const files = (e.target as HTMLInputElement).files;
    if (!files) return;
    emit('update:modelValue', props.multiple ? Array.from(files) : files[0] ?? null);
};

const clear = () => {
    emit('update:modelValue', props.multiple ? [] : null);
    if (input.value) input.value.value = '';
};

const model = computed(() => props.modelValue);
const fileNames = computed(() => {
    const value = model.value;
    if (!value || (Array.isArray(value) && value.length === 0)) return '';
    if (Array.isArray(value)) {
        return value.length === 1 ? value[0].name : `${value.length} files selected`;
    }
    return value.name;
});
const hasFile = computed(() => !!fileNames.value);

const textBase = `flex items-center w-full h-full
    bg-surface-0 dark:bg-surface-950
    leading-[1.25rem] text-sm
    p-small:text-xs
    p-large:text-base
    transition-colors duration-200`;
</script>

