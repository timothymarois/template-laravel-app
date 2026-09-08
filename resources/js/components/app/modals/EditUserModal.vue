<template>
    <SheetForm
        v-model="showModal"
        :title="form.id ? 'Edit user' : 'Add user'"
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
                        <div class="w-full">
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
                        </div>
                    </div>
                </form>
            </template>
        </Card>
    </SheetForm>
</template>

<script setup>
import { SheetForm, Card, TooltipIcon, LabelField, Input as InputText, Select } from '@/components/ui';
import { computed } from 'vue';
import { useModal } from '@/composables';
import { useForm, usePage } from '@inertiajs/vue3';
import { useFormSubmit } from '@/composables/useFormSubmit';
const { activeState, onOpen, onClose } = useModal();
const { submitForm } = useFormSubmit();

const showModal = activeState('ADD_EDIT_USER');

const page = usePage();

// Read from the page rather than taken as a prop: this modal is mounted by
// AppLayout, so a prop would have to be threaded through every page that uses
// the layout. Falls back to the roles the template ships when a page does not
// supply them.
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

onClose('ADD_EDIT_USER', (data) => {
    form.clearErrors();
    form.reset();
});
</script>
