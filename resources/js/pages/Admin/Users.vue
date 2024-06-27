<template>
    <Head title="Users" />
    <header class="bg-white dark:bg-surface-700 shadow-md">
        <div class="flex justify-between items-center mx-auto max-w-7xl px-8 py-4">
            <h1 class="text-lg font-semibold leading-6 text-gray-900 dark:text-white">Users</h1>
            <Button @click="router.visit($route('users.create'))" size="small">Add</Button>
        </div>
    </header>
    <main>
        <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6">
            <ul>
                <li
                    v-for="user in users.data"
                    :key="user.id"
                    class="border-b last:border-none border-gray-100 dark:border-gray-700 p-4 hover:bg-surface-200/50 dark:hover:bg-surface-700/50 flex items-center justify-between"
                >
                    <div class="flex items-center space-x-4">
                        <div class="min-w-[300px]">
                            <h2 class="text-sm font-semibold">{{ user.name }}</h2>
                            <p class="text-xs text-gray-700 dark:text-gray-200">{{ user.email }}</p>
                        </div>
                        <div>
                            <h2 class="text-sm font-semibold">Updated</h2>
                            <p class="text-xs text-gray-700 dark:text-gray-200">{{ convertLocalTimezone(user.updated_at) }}</p>
                        </div>
                    </div>
                    <div>
                        <InputGroup>
                            <Button @click.prevent.stop="openEditModal(user)" outlined label="Edit" icon="pi pi-pencil" size="small" />
                            <Button @click.prevent.stop="remove(user)" outlined  icon="pi pi-trash" size="small" :loading="deleteLoading" />
                        </InputGroup>
                    </div>
                </li>
            </ul>
        </div>
    </main>

    <Dialog v-model:visible="showModal" modal header="Edit User" :style="{ width: '25rem' }">
        <form >
            <div class="mb-4">
                <label for="name" class="block text-sm font-bold mb-2">Name:</label>
                <InputText type="text" v-model="form.name" placeholder="Name" />
                <div v-if="form.errors.name" class="text-red-600 text-sm">{{ form.errors.name  }}</div>
            </div>
            <div class="mb-4">
                <label for="email" class="block text-sm font-bold mb-2">Email Address:</label>
                <InputText type="text" v-model="form.email" placeholder="Email Address" />
                <div v-if="form.errors.email" class="text-red-600 text-sm">{{ form.errors.email  }}</div>
            </div>
        </form>
        <div class="flex justify-start gap-2 pt-2">
            <Button size="small" type="button" label="Save" @click="save" :disabled="form.processing" :loading="form.processing"></Button>
            <Button size="small" text type="button" label="Cancel" severity="secondary" @click="showModal = false;form.reset()"></Button>
        </div>
    </Dialog>
</template>

<script setup>
const props = defineProps({
    users: Object
});

const showModal = ref(false)
const deleteLoading = ref(false)

const form = useForm({
    id: null,
    name: null,
    email: null
})

const convertLocalTimezone = (time) => {
    // 'America/New_York' (pass current users timezone)
    return formatDatetime(time);
}

const openEditModal = (user) => {
    showModal.value = true
    form.id = user.id
    form.name = user.name
    form.email = user.email
};

const remove = (user) => {
    // { preserveState: true  }
    router.delete(route('users.destroy', [user.id]), {
        onStart: visit => {
            // deleteLoading.value = true
        },
        onSuccess: page => {
            // deleteLoading.value = false
        },
    })
}

const save = () => {
    if (form.id) {
        form.put(route('users.update', [form.id]), {
            onSuccess: user => {
                showModal.value = false
            },
        })
    }
};
</script>
