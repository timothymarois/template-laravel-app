<template>
    <Head title="New User" />
    <PageHeader title="New User">
        <template #actions>
            <Button outlined @click="router.visit($route('users.index'))" size="small">View All</Button>
        </template>
    </PageHeader>
    <PageMain>
        <div class="mx-auto max-w-2xl px-4 py-6 sm:px-6">
            <form  class="shadow-md rounded-lg p-6 border border-surface-200 dark:border-surface-700">
                <div class="mb-4">
                    <label for="name" class="block text-sm font-bold mb-2">Name:</label>
                    <InputText fluid type="text" v-model="form.name" placeholder="Name" />
                    <div v-if="form.errors.name" class="text-red-600 text-sm">{{ form.errors.name  }}</div>
                </div>
                <div class="mb-4">
                    <label for="email" class="block text-sm font-bold mb-2">Email Address:</label>
                    <InputText fluid type="text" v-model="form.email" placeholder="Email" />
                    <div v-if="form.errors.email" class="text-red-600 text-sm">{{ form.errors.email  }}</div>
                </div>
                <div class="flex items-center justify-between">
                    <Button size="small" @click="submitForm" :disabled="form.processing" :loading="form.processing" label="Add User" />
                </div>
            </form>
        </div>
    </PageMain>
</template>

<script setup>
const form = useForm({
    name: null,
    email: null,
})

const submitForm = () => {
    form.post(route('users.store'))
}
</script>
