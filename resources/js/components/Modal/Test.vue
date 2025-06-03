<template>
    <DrawerForm
        v-model="showModal"
        title="Edit test"
        position="right"
    >
        <Alert>
            <div>This modal does not do anything, Its only here for testing. It is a long established fact that a reader will be distracted by the readable content of a page when looking at its layout. The point of using Lorem Ipsum is that it has a more-or-less normal distribution of letters, as opposed to using 'Content here, content here', making it look like readable English. Many desktop publishing packages and web page editors now use Lorem Ipsum as their default model text, and a search for 'lorem ipsum' will uncover many web sites still in their infancy.</div>
        </Alert>
        <Alert hideIcon>
            <div><span class="font-semibold">No icon</span>. It is a long established fact that a reader will be distracted by the readable content of a page when looking at its layout. The point of using Lorem Ipsum is that it has a more-or-less normal distribution of letters, as opposed to using 'Content here, content here', making it look like readable English. Many desktop publishing packages and web page editors now use Lorem Ipsum as their default model text, and a search for 'lorem ipsum' will uncover many web sites still in their infancy.</div>
        </Alert>
        <Card>
            <template #header>
                <div class="font-semibold text-gray-900 dark:text-gray-200 text-md flex items-center space-x-2">
                    <div>Details</div>
                    <TooltipInfo>
                        <div>This is an info bubble tooltip</div>
                    </TooltipInfo>
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
        <Card>
            <template #header>
                <div class="font-semibold text-gray-900 dark:text-gray-200 text-md">Permissions <TooltipIcon text="User permissions are set on a per role basis. If a user has a role with a permission, they will have that permission." /></div>
            </template>
            <template #content>
                <form>
                    <div class="space-y-4 w-full">
                        <div class="grid grid-cols-2 items-center gap-6 w-full">
                            <LabelField name="role" label="Role" :error="form.errors.name">
                                <Select v-model="form.role" showClear :options="roles" option-label="name" option-value="id" fluid />
                            </LabelField>
                            <LabelField name="roles" label="Roles" :error="form.errors.name">
                                <MultiSelect v-model="form.roles" showClear :options="roles" option-label="name" option-value="id" fluid filter />
                            </LabelField>
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
            <div class="w-full flex flex-col space-y-4">
                <Errors :errors="form.errors" :failed="true" />
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
    </DrawerForm>
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
    { id: 'suspended', name: 'Suspended' },
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
