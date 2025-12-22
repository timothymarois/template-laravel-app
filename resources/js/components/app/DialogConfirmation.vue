<template>
    <AlertDialog v-model:open="isOpen">
        <AlertDialogContent>
            <AlertDialogHeader>
                <AlertDialogTitle>{{ title }}</AlertDialogTitle>
                <AlertDialogDescription>
                    {{ message }}
                </AlertDialogDescription>
            </AlertDialogHeader>
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
import { computed } from 'vue';
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

const handleConfirm = () => {
    emit('confirm');
};
</script>
