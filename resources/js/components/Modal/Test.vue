<template>
    <AtlasDrawer
        v-model="showModal"
        title="Edit test"
        position="right"
    >
        <Card>
            <template #header>
                <div class="font-semibold text-gray-900 dark:text-gray-200 text-md flex items-center space-x-2">
                    <div>Details</div>
                    <AtlasHelpInfo>
                        <div>This is an info bubble tooltip</div>
                    </AtlasHelpInfo>
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
        <Card>
            <template #header>
                <div class="font-semibold text-gray-900 dark:text-gray-200 text-md">Permissions <AtlasHelpTooltip text="User permissions are set on a per role basis. If a user has a role with a permission, they will have that permission." /></div>
            </template>
            <template #content>
                <form>
                    <div class="space-y-4 w-full">
                        <div class="grid grid-cols-2 items-center gap-6 w-full">
                            <AtlasFormField name="role" label="Role" :error="form.errors.name">
                                <Select v-model="form.role" showClear :options="roles" option-label="name" option-value="id" fluid />
                            </AtlasFormField>
                            <AtlasFormField name="roles" label="Roles" :error="form.errors.name">
                                <MultiSelect v-model="form.roles" showClear :options="roles" option-label="name" option-value="id" fluid filter />
                            </AtlasFormField>
                        </div>
                    </div>
                </form>
            </template>
        </Card>
        <Card>
            <template #header>
                <div class="font-semibold text-gray-900 dark:text-gray-200 text-md">Add user</div>
            </template>
            <template #content>
                <form>
                    <div class="space-y-4 w-full">
                        <Button
                            label="Add user"
                            outlined
                            @click="open('ADD_EDIT_USER')"
                        />
                    </div>
                </form>
            </template>
        </Card>
        <template #footer>
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
        </template>
    </AtlasDrawer>
</template>

<script setup>
import { useModal } from '@atlas/composables';

const { activeState, open, onOpen, onClose } = useModal();

const showModal = activeState('TEST');

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

onOpen('TEST', (data) => {

});

onClose('TEST', (data) => {

});
</script>
