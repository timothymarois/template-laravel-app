<template>
    <Dialog v-model:visible="showModal" :header="form.id ? 'Edit user' : 'Add user'">
        <form class="space-y-4 w-full" @submit.prevent="submit">
            <LabelField name="name" label="Name" required :error="form.errors.name">
                <InputText id="name" v-model="form.name" type="text" fluid :invalid="!!form.errors.name" />
            </LabelField>
            <LabelField name="email" label="Email" required :error="form.errors.email">
                <InputText id="email" v-model="form.email" type="text" fluid :invalid="!!form.errors.email" />
            </LabelField>
            <LabelField
                name="role"
                label="Role"
                required
                :error="form.errors.role"
                help="An administrator can reach /admin and manage every user."
            >
                <Select
                    v-model="form.role"
                    :options="roleOptions"
                    option-label="label"
                    option-value="value"
                    fluid
                    :invalid="!!form.errors.role"
                />
            </LabelField>

            <!-- Under the fields, not in the footer: the footer is a horizontal
                 row, so an error block there is squeezed beside the action. -->
            <FormErrors :errors="form.errors" :expandDefault="true" />

            <button type="submit" class="hidden" tabindex="-1" aria-hidden="true"></button>
        </form>
        <template #footer>
            <Button variant="outline" label="Cancel" class="cursor-pointer" @click="showModal = false" />
            <Button
                :label="form.id ? 'Save changes' : 'Create user'"
                class="cursor-pointer"
                :disabled="form.processing"
                :loading="form.processing"
                @click="submit"
            />
        </template>
    </Dialog>
</template>

<script setup>
import { computed } from 'vue';
import { useForm, usePage } from '@inertiajs/vue3';
import { Dialog, Button, FormErrors, LabelField, Input as InputText, Select } from '@/components/ui';
import { useModal } from '@/composables';
import { useFormSubmit } from '@/composables/useFormSubmit';

const { activeState, onOpen, onClose } = useModal();
const { submitForm } = useFormSubmit();
const page = usePage();

const showModal = activeState('ADD_EDIT_USER');

// Read from the page rather than taken as a prop: this modal is mounted by
// AppLayout, so a prop would have to be threaded through every page using the
// layout. Falls back to the roles the template ships when a page supplies none.
const roleOptions = computed(() => page.props.roles ?? [
    { value: 'admin', label: 'Administrator' },
    { value: 'user', label: 'User' },
]);

const form = useForm({
    id: null,
    name: null,
    email: null,
    role: 'user',
});

const submit = () => {
    const method = form.id ? 'put' : 'post';
    const routePath = form.id ? route('admin.users.update', form.id) : route('admin.users.store');

    submitForm(form, method, routePath, {
        only: ['users', 'user', 'item'],
        toastMessage: form.id ? 'User updated successfully' : 'User created successfully',
        onSuccess: () => showModal.value = false,
    });
};

onOpen('ADD_EDIT_USER', (data) => {
    if (data?.id) {
        form.id = data.id;
        form.name = data.name;
        form.email = data.email;
        form.role = data.role ?? 'user';
    }
});

onClose('ADD_EDIT_USER', () => {
    form.clearErrors();
    form.reset();
});
</script>
