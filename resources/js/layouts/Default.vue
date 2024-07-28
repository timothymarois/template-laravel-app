<template>

    <div class="min-h-full">
        <Nav />
        <slot />
    </div>

    <Dialog :closable="false" :draggable="false" v-model:visible="showModal" modal header="Your session has ended!" :style="{ width: '25rem' }">
        <template #header>
            <div>
                <div class="font-semibold text-xl pb-2">Login</div>
                <div>Please login to resume your session.</div>
            </div>
        </template>

        <form class="space-y-6 w-full">
            <div class="w-full">
                <label for="email" class="block text-sm font-medium leading-6 text-gray-900 dark:text-gray-200">Email address</label>
                <div class="mt-2 w-full">
                    <InputText fluid type="text" v-model="form.email" />
                    <div v-if="form.errors.email" class="text-sm text-red-500">{{ form.errors.email }}</div>
                </div>
            </div>
            <div class="w-full">
                <div class="flex items-center justify-between">
                    <label for="password" class="block text-sm font-medium leading-6 text-gray-900 dark:text-gray-200">Password</label>
                </div>
                <div class="mt-2 w-full">
                    <InputText fluid type="password" v-model="form.password" />
                    <div v-if="form.errors.password" class="text-sm text-red-500">{{ form.errors.password }}</div>
                </div>
            </div>
            <div class="w-full">
                <Button
                    label="Login"
                    @click="submit"
                    :disabled="form.processing"
                    :loading="form.processing"
                />
            </div>
        </form>
    </Dialog>

</template>

<script setup>
const props = defineProps({
    users: Object
});

const showModal = ref(false);

const form = useForm({
    email: null,
    password: null,
});

const submit = () => {
    form.post(route('auth.login'), {
        preserveState: true,
        preserveScroll: true,
        replace: false,
        onSuccess: () => {
            showModal.value = false
            form.reset('password')
        },
    });
};
</script>
