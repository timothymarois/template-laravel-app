<template>
    <AppLayout
        title="Components - Alerts"
        pageTitle="Alert"
        :pageSidebarItems="sidebarItems"
        pageSidebarTitle="Components"
    >
        <div class="space-y-4">
            <!-- Basic Alerts -->
            <Card>
                <CardHeader>
                    <CardTitle>Basic Alerts</CardTitle>
                    <CardDescription>Simple alert messages with different variants</CardDescription>
                </CardHeader>
                <CardContent class="space-y-4">
                    <Alert>
                        <Info class="size-4" />
                        <AlertTitle>Default</AlertTitle>
                        <AlertDescription>
                            This is a default alert with neutral styling.
                        </AlertDescription>
                    </Alert>
                    <Alert variant="info">
                        <Info class="size-4" />
                        <AlertTitle>Information</AlertTitle>
                        <AlertDescription>
                            A new software update is available. See what's new in version 2.0.
                        </AlertDescription>
                    </Alert>
                    <Alert variant="success">
                        <CheckCircle class="size-4" />
                        <AlertTitle>Success</AlertTitle>
                        <AlertDescription>
                            Your changes have been saved successfully.
                        </AlertDescription>
                    </Alert>
                    <Alert variant="warning">
                        <AlertTriangle class="size-4" />
                        <AlertTitle>Warning</AlertTitle>
                        <AlertDescription>
                            Your trial period ends in 3 days. Upgrade now to keep access.
                        </AlertDescription>
                    </Alert>
                    <Alert variant="destructive">
                        <XCircle class="size-4" />
                        <AlertTitle>Error</AlertTitle>
                        <AlertDescription>
                            There was an error processing your request. Please try again.
                        </AlertDescription>
                    </Alert>
                </CardContent>
            </Card>

            <!-- Alerts without Title -->
            <Card>
                <CardHeader>
                    <CardTitle>Alerts without Title</CardTitle>
                    <CardDescription>Simple one-line alert messages</CardDescription>
                </CardHeader>
                <CardContent class="space-y-4">
                    <Alert variant="info">
                        <Info class="size-4" />
                        <AlertDescription>
                            A new software update is available. See what's new in version 2.0.
                        </AlertDescription>
                    </Alert>
                    <Alert variant="success">
                        <CheckCircle class="size-4" />
                        <AlertDescription>
                            Your profile has been updated successfully.
                        </AlertDescription>
                    </Alert>
                    <Alert variant="warning">
                        <AlertTriangle class="size-4" />
                        <AlertDescription>
                            Your session will expire in 10 minutes.
                        </AlertDescription>
                    </Alert>
                    <Alert variant="destructive">
                        <XCircle class="size-4" />
                        <AlertDescription>
                            Payment failed. Please check your card details.
                        </AlertDescription>
                    </Alert>
                </CardContent>
            </Card>

            <!-- Alerts with Actions -->
            <Card>
                <CardHeader>
                    <CardTitle>Alerts with Actions</CardTitle>
                    <CardDescription>Alerts that include actionable buttons</CardDescription>
                </CardHeader>
                <CardContent class="space-y-4">
                    <Alert variant="info">
                        <Info class="size-4" />
                        <AlertTitle>Update available</AlertTitle>
                        <AlertDescription>
                            <p>A new version is available with bug fixes and improvements.</p>
                            <div class="mt-3 flex gap-2">
                                <Button size="sm">Update Now</Button>
                                <Button variant="outline" size="sm">Learn More</Button>
                            </div>
                        </AlertDescription>
                    </Alert>
                    <Alert variant="warning">
                        <AlertTriangle class="size-4" />
                        <AlertTitle>Verify your email</AlertTitle>
                        <AlertDescription>
                            <p>Please verify your email address to access all features.</p>
                            <div class="mt-3">
                                <Button variant="outline" size="sm">Resend verification email</Button>
                            </div>
                        </AlertDescription>
                    </Alert>
                    <Alert variant="destructive">
                        <XCircle class="size-4" />
                        <AlertTitle>Payment failed</AlertTitle>
                        <AlertDescription>
                            <p>Your payment could not be processed. Please check your card details and try again.</p>
                            <div class="mt-3">
                                <Button variant="destructive" size="sm">Update Payment Method</Button>
                            </div>
                        </AlertDescription>
                    </Alert>
                </CardContent>
            </Card>

            <!-- Inline Alerts with Actions -->
            <Card>
                <CardHeader>
                    <CardTitle>Inline Alerts with Actions</CardTitle>
                    <CardDescription>Compact alerts with actions aligned to the right</CardDescription>
                </CardHeader>
                <CardContent class="space-y-4">
                    <Alert variant="info" class="flex items-center">
                        <Info class="size-4" />
                        <AlertDescription class="flex-1">
                            A new software update is available.
                        </AlertDescription>
                        <Button size="sm" class="ml-auto shrink-0">Update</Button>
                    </Alert>
                    <Alert variant="success" class="flex items-center">
                        <CheckCircle class="size-4" />
                        <AlertDescription class="flex-1">
                            Your profile has been updated successfully.
                        </AlertDescription>
                        <Button variant="outline" size="sm" class="ml-auto shrink-0">View Profile</Button>
                    </Alert>
                    <Alert variant="warning" class="flex items-center">
                        <AlertTriangle class="size-4" />
                        <AlertDescription class="flex-1">
                            Your subscription expires in 3 days.
                        </AlertDescription>
                        <Button size="sm" class="ml-auto shrink-0">Renew Now</Button>
                    </Alert>
                </CardContent>
            </Card>

            <!-- Dismissible Alerts -->
            <Card>
                <CardHeader>
                    <CardTitle>Dismissible Alerts</CardTitle>
                    <CardDescription>Alerts that can be closed by the user</CardDescription>
                </CardHeader>
                <CardContent class="space-y-4">
                    <Alert v-if="showInfoAlert" variant="info" class="relative">
                        <Info class="size-4" />
                        <AlertTitle>Did you know?</AlertTitle>
                        <AlertDescription>
                            You can customize your dashboard by dragging and dropping widgets.
                        </AlertDescription>
                        <button
                            class="absolute right-4 top-4 rounded-sm opacity-70 hover:opacity-100 cursor-pointer"
                            @click="showInfoAlert = false"
                        >
                            <X class="size-4" />
                        </button>
                    </Alert>
                    <Alert v-if="showSuccessAlert" variant="success" class="relative">
                        <CheckCircle class="size-4" />
                        <AlertTitle>Changes saved</AlertTitle>
                        <AlertDescription>
                            Your preferences have been updated successfully.
                        </AlertDescription>
                        <button
                            class="absolute right-4 top-4 rounded-sm opacity-70 hover:opacity-100 cursor-pointer"
                            @click="showSuccessAlert = false"
                        >
                            <X class="size-4" />
                        </button>
                    </Alert>
                    <Alert v-if="showWarningAlert" variant="warning" class="relative">
                        <AlertTriangle class="size-4" />
                        <AlertTitle>Attention needed</AlertTitle>
                        <AlertDescription>
                            Some of your account settings need to be updated.
                        </AlertDescription>
                        <button
                            class="absolute right-4 top-4 rounded-sm opacity-70 hover:opacity-100 cursor-pointer"
                            @click="showWarningAlert = false"
                        >
                            <X class="size-4" />
                        </button>
                    </Alert>
                    <Alert v-if="showErrorAlert" variant="destructive" class="relative">
                        <XCircle class="size-4" />
                        <AlertTitle>Error occurred</AlertTitle>
                        <AlertDescription>
                            Failed to sync your data. Please try again later.
                        </AlertDescription>
                        <button
                            class="absolute right-4 top-4 rounded-sm opacity-70 hover:opacity-100 cursor-pointer"
                            @click="showErrorAlert = false"
                        >
                            <X class="size-4" />
                        </button>
                    </Alert>
                    <Button
                        v-if="!showInfoAlert || !showSuccessAlert || !showWarningAlert || !showErrorAlert"
                        variant="outline"
                        @click="resetAlerts"
                    >
                        Reset Alerts
                    </Button>
                </CardContent>
            </Card>

            <!-- Form Errors Component -->
            <Card>
                <CardHeader>
                    <CardTitle>Form Errors Component</CardTitle>
                    <CardDescription>Expandable error list for form validation</CardDescription>
                </CardHeader>
                <CardContent class="space-y-4">
                    <FormErrors
                        :errors="sampleErrors"
                        title="Please fix the following errors"
                    />
                    <FormErrors
                        :errors="singleError"
                        title="Validation Error"
                    />
                    <FormErrors
                        :failed="true"
                        title="Server Error"
                    />
                </CardContent>
            </Card>

            <!-- Variant Comparison -->
            <Card>
                <CardHeader>
                    <CardTitle>Variant Comparison</CardTitle>
                    <CardDescription>All alert variants side by side</CardDescription>
                </CardHeader>
                <CardContent>
                    <div class="grid grid-cols-2 gap-4">
                        <Alert>
                            <Info class="size-4" />
                            <AlertTitle>Default</AlertTitle>
                            <AlertDescription>Neutral styling for general messages.</AlertDescription>
                        </Alert>
                        <Alert variant="info">
                            <Info class="size-4" />
                            <AlertTitle>Info</AlertTitle>
                            <AlertDescription>Informational messages and tips.</AlertDescription>
                        </Alert>
                        <Alert variant="success">
                            <CheckCircle class="size-4" />
                            <AlertTitle>Success</AlertTitle>
                            <AlertDescription>Positive feedback and confirmations.</AlertDescription>
                        </Alert>
                        <Alert variant="warning">
                            <AlertTriangle class="size-4" />
                            <AlertTitle>Warning</AlertTitle>
                            <AlertDescription>Cautionary messages requiring attention.</AlertDescription>
                        </Alert>
                        <Alert variant="destructive" class="col-span-2">
                            <XCircle class="size-4" />
                            <AlertTitle>Destructive</AlertTitle>
                            <AlertDescription>Critical errors and failure messages.</AlertDescription>
                        </Alert>
                    </div>
                </CardContent>
            </Card>
        </div>
    </AppLayout>
</template>

<script setup lang="ts">
import { ref } from 'vue';
import AppLayout from '@/components/app/layout/AppLayout.vue';
import { Card, CardHeader, CardTitle, CardDescription, CardContent } from '@/components/ui/card';
import { Alert, AlertTitle, AlertDescription } from '@/components/ui/alert';
import { Button } from '@/components/ui/button';
import { FormErrors } from '@/components/ui/form-errors';
import { Info, CheckCircle, AlertTriangle, XCircle, X } from 'lucide-vue-next';
import { useShowcaseNav } from '../_composables/useShowcaseNav';

const { sidebarItems } = useShowcaseNav();

// Sample errors for the Errors component
const sampleErrors = [
    'Email address is required',
    'Password must be at least 8 characters',
    'Please accept the terms and conditions',
];

const singleError = ['Invalid email format'];

// Dismissible alert states
const showInfoAlert = ref(true);
const showSuccessAlert = ref(true);
const showWarningAlert = ref(true);
const showErrorAlert = ref(true);

const resetAlerts = () => {
    showInfoAlert.value = true;
    showSuccessAlert.value = true;
    showWarningAlert.value = true;
    showErrorAlert.value = true;
};
</script>
