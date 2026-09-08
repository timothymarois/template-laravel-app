<template>
    <SiteLayout title="Login" robots="noindex, nofollow">
        <div class="mt-10 sm:mx-auto sm:w-full sm:max-w-sm">
            <Card>
                <template #header>
                    <div class="font-semibold text-gray-900 dark:text-gray-100 text-md flex items-center space-x-2">
                        <div>Login</div>
                    </div>
                </template>
                <template #content>
                    <Alert v-if="flash.error" variant="destructive" class="mb-4">
                        <AlertDescription>{{ flash.error }}</AlertDescription>
                    </Alert>
                    <Alert v-else-if="flash.status" variant="success" class="mb-4">
                        <AlertDescription>{{ flash.status }}</AlertDescription>
                    </Alert>
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
                        <FormErrors :errors="form.errors" />
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
                            <Link :href="$route('password.request')" class="cursor-pointer hover:underline">
                                Forgot your password?
                            </Link>
                        </div>
                        <div class="text-center">
                            <Link :href="$route('register')" class="cursor-pointer hover:underline">
                                Don't have an account? Register
                            </Link>
                        </div>
                    </div>
                </template>
            </Card>
        </div>
    </SiteLayout>
</template>

<script setup>
import { computed } from 'vue';
import { Link, useForm, usePage } from '@inertiajs/vue3';
import SiteLayout from '@/components/site/layout/SiteLayout.vue';
import { Card, LabelField, Input as InputText, FormErrors, Button, Alert, AlertDescription } from '@/components/ui';
import { useFormSubmit } from '@/composables/useFormSubmit';
const { submitForm } = useFormSubmit();

const page = usePage();
const flash = computed(() => page.props.flash ?? {});

const form = useForm({
    email: null,
    password: null,
});

const submit = () => {
    submitForm(form, 'post', route('auth.login'));
};
</script>
