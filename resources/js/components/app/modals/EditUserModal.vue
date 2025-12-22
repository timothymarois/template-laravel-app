<template>
    <DrawerForm
        v-model="showModal"
        :title="form.id ? 'Edit user' : 'Add user'"
        :tabs="[{title: 'Details'}, {title: 'Line items'}, {title: 'Roles', disabled: true}]"
        position="right"
        width="600px"
        :loading="form.processing"
        :errors="form.errors"
        @submit="submit"
    >
        <Card>
            <template #header>
                <div class="font-semibold text-gray-900 dark:text-gray-200 text-md flex items-center space-x-2">
                    <div>Details</div>
                    <TooltipIcon text="Edit the user details such as name and email." />
                </div>
            </template>
            <template #content>
                <form>
                    <div class="space-y-4 w-full">
                        <div class="w-full">
                            <LabelField name="name" label="Name" required :error="form.errors.name">
                                <InputText id="name" v-model="form.name" type="text" fluid :invalid="!!form.errors.name" />
                            </LabelField>
                        </div>
                        <div class="w-full">
                            <LabelField name="email" label="Email" required :error="form.errors.email">
                                <InputText id="email" v-model="form.email" type="text" fluid :invalid="!!form.errors.email" />
                            </LabelField>
                        </div>
                    </div>
                </form>
            </template>
        </Card>
        <template #tab-1>
            <Card>
                <template #content>
                    <div>This is line items</div>
                </template>
            </Card>
        </template>
    </DrawerForm>
</template>

<script setup>
import { DrawerForm, Card, TooltipIcon, LabelField, Input as InputText } from '@/components/ui';
import { useModal } from '@/composables';
import { useForm } from '@inertiajs/vue3';
import { useFormSubmit } from '@/composables/useFormSubmit';
const { activeState, onOpen, onClose } = useModal();
const { submitForm } = useFormSubmit();

const showModal = activeState('ADD_EDIT_USER');

const form = useForm({
    id: null,
    name: null,
    email: null,
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
    }
});

onClose('ADD_EDIT_USER', (data) => {
    form.clearErrors();
    form.reset();
});
</script>
