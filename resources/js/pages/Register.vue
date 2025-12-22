<template>
    <LayoutDefault title="Register">
        <div class="mt-10 sm:mx-auto sm:w-full sm:max-w-sm">
            <Card>
                <template #header>
                    <div class="font-semibold text-gray-900 dark:text-gray-100 text-md flex items-center space-x-2">
                        <div>Create an account</div>
                    </div>
                </template>
                <template #content>
                    <form class="space-y-4 w-full">
                        <LabelField name="name" label="Name" required :error="form.errors.name">
                            <InputText id="name" v-model="form.name" type="text" fluid :invalid="!!form.errors.name" />
                        </LabelField>
                        <LabelField name="email" label="Email address" required :error="form.errors.email">
                            <InputText id="email" v-model="form.email" type="text" fluid :invalid="!!form.errors.email" />
                        </LabelField>
                        <LabelField name="password" label="Password" required :error="form.errors.password">
                            <InputText id="password" v-model="form.password" type="password" fluid :invalid="!!form.errors.password" />
                        </LabelField>
                        <LabelField name="password_confirmation" label="Password confirmation" required :error="form.errors.password_confirmation">
                            <InputText id="password_confirmation" v-model="form.password_confirmation" type="password" fluid :invalid="!!form.errors.password_confirmation" />
                        </LabelField>
                    </form>
                </template>
                <template #footer>
                    <div class="w-full flex flex-col space-y-4">
                        <Errors :errors="form.errors" />
                        <div class="w-full">
                            <Button
                                fluid
                                label="Create account"
                                :disabled="form.processing"
                                :loading="form.processing"
                                @click="submit"
                            />
                        </div>
                        <div class="text-center">
                            <Link href="/login" class="hover:underline">
                                Already have an account? Login
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
import { DefaultLayout as LayoutDefault } from '@/components/site';
import { Card, LabelField, Input as InputText, Errors, Button } from '@/components/ui';
import { useFormSubmit } from '@/composables/useFormSubmit';
const { submitForm } = useFormSubmit();

const form = useForm({
    name: null,
    email: null,
    password: null,
    password_confirmation: null
});

const submit = () => {
    submitForm(form, 'post', route('auth.register'));
};
</script>
