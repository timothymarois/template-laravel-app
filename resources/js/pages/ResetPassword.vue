<template>
    <SiteLayout title="Reset password" robots="noindex, nofollow">
        <div class="mt-10 sm:mx-auto sm:w-full sm:max-w-sm">
            <Card>
                <template #header>
                    <div class="font-semibold text-gray-900 dark:text-gray-100 text-base flex items-center space-x-2">
                        <div>Choose a new password</div>
                    </div>
                </template>
                <template #content>
                    <form class="space-y-4 w-full" @submit.prevent="submit">
                        <LabelField name="email" label="Email address" required :error="form.errors.email">
                            <InputText v-model="form.email" type="text" fluid :invalid="!!form.errors.email" />
                        </LabelField>
                        <LabelField name="password" label="New password" required :error="form.errors.password">
                            <InputText v-model="form.password" type="password" fluid :invalid="!!form.errors.password" />
                        </LabelField>
                        <LabelField
                            name="password_confirmation"
                            label="Confirm new password"
                            required
                            :error="form.errors.password_confirmation"
                        >
                            <InputText
                                v-model="form.password_confirmation"
                                type="password"
                                fluid
                                :invalid="!!form.errors.password_confirmation"
                            />
                        </LabelField>
                        <button type="submit" class="hidden" tabindex="-1" aria-hidden="true"></button>
                    </form>
                </template>
                <template #footer>
                    <div class="w-full flex flex-col space-y-4">
                        <FormErrors :errors="form.errors" />
                        <div class="w-full">
                            <Button
                                fluid
                                label="Reset password"
                                :disabled="form.processing"
                                :loading="form.processing"
                                @click="submit"
                            />
                        </div>
                        <div class="text-center">
                            <Link :href="$route('login')" class="cursor-pointer hover:underline">
                                Back to login
                            </Link>
                        </div>
                    </div>
                </template>
            </Card>
        </div>
    </SiteLayout>
</template>

<script setup>
import { Link, useForm } from '@inertiajs/vue3';
import SiteLayout from '@/components/site/layout/SiteLayout.vue';
import { Card, LabelField, Input as InputText, FormErrors, Button } from '@/components/ui';
import { useFormSubmit } from '@/composables/useFormSubmit';
const { submitForm } = useFormSubmit();

const props = defineProps({
    token: { type: String, required: true },
    email: { type: String, default: '' },
});

const form = useForm({
    token: props.token,
    email: props.email,
    password: null,
    password_confirmation: null,
});

const submit = () => {
    submitForm(form, 'post', route('password.store'));
};
</script>
