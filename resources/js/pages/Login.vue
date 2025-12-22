<template>
    <LayoutDefault title="Login">
        <div class="mt-10 sm:mx-auto sm:w-full sm:max-w-sm">
            <Card>
                <template #header>
                    <div class="font-semibold text-gray-900 dark:text-gray-100 text-md flex items-center space-x-2">
                        <div>Login</div>
                    </div>
                </template>
                <template #content>
                    <form class="space-y-4 w-full">
                        <LabelField name="email" label="Email address" required :error="form.errors.email">
                            <InputText v-model="form.email" type="text" fluid :invalid="!!form.errors.email" />
                        </LabelField>
                        <LabelField name="password" label="Password" required :error="form.errors.password">
                            <InputText v-model="form.password" type="password" fluid :invalid="!!form.errors.password" />
                        </LabelField>
                    </form>
                </template>
                <template #footer>
                    <div class="w-full flex flex-col space-y-4">
                        <Errors :errors="form.errors" />
                        <div class="w-full">
                            <Button
                                fluid
                                label="Login"
                                :disabled="form.processing"
                                :loading="form.processing"
                                @click="submit"
                            />
                        </div>
                        <div class="text-center">
                            <Link href="/register" class="hover:underline">
                                Don't have an account? Register
                            </Link>
                        </div>
                    </div>
                </template>
            </Card>
        </div>
    </LayoutDefault>
</template>

<script setup>
import { Link, useForm } from '@inertiajs/vue3';
import { DefaultLayout as LayoutDefault } from '@/components/web';
import { Card, LabelField, Input as InputText, Errors, Button } from '@/components/composed';
import { useFormSubmit } from '@/composables/useFormSubmit';
const { submitForm } = useFormSubmit();

const form = useForm({
    email: null,
    password: null,
});

const submit = () => {
    submitForm(form, 'post', route('auth.login'));
};
</script>
