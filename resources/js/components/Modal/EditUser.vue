<template>
    <AtlasDrawer
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
                    <AtlasHelpTooltip text="Edit the user details such as name and email." />
                </div>
            </template>
            <template #content>
                <form>
                    <div class="space-y-4 w-full">
                        <div class="w-full">
                            <AtlasFormField name="name" label="Name" required :error="form.errors.name">
                                <InputText id="name" v-model="form.name" type="text" fluid :invalid="!!form.errors.name" />
                            </AtlasFormField>
                        </div>
                        <div class="w-full">
                            <AtlasFormField name="email" label="Email" required :error="form.errors.email">
                                <InputText id="email" v-model="form.email" type="text" fluid :invalid="!!form.errors.email" />
                            </AtlasFormField>
                        </div>
                    </div>
                </form>
            </template>
        </Card>
    </AtlasDrawer>
</template>

<script setup>
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
    const routePath = form.id ? route('users.update', form.id) : route('users.store');
    submitForm(form, method, routePath, {
        only: ['users'],
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
