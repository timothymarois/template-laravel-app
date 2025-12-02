<template>
    <Dialog
        v-bind="dialogAttrs"
        modal
        closable
        :visible="props.modelValue"
        :header="props.title"
        :dissmissible="false"
        :dismissable-mask="false"
        @update:visible="close"
    >
        <div :class="mergedPt.root.class">
            <slot name="message">
                <div :class="mergedPt.iconContainer.class">
                    <i :class="[icon, mergedPt.icon.class]" />
                </div>
                <div :class="mergedPt.message.class">
                    {{ message }}
                </div>
            </slot>
            <div :class="mergedPt.actions.class">
                <slot name="actions">
                    <Button
                        label="Confirm"
                        raised
                        pt:root:class="text-white bg-red-600 enabled:hover:bg-red-700 border-red-600 enabled:active:border-red-400 enabled:active:bg-red-600 enabled:hover:border-red-emphasis"
                        :disabled="loading"
                        :loading="loading"
                        @click="confirm"
                    />
                    <Button
                        text
                        label="Cancel"
                        @click="close"
                    />
                </slot>
            </div>
        </div>
    </Dialog>
</template>

<script setup lang="ts">
import Dialog from './Dialog.vue';
import Button from './Button.vue';
import { useAttrs, computed } from 'vue';
import type { DialogProps, DialogPassThroughOptions } from 'primevue/dialog';
import type { ButtonPassThroughOptions } from 'primevue/button';
import { usePrimeBindings } from '../composables';

interface DialogConfirmationPassThroughOptions {
    dialog?: DialogPassThroughOptions;
    root?: any;
    iconContainer?: any;
    icon?: any;
    message?: any;
    actions?: any;
    confirmButton?: ButtonPassThroughOptions;
    cancelButton?: ButtonPassThroughOptions;
}

interface Props extends /* @vue-ignore */ Omit<DialogProps, 'visible' | 'header' | 'pt'> {
    modelValue: boolean;
    title?: string;
    icon?: string;
    message?: string;
    loading?: boolean;
    pt?: DialogConfirmationPassThroughOptions;
}

const props = withDefaults(defineProps<Props>(), {
    title: 'Delete Item',
    icon: 'pi pi-trash',
    message: 'Are you sure you want to delete this item?',
    loading: false,
});
const emit = defineEmits(['update:modelValue', 'confirm']);
const attrs = useAttrs();

const theme = computed<DialogConfirmationPassThroughOptions>(() => ({
    dialog: {},
    root: 'relative text-center flex flex-col space-y-6',
    iconContainer: 'text-gray-400 dark:text-gray-500 mx-auto',
    icon: 'text-4xl',
    message: 'text-gray-700 dark:text-gray-200',
    actions: 'flex justify-center items-center space-x-4 py-2',
    confirmButton: {
        root: 'text-white bg-red-600 enabled:hover:bg-red-700 border-red-600 enabled:active:border-red-400 enabled:active:bg-red-600 enabled:hover:border-red-emphasis',
    },
    cancelButton: {},
}));

const { bindProps: dialogAttrs, mergedPt } = usePrimeBindings(
    props,
    attrs,
    theme,
    ['modelValue', 'title', 'icon', 'message', 'loading'] as const
);
const close = () => emit('update:modelValue', false);
const confirm = () => emit('confirm');
</script>

