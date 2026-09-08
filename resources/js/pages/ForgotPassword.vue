<template>
    <SiteLayout title="Forgot password" robots="noindex, nofollow">
        <div class="mt-10 sm:mx-auto sm:w-full sm:max-w-sm">
            <Card>
                <template #header>
                    <div class="font-semibold text-gray-900 dark:text-gray-100 text-base flex items-center space-x-2">
                        <div>Forgot your password?</div>
                    </div>
                </template>
                <template #content>
                    <Alert v-if="status" variant="success" class="mb-4">
                        <AlertDescription>{{ status }}</AlertDescription>
                    </Alert>
                    <p class="text-sm text-muted-foreground mb-4">
                        Enter your email address and we'll send you a link to choose a new password.
                    </p>
                    <form class="space-y-4 w-full" @submit.prevent="submit">
                        <LabelField name="email" label="Email address" required :error="form.errors.email">
                            <InputText v-model="form.email" type="text" fluid :invalid="!!form.errors.email" />
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
                                label="Email password reset link"
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
import { computed } from 'vue';
import { Link, useForm, usePage } from '@inertiajs/vue3';
import SiteLayout from '@/components/site/layout/SiteLayout.vue';
import { Card, LabelField, Input as InputText, FormErrors, Button, Alert, AlertDescription } from '@/components/ui';
import { useFormSubmit } from '@/composables/useFormSubmit';
const { submitForm } = useFormSubmit();

const page = usePage();
const status = computed(() => page.props.flash?.status);

const form = useForm({
    email: null,
});

const submit = () => {
    submitForm(form, 'post', route('password.email'));
};
</script>
