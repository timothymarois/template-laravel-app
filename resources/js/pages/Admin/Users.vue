<template>
    <Head title="Users" />
    <LayoutDefault>
        <PageHeader :title="`Users (${userTotal})`">
            <template #actions>
                <Button label="Add" @click="router.visit($route('users.create'))" />
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
    <AtlasDrawer
        v-model="showModal"
        title="Edit user"
        position="right"
    >
        <Card>
            <template #header>
                <div class="font-semibold text-gray-900 dark:text-gray-200 text-md">Details</div>
            </template>
            <template #content>
                <form>
                    <div class="space-y-4 w-full">
                        <div class="grid grid-cols-2 items-center gap-6 w-full">
                            <AtlasFormSlot name="name" label="Name" required :error="form.errors.name">
                                <InputText id="name" v-model="form.name" type="text" fluid :invalid="!!form.errors.name" />
                            </AtlasFormSlot>
                            <AtlasFormSlot name="email" label="Email" required :error="form.errors.email">
                                <InputText id="email" v-model="form.email" type="text" fluid :invalid="!!form.errors.email" />
                            </AtlasFormSlot>
                        </div>
                    </div>
                </form>
            </template>
        </Card>
        <Card>
            <template #header>
                <div class="font-semibold text-gray-900 dark:text-gray-200 text-md">Permissions <AtlasHelpTooltip text="User permissions are set on a per role basis. If a user has a role with a permission, they will have that permission." /></div>
            </template>
            <template #content>
                <form>
                    <div class="space-y-4 w-full">
                        <div class="grid grid-cols-2 items-center gap-6 w-full">
                            <AtlasFormSlot name="role" label="Role" :error="form.errors.name">
                                <Select v-model="form.role" showClear :options="roles" option-label="name" option-value="id" fluid />
                            </AtlasFormSlot>
                            <AtlasFormSlot name="roles" label="Roles" :error="form.errors.name">
                                <MultiSelect v-model="form.roles" showClear :options="roles" option-label="name" option-value="id" fluid filter />
                            </AtlasFormSlot>
                        </div>
                    </div>
                </form>
            </template>
        </Card>
        <template #footer>
            <Button
                label="Save"
                :disabled="form.processing"
                :loading="form.processing"
                @click="save"
            />
            <Button
                text
                type="button"
                label="Cancel"
                @click="showModal = false;form.reset()"
            />
        </template>
    </AtlasDrawer>
</template>

<script setup>
import { useModal } from '@atlas/composables';
import { formatToDatetime } from '@atlas/utils/format';

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
    email: null,
    role: 'user',
    roles: [],
});

const roles = ref([
    { id: 'admin', name: 'Admin' },
    { id: 'user', name: 'User' },
    { id: 'guest', name: 'Guest' },
    { id: 'disabled', name: 'Disabled' },
    { id: 'banned', name: 'Banned' },
    { id: 'pending', name: 'Pending' },
    { id: 'suspended', name: 'Suspended' },,
    { id: 'deleted', name: 'Deleted' },
    { id: 'blacklisted', name: 'Blacklisted' },
    { id: 'archived', name: 'Archived' },
    { id: 'deleted', name: 'Deleted' },
]);

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
                toast.add({ summary: 'User deleted', life: 5000 });
            }
        });
    }
};

const save = () => {
    if (form.id) {
        form.put(route('users.update', [form.id]), {
            onSuccess: user => {
                showModal.value = false;
                toast.add({ summary: 'User updated', life: 5000 });
            }
        });
    }
};
</script>
