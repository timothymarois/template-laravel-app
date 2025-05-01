<template>
    <AtlasDrawer
        v-model="showModal"
        :title="form.id ? 'Edit user' : 'Add user'"
        position="right"
        width="600px"
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
        <template #footer>
            <div class="w-full flex flex-col space-y-4">
                <AtlasErrors :errors="form.errors" />
                <!-- <div class="w-full py-4 bg-red-100 rounded-md px-4">
                    <span class="font-semibold">Internal server error</span> - Something went wrong
                </div> -->
                <div class="flex items-center space-x-4">
                    <Button
                        label="Save"
                        :disabled="form.processing"
                        :loading="form.processing"
                        @click="submit"
                    />
                    <Button
                        text
                        type="button"
                        label="Cancel"
                        @click="showModal = false"
                    />
                </div>
            </div>
        </template>
    </AtlasDrawer>
</template>

<script setup>
import { useModal } from '@atlas/composables';

const toast = useToast();
const { activeState, onOpen, onClose } = useModal();

const showModal = activeState('ADD_EDIT_USER');

const form = useForm({
    id: null,
    name: null,
    email: null,
});

const submit = () => {
    if (form.id) {
        form.put(route('users.update', [form.id]), {
            preserveScroll: true,
            onSuccess: r => {
                showModal.value = false;
                toast.add({ summary: 'User updated', life: 5000 });
            }
        });
    }
    else {
        form.post(route('users.store'),  {
            preserveScroll: true,
            onSuccess: r => {
                showModal.value = false;
                toast.add({ severity: 'success', summary: 'User created', life: 5000 });
            }
        });
    }
};

onOpen('ADD_EDIT_USER', (data) => {
    if (data?.id) {
        form.id = data.id;
        form.name = data.name;
        form.email = data.email;
    }
});

onClose('ADD_EDIT_USER', (data) => {
    // console.log('modal closed', data);
    form.clearErrors();
    form.reset();
});
</script>
