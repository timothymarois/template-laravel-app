<template>
    <DialogConfirmation
        v-model="showModal"
        title="Delete user"
        message="Are you sure you want to delete this user?"
        :loading="form.processing"
        @confirm="submit"
    />
</template>

<script setup>
import DialogConfirmation from '@atlas/ui/components/DialogConfirmation.vue';
const { submitForm } = useFormSubmit();
const { activeState, onOpen, onClose } = useModal();

const showModal = activeState('DELETE_USER');

const form = useForm({
    id: null
});

const submit = () => {
    submitForm(form, 'delete', route('users.destroy', [form.id]), {
        only: ['users'],
        toastMessage: 'User deleted successfully',
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
