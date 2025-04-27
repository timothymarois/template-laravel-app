<template>
    <Head title="Register" />
    <LayoutDefault>
        <PageMain>
            <div class="mt-10 sm:mx-auto sm:w-full sm:max-w-sm border-2 border-gray-200 dark:border-surface-700 p-6 rounded-lg">
                <div class="pb-4">
                    <div class="leading-3 font-semibold text-lg">
                        Create Account
                    </div>
                </div>
                <form class="space-y-6 w-full">
                    <div class="w-full">
                        <label
                            for="name"
                            class="block text-sm font-medium leading-6 text-gray-900 dark:text-gray-200"
                        >Name</label>
                        <div class="mt-2 w-full">
                            <InputText
                                v-model="form.name"
                                fluid
                                type="text"
                            />
                            <div v-if="form.errors.name" class="text-sm text-red-500">
                                {{ form.errors.name }}
                            </div>
                        </div>
                    </div>
                    <div class="w-full">
                        <label
                            for="email"
                            class="block text-sm font-medium leading-6 text-gray-900 dark:text-gray-200"
                        >Email address</label>
                        <div class="mt-2 w-full">
                            <InputText
                                v-model="form.email"
                                fluid
                                type="text"
                            />
                            <div v-if="form.errors.email" class="text-sm text-red-500">
                                {{ form.errors.email }}
                            </div>
                        </div>
                    </div>
                    <div class="w-full">
                        <div class="flex items-center justify-between">
                            <label
                                for="password"
                                class="block text-sm font-medium leading-6 text-gray-900 dark:text-gray-200"
                            >Password</label>
                        </div>
                        <div class="mt-2 w-full">
                            <InputText
                                v-model="form.password"
                                fluid
                                type="password"
                            />
                            <div v-if="form.errors.password" class="text-sm text-red-500">
                                {{ form.errors.password }}
                            </div>
                        </div>
                    </div>
                    <div class="w-full">
                        <div class="flex items-center justify-between">
                            <label
                                for="password"
                                class="block text-sm font-medium leading-6 text-gray-900 dark:text-gray-200"
                            >Password Confirmation</label>
                        </div>
                        <div class="mt-2 w-full">
                            <InputText
                                v-model="form.password_confirmation"
                                fluid
                                type="password"
                            />
                            <div v-if="form.errors.password_confirmation" class="text-sm text-red-500">
                                {{ form.errors.password_confirmation }}
                            </div>
                        </div>
                    </div>
                    <div class="w-full">
                        <Button
                            label="Create Account"
                            :disabled="form.processing"
                            :loading="form.processing"
                            @click="submit"
                        />
                    </div>
                </form>
                <div class="mt-4">
                    <Link href="/login">
                        Already have an account? Login
                    </Link>
                </div>
            </div>
        </PageMain>
    </LayoutDefault>
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
