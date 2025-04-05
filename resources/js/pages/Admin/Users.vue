<template>
    <Head title="Users" />
    <LayoutDefault>
        <PageHeader :title="`Users (${userTotal})`">
            <template #actions>
                <Button
                    raised
                    @click="router.visit($route('users.create'))"
                >
                    Add
                </Button>
            </template>
        </PageHeader>
        <PageMain>
            <Card>
                <template #content>
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
                                        {{ convertLocalTimezone(user.updated_at) }}
                                    </p>
                                </div>
                            </div>
                            <div>
                                <!-- <InputGroup>
                                    <Button @click.prevent.stop="openEditModal(user)" outlined label="Edit" icon="pi pi-pencil" size="small" />
                                    <Button @click.prevent.stop="remove(user)" outlined  icon="pi pi-trash" size="small" :loading="deleteLoading" />
                                </InputGroup> -->

                                <ButtonGroup>
                                    <Button
                                        outlined
                                        label="Edit"
                                        icon="pi pi-pencil"
                                        size="small"
                                        @click.prevent.stop="openEditModal(user)"
                                    />
                                    <Button
                                        outlined
                                        icon="pi pi-trash"
                                        size="small"
                                        @click.prevent.stop="openDeleteModal(user)"
                                    />
                                </ButtonGroup>
                            </div>
                        </li>
                    </ul>
                </template>
            </Card>
        </PageMain>
    </LayoutDefault>
    <Dialog
        v-model:visible="showDelete"
        modal
        header="Delete user"
        :style="{ width: '25rem' }"
    >
        <div class="relative text-center">
            <svg
                class="text-gray-400 dark:text-gray-500 w-11 h-11 mb-6 mx-auto"
                aria-hidden="true"
                fill="currentColor"
                viewBox="0 0 20 20"
                xmlns="http://www.w3.org/2000/svg"
            ><path
                fill-rule="evenodd"
                d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z"
                clip-rule="evenodd"
            /></svg>
            <p class="mb-4 text-gray-500 dark:text-gray-300">
                Are you sure you want to delete this item?
            </p>
            <div class="flex justify-center items-center space-x-4 py-2">
                <Button
                    type="button"
                    label="Yes, delete"
                    severity="danger"
                    :disabled="form.processing"
                    :loading="form.processing"
                    @click="remove"
                />
                <Button
                    text
                    type="button"
                    label="Cancel"
                    severity="secondary"
                    @click="showDelete = false;form.reset()"
                />
            </div>
        </div>
    </Dialog>
    <!-- :pt="{'header': '!shadow border-surface-300'}" :ptOptions="{ mergeProps: true }" -->
    <Drawer
        v-model:visible="showModal"
        header="Edit User"
        position="right"
    >
        <div class="w-[450px] h-[2000px]">
            <div class="w-full p-6">
                <form>
                    <div class="mb-4">
                        <label
                            for="name"
                            class="block text-sm font-bold mb-2"
                        >Name:</label>
                        <InputText
                            v-model="form.name"
                            fluid
                            type="text"
                            placeholder="Name"
                        />
                        <div
                            v-if="form.errors.name"
                            class="text-red-600 text-sm"
                        >
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
                        <div
                            v-if="form.errors.email"
                            class="text-red-600 text-sm"
                        >
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
import { useToast } from 'primevue/usetoast';
const toast = useToast();
const props = defineProps({
    users: {
        type: Object,
        default: () => {}
    }
});

const userTotal = computed(() => {
    return props?.users?.total || 0
})

const showModal = ref(false)
const showDelete = ref(false)
const deleteLoading = ref(false)

const form = useForm({
    id: null,
    name: null,
    email: null
})

const convertLocalTimezone = (time) => {
    // 'America/New_York' (pass current users timezone)
    return (time);
}

const openEditModal = (user) => {
    showModal.value = true
    form.id = user.id
    form.name = user.name
    form.email = user.email
    form.clearErrors()
};

const openDeleteModal = (user) => {
    showDelete.value = true
    form.id = user.id
    form.name = user.name
    form.email = user.email
};

const remove = () => {
    if (form.id) {
        form.delete(route('users.destroy', [form.id]), {
            onSuccess: page => {
                deleteLoading.value = false
                showDelete.value = false
                toast.add({ severity: 'success', summary: 'User Deleted', detail: 'The user has been deleted.', life: 3000 });
            },
            onError: err => {
                toast.add({ severity: 'error', summary: 'An Error Occured', detail: 'This request failed to process.', life: 4000 });
            }
        })
    }
}

const save = () => {
    if (form.id) {
        form.put(route('users.update', [form.id]), {
            onSuccess: user => {
                showModal.value = false
                toast.add({ severity: 'success', summary: 'User Saved', detail: 'The user has been saved.', life: 3000 });
            },
            onError: err => {
                toast.add({ severity: 'error', summary: 'An Error Occured', detail: 'This request failed to process.', group: 'bl', life: 5000 });
            }
        })
    }
};
</script>
