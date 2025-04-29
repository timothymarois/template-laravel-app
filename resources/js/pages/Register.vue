<template>
    <Head title="Register" />
    <LayoutDefault>
        <PageMain>
            <div class="mt-10 sm:mx-auto sm:w-full sm:max-w-sm">
                <Card>
                    <template #title>
                        Register
                    </template>
                    <template #content>
                        <form class="space-y-4 w-full">
                            <AtlasFormSlot name="name" label="Name" required :error="form.errors.name">
                                <InputText id="name" v-model="form.name" type="text" fluid :invalid="!!form.errors.name" />
                            </AtlasFormSlot>
                            <AtlasFormSlot name="email" label="Email address" required :error="form.errors.email">
                                <InputText id="email" v-model="form.email" type="text" fluid :invalid="!!form.errors.email" />
                            </AtlasFormSlot>
                            <AtlasFormSlot name="password" label="Password" required :error="form.errors.password">
                                <InputText id="password" v-model="form.password" type="password" fluid :invalid="!!form.errors.password" />
                            </AtlasFormSlot>
                            <AtlasFormSlot name="password_confirmation" label="Password confirmation" required :error="form.errors.password_confirmation">
                                <InputText id="password_confirmation" v-model="form.password_confirmation" type="password" fluid :invalid="!!form.errors.password_confirmation" />
                            </AtlasFormSlot>
                            <div class="w-full">
                                <Button
                                    label="Create account"
                                    :disabled="form.processing"
                                    :loading="form.processing"
                                    @click="submit"
                                />
                            </div>
                        </form>
                        <div class="mt-4">
                            <Link href="/login" class="hover:underline">
                                Already have an account? Login
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
