<template>
    <Head title="New User" />
    <LayoutDefault>
        <PageHeader title="New User">
            <template #actions>
                <Button outlined label="Back" @click="router.visit($route('users.index'))" />
            </template>
        </PageHeader>
        <PageMain>
            <div class="mx-auto max-w-2xl px-4 py-6 sm:px-6">
                <Card>
                    <template #title>
                        Add User
                    </template>
                    <template #content>
                        <form class="space-y-4 w-full">
                            <AtlasFormSlot name="name" label="Name" required :error="form.errors.name">
                                <InputText v-model="form.name" type="text" fluid :invalid="!!form.errors.name" />
                            </AtlasFormSlot>
                            <AtlasFormSlot name="email" label="Email" required :error="form.errors.email">
                                <InputText v-model="form.email" type="text" fluid :invalid="!!form.errors.email" />
                            </AtlasFormSlot>
                        </form>
                    </template>
                    <template #footer>
                        <Button
                            :disabled="form.processing"
                            :loading="form.processing"
                            label="Add user"
                            @click="submitForm"
                        />
                    </template>
                </Card>
            </div>
        </PageMain>
    </LayoutDefault>
</template>

<script setup>
const form = useForm({
    name: null,
    email: null,
});

const toast = useToast();

const submitForm = () => {
    form.post(route('users.store'),  {
        onSuccess: r => {
            toast.add({ severity: 'success', summary: 'User created', life: 5000 });
        }
    });
};
</script>
