<template>
    <Head title="New User" />
    <LayoutDefault>
        <PageHeader title="New User">
            <template #actions>
                <Button
                    outlined
                    size="small"
                    @click="router.visit($route('users.index'))"
                >
                    View All
                </Button>
            </template>
        </PageHeader>
        <PageMain>
            <div class="mx-auto max-w-2xl px-4 py-6 sm:px-6">
                <Card>
                    <template #title>
                        Add User
                    </template>
                    <!-- <template #subtitle></template> -->
                    <template #content>
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
                                    placeholder="Email"
                                />
                                <div
                                    v-if="form.errors.email"
                                    class="text-red-600 text-sm"
                                >
                                    {{ form.errors.email }}
                                </div>
                            </div>
                            <!-- <div class="flex items-center justify-between">
                                <Button size="small" @click="submitForm" :disabled="form.processing" :loading="form.processing" label="Add User" />
                            </div> -->
                        </form>
                    </template>
                    <template #footer>
                        <Button
                            size="small"
                            :disabled="form.processing"
                            :loading="form.processing"
                            label="Add User"
                            @click="submitForm"
                        />
                    </template>
                </Card>
            </div>
        </PageMain>
    </LayoutDefault>
</template>

<script setup>
import { useToast } from 'primevue/usetoast';
const form = useForm({
    name: null,
    email: null,
})

const toast = useToast();

const submitForm = () => {
    form.post(route('users.store'),  {
        onSuccess: r => {
            toast.add({ severity: 'success', summary: 'User Created', detail: 'The user has been created.', life: 3000 });
        },
        onError: err => {
            toast.add({ severity: 'error', summary: 'An Error Occured', detail: 'This request failed to process.', life: 5000 });
        }
    })
}
</script>
