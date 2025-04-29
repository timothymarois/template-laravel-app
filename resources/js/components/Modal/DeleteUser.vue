<template>
    <AtlasModalConfirmation
        v-model="showModal"
        title="Delete user"
        message="Are you sure you want to delete this user?"
        :loading="form.processing"
        @confirm="submit"
    />
</template>

<script setup>
import { useModal } from '@atlas/composables';

const toast = useToast();
const { activeState, onOpen, onClose } = useModal();

const showModal = activeState('DELETE_USER');

const form = useForm({
    id: null
});

const submit = () => {
    if (form.id) {
        form.delete(route('users.destroy', [form.id]), {
            onSuccess: r => {
                showModal.value = false;
                toast.add({ summary: 'User deleted', life: 5000 });
            }
        });
    }
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
