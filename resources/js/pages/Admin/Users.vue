<template>
    <Head title="Users" />
    <LayoutDefault>
        <PageHeader :title="`Users (${userTotal})`">
            <template #actions>
                <Button raised @click="router.visit($route('users.create'))">
                    Add
                </Button>
            </template>
        </PageHeader>
        <PageMain>
            <div class="shadow border border-surface-200 dark:border-surface-600 bg-white dark:bg-surface-700 rounded">
                <ul>
                    <li
                        v-for="user in users.data"
                        :key="user.id"
                        class="border-b last:border-none border-gray-100 dark:border-gray-700 p-4 hover:bg-surface-200/50 dark:hover:bg-surface-700/50 flex items-center justify-between rounded"
                    >
                        <div class="flex items-center space-x-4">
                            <div class="min-w-[300px]">
                                <h2 class="text-sm font-semibold">
                                    {{ user.name }}
                                </h2>
                                <p class="text-xs text-gray-700 dark:text-gray-200">
                                    {{ user.email }}
                                </p>
                            </div>
                            <div>
                                <h2 class="text-sm font-semibold">
                                    Updated
                                </h2>
                                <p class="text-xs text-gray-700 dark:text-gray-200">
                                    {{ formatToDatetime(user.updated_at) }}
                                </p>
                            </div>
                        </div>
                        <div>
                            <ButtonGroup>
                                <Button
                                    label="Edit"
                                    icon="pi pi-pencil"
                                    size="small"
                                    @click.prevent.stop="openEditModal(user)"
                                />
                                <Button
                                    icon="pi pi-trash"
                                    size="small"
                                    @click.prevent.stop="openDeleteModal(user)"
                                />
                            </ButtonGroup>
                        </div>
                    </li>
                </ul>
            </div>
        </PageMain>
    </LayoutDefault>
    <AtlasModalConfirmation
        v-model="userDeleteModal"
        title="Delete user"
        message="Are you sure you want to delete this user?"
        :loading="form.processing"
        @confirm="remove"
    />
    <Drawer
        v-model:visible="showModal"
        header="Edit User"
        position="right"
    >
        <div class="w-[450px] h-[2000px]">
            <div class="w-full p-6">
                <form>
                    <div class="mb-4">
                        <label for="name" class="block text-sm font-bold mb-2">Name:</label>
                        <InputText
                            v-model="form.name"
                            fluid
                            type="text"
                            placeholder="Name"
                        />
                        <div v-if="form.errors.name" class="text-red-600 text-sm">
                            {{ form.errors.name }}
                        </div>
                    </div>
                    <div class="mb-4">
                        <label
                            for="email"
                            class="block text-sm font-bold mb-2"
                        >Email Address:</label>
                        <InputText
                            v-model="form.email"
                            fluid
                            type="text"
                            placeholder="Email Address"
                        />
                        <div v-if="form.errors.email" class="text-red-600 text-sm">
                            {{ form.errors.email }}
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <template #footer>
            <div class="flex items-center gap-2 py-4 px-6 border-t border-surface-200 dark:border-surface-600">
                <Button
                    type="button"
                    label="Save"
                    :disabled="form.processing"
                    :loading="form.processing"
                    @click="save"
                />
                <Button
                    text
                    type="button"
                    label="Cancel"
                    severity="secondary"
                    @click="showModal = false;form.reset()"
                />
            </div>
        </template>
    </Drawer>
</template>

<script setup>
import { useModal } from '@atlas/composables';
import { formatToDatetime } from '@atlas/utils';

const props = defineProps({
    users: {
        type: Object,
        default: () => {}
    }
});

const { modalActiveState } = useModal();

const userDeleteModal = modalActiveState('userDeleteModal');
const userEditModal = modalActiveState('userEditModal');

const toast = useToast();

const userTotal = computed(() => {
    return props?.users?.total || 0;
});

const showModal = ref(false);

const form = useForm({
    id: null,
    name: null,
    email: null
});

const convertLocalTimezone = (time) => {
    // 'America/New_York' (pass current users timezone)
    return (time);
};

const openEditModal = (user) => {
    showModal.value = true;
    form.id = user.id;
    form.name = user.name;
    form.email = user.email;
    form.clearErrors();
};

const openDeleteModal = (user) => {
    userDeleteModal.value = true;
    form.id = user.id;
    form.name = user.name;
    form.email = user.email;
};

const remove = () => {
    if (form.id) {
        form.delete(route('users.destroy', [form.id]), {
            onSuccess: page => {
                userDeleteModal.value = false;
                toast.add({ severity: 'success', summary: 'User Deleted', detail: 'The user has been deleted.', life: 3000 });
            },
            onError: err => {
                toast.add({ severity: 'error', summary: 'An Error Occured', detail: 'This request failed to process.', life: 4000 });
            }
        });
    }
};

const save = () => {
    if (form.id) {
        form.put(route('users.update', [form.id]), {
            onSuccess: user => {
                showModal.value = false;
                toast.add({ severity: 'success', summary: 'User Saved', detail: 'The user has been saved.', life: 3000 });
            },
            onError: err => {
                toast.add({ severity: 'error', summary: 'An Error Occured', detail: 'This request failed to process.', group: 'bl', life: 5000 });
            }
        });
    }
};
</script>
