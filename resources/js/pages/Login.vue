<template>
    <Head title="Login" />
    <LayoutDefault>
        <PageMain>
            <div class="mt-10 sm:mx-auto sm:w-full sm:max-w-sm">
                <Card>
                    <template #title>
                        Login
                    </template>
                    <template #content>
                        <form class="space-y-4 w-full">
                            <AtlasFormSlot name="email" label="Email address" required :error="form.errors.email">
                                <InputText v-model="form.email" type="text" fluid :invalid="!!form.errors.email" />
                            </AtlasFormSlot>
                            <AtlasFormSlot name="password" label="Password" required :error="form.errors.password">
                                <InputText v-model="form.password" type="password" fluid :invalid="!!form.errors.password" />
                            </AtlasFormSlot>
                            <div class="w-full">
                                <Button
                                    raised
                                    label="Login"
                                    :disabled="form.processing"
                                    :loading="form.processing"
                                    @click="submit"
                                />
                            </div>
                        </form>
                        <div class="mt-4">
                            <Link href="/register" class="hover:underline">
                                Don't have an account? Register
                            </Link>
                        </div>
                    </template>
                </Card>
            </div>
        </PageMain>
    </LayoutDefault>
</template>

<script setup>
const form = useForm({
    email: null,
    password: null,
});

const submit = () => {
    form.post(route('auth.login'), {
        preserveScroll: true,
        onSuccess: () => form.reset('password'),
    });
};
</script>
