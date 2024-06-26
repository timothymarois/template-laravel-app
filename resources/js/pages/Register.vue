<template>
    <div>
        <div class="mt-10 sm:mx-auto sm:w-full sm:max-w-sm border border-2 border-gray-200 dark:border-surface-700 p-6 rounded-lg">
            <div class="pb-4">
                <div class="leading-3 font-semibold text-lg">Create Account</div>
            </div>
            <form class="space-y-6 w-full">
                <div class="w-full">
                    <label for="name" class="block text-sm font-medium leading-6 text-gray-900 dark:text-gray-200">Name</label>
                    <div class="mt-2 w-full">
                        <InputText type="text" v-model="form.name" />
                        <div v-if="form.errors.name" class="text-sm text-red-500">{{ form.errors.name }}</div>
                    </div>
                </div>
                <div class="w-full">
                    <label for="email" class="block text-sm font-medium leading-6 text-gray-900 dark:text-gray-200">Email address</label>
                    <div class="mt-2 w-full">
                        <InputText type="text" v-model="form.email" />
                        <div v-if="form.errors.email" class="text-sm text-red-500">{{ form.errors.email }}</div>
                    </div>
                </div>
                <div class="w-full">
                    <div class="flex items-center justify-between">
                        <label for="password" class="block text-sm font-medium leading-6 text-gray-900 dark:text-gray-200">Password</label>
                    </div>
                    <div class="mt-2 w-full">
                        <InputText type="password" v-model="form.password" />
                        <div v-if="form.errors.password" class="text-sm text-red-500">{{ form.errors.password }}</div>
                    </div>
                </div>
                <div class="w-full">
                    <div class="flex items-center justify-between">
                        <label for="password" class="block text-sm font-medium leading-6 text-gray-900 dark:text-gray-200">Password Confirmation</label>
                    </div>
                    <div class="mt-2 w-full">
                        <InputText type="password" v-model="form.password_confirmation" />
                        <div v-if="form.errors.password_confirmation" class="text-sm text-red-500">{{ form.errors.password_confirmation }}</div>
                    </div>
                </div>
                <div class="w-full">
                    <Button
                        label="Create Account"
                        @click="submit"
                        :disabled="form.processing"
                        :loading="form.processing"
                    />
                </div>
            </form>
            <!-- <div v-if="form.isDirty">Changes have been made.</div> -->

            <div class="mt-4">
                <Link href="/login">Already have an account? Login</Link>
            </div>
        </div>
    </div>
</template>

<script setup>
const form = useForm({
    name: null,
    email: null,
    password: null,
    password_confirmation: null
});

const submit = () => {
    form.post(route('auth.register'), {
        preserveScroll: true,
        onSuccess: () => form.reset('password'),
    });
};

</script>
