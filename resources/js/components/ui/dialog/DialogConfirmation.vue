<template>
    <AlertDialog v-model:open="isOpen">
        <AlertDialogContent>
            <AlertDialogHeader>
                <AlertDialogTitle>{{ title }}</AlertDialogTitle>
                <AlertDialogDescription>
                    {{ message }}
                </AlertDialogDescription>
            </AlertDialogHeader>
            <slot />
            <AlertDialogFooter>
                <AlertDialogCancel>Cancel</AlertDialogCancel>
                <AlertDialogAction
                    @click="handleConfirm"
                    :disabled="loading"
                    :variant="destructive ? 'destructive' : 'default'"
                >
                    <Loader2 v-if="loading" class="mr-2 h-4 w-4 animate-spin" />
                    {{ confirmLabel }}
                </AlertDialogAction>
            </AlertDialogFooter>
        </AlertDialogContent>
    </AlertDialog>
</template>

<script setup lang="ts">
import { computed, onBeforeUnmount, watch } from 'vue';
import {
    AlertDialog,
    AlertDialogAction,
    AlertDialogCancel,
    AlertDialogContent,
    AlertDialogDescription,
    AlertDialogFooter,
    AlertDialogHeader,
    AlertDialogTitle,
} from '@/components/ui/alert-dialog';
import { Loader2 } from 'lucide-vue-next';
import { shouldConfirmOnEnter } from './dialogUtils';

interface Props {
    modelValue?: boolean;
    title?: string;
    message?: string;
    loading?: boolean;
    destructive?: boolean;
    confirmLabel?: string;
}

const props = withDefaults(defineProps<Props>(), {
    modelValue: false,
    title: 'Confirm',
    message: 'Are you sure?',
    loading: false,
    destructive: false,
    confirmLabel: 'Confirm',
});

const emit = defineEmits<{
    'update:modelValue': [value: boolean];
    'confirm': [];
}>();

const isOpen = computed({
    get: () => props.modelValue,
    set: (val) => emit('update:modelValue', val),
});

/**
 * Closes as well as emitting. Clicking AlertDialogAction closes the dialog by
 * itself, so a keyboard confirm that only emitted left the dialog open over a
 * completed action — the same gesture with two different outcomes.
 */
const handleConfirm = () => {
    emit('confirm');
    isOpen.value = false;
};

/**
 * Enter confirms; Escape closes (reka's AlertDialog already handles Escape).
 *
 * Bound to the document while open rather than to the template. reka renders the
 * content through a portal whose wrapper root is AlertDialogPortal — a component
 * that emits no DOM node — so a listener placed in this template never received
 * the event, and Enter activated the focused Cancel button instead.
 */
const onKeydown = (event: KeyboardEvent) => {
    if (event.key !== 'Enter') return;
    if (! shouldConfirmOnEnter(event.target, props.loading)) return;

    // Stops the focused control activating as well: reka focuses Cancel by
    // default on a destructive dialog, so Enter would otherwise cancel.
    event.preventDefault();
    event.stopPropagation();
    handleConfirm();
};

const stopListening = () => document.removeEventListener('keydown', onKeydown, true);

watch(() => props.modelValue, (open) => {
    stopListening();

    // Capture phase, so this runs before reka's own key handling.
    if (open) document.addEventListener('keydown', onKeydown, true);
}, { immediate: true });

onBeforeUnmount(stopListening);
</script>
