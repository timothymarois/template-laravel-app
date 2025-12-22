<template>
    <DialogConfirmation
        v-model="showModal"
        title="Archive user"
        message="Are you sure you want to archive this user? This action can be undone."
        confirmLabel="Archive"
        destructive
        :loading="form.processing"
        @confirm="submit"
    />
</template>

<script setup>
import DialogConfirmation from '@components/app/DialogConfirmation.vue';
import { useModal } from '@/composables';
import { useForm } from '@inertiajs/vue3';
import { useFormSubmit } from '@/composables/useFormSubmit';
const { submitForm } = useFormSubmit();
const { activeState, onOpen, onClose } = useModal();

const showModal = activeState('DELETE_USER');

const form = useForm({
    id: null
});

const submit = () => {
    submitForm(form, 'delete', route('users.destroy', [form.id]), {
        only: ['users'],
        toastMessage: 'User archived successfully',
        onSuccess: () => showModal.value = false
    });
};

onOpen('DELETE_USER', (data) => {
    if (data?.id) {
        form.id = data.id;
    }
});

onClose('DELETE_USER', (data) => {
    form.reset();
});
</script>
