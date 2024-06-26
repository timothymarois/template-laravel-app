<template>
    <Head title="New User" />
    <header class="bg-white dark:bg-surface-700 shadow-md">
        <div class="flex justify-between items-center mx-auto max-w-7xl px-8 py-4">
            <h1 class="text-lg font-semibold leading-6 text-gray-900 dark:text-white">New User</h1>
            <Button outlined @click="router.visit($route('users.index'))" size="small">View All</Button>
        </div>
    </header>
    <main>
        <div class="mx-auto max-w-2xl px-4 py-6 sm:px-6">
            <form  class="shadow-md rounded-lg p-6 border border-surface-200 dark:border-surface-700">
                <div class="mb-4">
                    <label for="name" class="block text-sm font-bold mb-2">Name:</label>
                    <InputText type="text" v-model="form.name" placeholder="Name" />
                    <div v-if="form.errors.name" class="text-red-600 text-sm">{{ form.errors.name  }}</div>
                </div>
                <div class="mb-4">
                    <label for="email" class="block text-sm font-bold mb-2">Email Address:</label>
                    <InputText type="text" v-model="form.email" placeholder="Email" />
                    <div v-if="form.errors.email" class="text-red-600 text-sm">{{ form.errors.email  }}</div>
                </div>
                <div class="flex items-center justify-between">
                    <Button size="small" @click="submitForm" :disabled="form.processing" :loading="form.processing" label="Add User" />
                </div>
            </form>
        </div>
    </main>
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
