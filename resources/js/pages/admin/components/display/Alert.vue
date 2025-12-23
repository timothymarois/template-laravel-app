<template>
    <LayoutApp
        title="Components - Alerts"
        pageTitle="Alert"
        :pageSidebarItems="sidebarItems"
        pageSidebarTitle="Components"
    >
        <div class="space-y-4">
            <!-- Info Alerts -->
            <Card>
                <CardHeader>
                    <CardTitle>Info Alerts</CardTitle>
                    <CardDescription>Informational messages for users</CardDescription>
                </CardHeader>
                <CardContent class="space-y-4">
                    <div class="rounded-md bg-blue-50 dark:bg-blue-950 border border-blue-200 dark:border-blue-800 p-4">
                        <div class="flex">
                            <Info class="size-5 text-blue-500 shrink-0" />
                            <div class="ml-3">
                                <p class="text-sm text-blue-800 dark:text-blue-200">
                                    A new software update is available. See what's new in version 2.0.
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="rounded-md bg-blue-50 dark:bg-blue-950 border border-blue-200 dark:border-blue-800 p-4">
                        <div class="flex">
                            <Info class="size-5 text-blue-500 shrink-0" />
                            <div class="ml-3">
                                <h3 class="text-sm font-medium text-blue-800 dark:text-blue-200">New features available</h3>
                                <p class="mt-1 text-sm text-blue-700 dark:text-blue-300">
                                    We've added several new features to improve your workflow. Check out the changelog for details.
                                </p>
                            </div>
                        </div>
                    </div>
                </CardContent>
            </Card>

            <!-- Success Alerts -->
            <Card>
                <CardHeader>
                    <CardTitle>Success Alerts</CardTitle>
                    <CardDescription>Positive feedback for completed actions</CardDescription>
                </CardHeader>
                <CardContent class="space-y-4">
                    <div class="rounded-md bg-green-50 dark:bg-green-950 border border-green-200 dark:border-green-800 p-4">
                        <div class="flex">
                            <CheckCircle class="size-5 text-green-500 shrink-0" />
                            <div class="ml-3">
                                <p class="text-sm text-green-800 dark:text-green-200">
                                    Your changes have been saved successfully.
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="rounded-md bg-green-50 dark:bg-green-950 border border-green-200 dark:border-green-800 p-4">
                        <div class="flex">
                            <CheckCircle class="size-5 text-green-500 shrink-0" />
                            <div class="ml-3">
                                <h3 class="text-sm font-medium text-green-800 dark:text-green-200">Order completed</h3>
                                <p class="mt-1 text-sm text-green-700 dark:text-green-300">
                                    Your order #12345 has been placed successfully. You will receive a confirmation email shortly.
                                </p>
                            </div>
                        </div>
                    </div>
                </CardContent>
            </Card>

            <!-- Warning Alerts -->
            <Card>
                <CardHeader>
                    <CardTitle>Warning Alerts</CardTitle>
                    <CardDescription>Cautionary messages requiring attention</CardDescription>
                </CardHeader>
                <CardContent class="space-y-4">
                    <div class="rounded-md bg-yellow-50 dark:bg-yellow-950 border border-yellow-200 dark:border-yellow-800 p-4">
                        <div class="flex">
                            <AlertTriangle class="size-5 text-yellow-500 shrink-0" />
                            <div class="ml-3">
                                <p class="text-sm text-yellow-800 dark:text-yellow-200">
                                    Your trial period ends in 3 days. Upgrade now to keep access.
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="rounded-md bg-yellow-50 dark:bg-yellow-950 border border-yellow-200 dark:border-yellow-800 p-4">
                        <div class="flex">
                            <AlertTriangle class="size-5 text-yellow-500 shrink-0" />
                            <div class="ml-3">
                                <h3 class="text-sm font-medium text-yellow-800 dark:text-yellow-200">Attention needed</h3>
                                <p class="mt-1 text-sm text-yellow-700 dark:text-yellow-300">
                                    Some of your account settings need to be updated. Please review your profile to ensure accuracy.
                                </p>
                            </div>
                        </div>
                    </div>
                </CardContent>
            </Card>

            <!-- Error Alerts -->
            <Card>
                <CardHeader>
                    <CardTitle>Error Alerts</CardTitle>
                    <CardDescription>Critical errors and failure messages</CardDescription>
                </CardHeader>
                <CardContent class="space-y-4">
                    <div class="rounded-md bg-red-50 dark:bg-red-950 border border-red-200 dark:border-red-800 p-4">
                        <div class="flex">
                            <XCircle class="size-5 text-red-500 shrink-0" />
                            <div class="ml-3">
                                <p class="text-sm text-red-800 dark:text-red-200">
                                    There was an error processing your request. Please try again.
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="rounded-md bg-red-50 dark:bg-red-950 border border-red-200 dark:border-red-800 p-4">
                        <div class="flex">
                            <XCircle class="size-5 text-red-500 shrink-0" />
                            <div class="ml-3">
                                <h3 class="text-sm font-medium text-red-800 dark:text-red-200">Payment failed</h3>
                                <p class="mt-1 text-sm text-red-700 dark:text-red-300">
                                    Your payment could not be processed. Please check your card details and try again.
                                </p>
                            </div>
                        </div>
                    </div>
                </CardContent>
            </Card>

            <!-- Errors Component -->
            <Card>
                <CardHeader>
                    <CardTitle>Form Errors Component</CardTitle>
                    <CardDescription>Expandable error list for form validation</CardDescription>
                </CardHeader>
                <CardContent class="space-y-4">
                    <Errors
                        :errors="sampleErrors"
                        title="Please fix the following errors"
                    />
                    <Errors
                        :errors="singleError"
                        title="Validation Error"
                    />
                    <Errors
                        :failed="true"
                        title="Server Error"
                    />
                </CardContent>
            </Card>

            <!-- Alerts with Actions -->
            <Card>
                <CardHeader>
                    <CardTitle>Alerts with Actions</CardTitle>
                    <CardDescription>Alerts that include actionable buttons</CardDescription>
                </CardHeader>
                <CardContent class="space-y-4">
                    <div class="rounded-md bg-blue-50 dark:bg-blue-950 border border-blue-200 dark:border-blue-800 p-4">
                        <div class="flex">
                            <Info class="size-5 text-blue-500 shrink-0" />
                            <div class="ml-3 flex-1">
                                <h3 class="text-sm font-medium text-blue-800 dark:text-blue-200">Update available</h3>
                                <p class="mt-1 text-sm text-blue-700 dark:text-blue-300">
                                    A new version is available with bug fixes and improvements.
                                </p>
                                <div class="mt-3 flex gap-2">
                                    <Button size="sm">Update Now</Button>
                                    <Button size="sm" variant="outline">Learn More</Button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="rounded-md bg-yellow-50 dark:bg-yellow-950 border border-yellow-200 dark:border-yellow-800 p-4">
                        <div class="flex">
                            <AlertTriangle class="size-5 text-yellow-500 shrink-0" />
                            <div class="ml-3 flex-1">
                                <h3 class="text-sm font-medium text-yellow-800 dark:text-yellow-200">Verify your email</h3>
                                <p class="mt-1 text-sm text-yellow-700 dark:text-yellow-300">
                                    Please verify your email address to access all features.
                                </p>
                                <div class="mt-3">
                                    <Button size="sm" variant="outline">Resend verification email</Button>
                                </div>
                            </div>
                        </div>
                    </div>
                </CardContent>
            </Card>

            <!-- Alerts with Actions on Right -->
            <Card>
                <CardHeader>
                    <CardTitle>Alerts with Actions on Right</CardTitle>
                    <CardDescription>Alerts with buttons aligned to the right side</CardDescription>
                </CardHeader>
                <CardContent class="space-y-4">
                    <div class="rounded-md bg-blue-50 dark:bg-blue-950 border border-blue-200 dark:border-blue-800 p-4">
                        <div class="flex items-center justify-between gap-4">
                            <div class="flex items-center">
                                <Info class="size-5 text-blue-500 shrink-0" />
                                <p class="ml-3 text-sm text-blue-800 dark:text-blue-200">
                                    A new software update is available.
                                </p>
                            </div>
                            <Button size="sm">Update</Button>
                        </div>
                    </div>
                    <div class="rounded-md bg-green-50 dark:bg-green-950 border border-green-200 dark:border-green-800 p-4">
                        <div class="flex items-center justify-between gap-4">
                            <div class="flex items-center">
                                <CheckCircle class="size-5 text-green-500 shrink-0" />
                                <p class="ml-3 text-sm text-green-800 dark:text-green-200">
                                    Your profile has been updated successfully.
                                </p>
                            </div>
                            <Button size="sm" variant="outline">View Profile</Button>
                        </div>
                    </div>
                    <div class="rounded-md bg-yellow-50 dark:bg-yellow-950 border border-yellow-200 dark:border-yellow-800 p-4">
                        <div class="flex items-center justify-between gap-4">
                            <div class="flex items-center">
                                <AlertTriangle class="size-5 text-yellow-500 shrink-0" />
                                <p class="ml-3 text-sm text-yellow-800 dark:text-yellow-200">
                                    Your subscription expires in 3 days.
                                </p>
                            </div>
                            <Button size="sm">Renew Now</Button>
                        </div>
                    </div>
                    <div class="rounded-md bg-red-50 dark:bg-red-950 border border-red-200 dark:border-red-800 p-4">
                        <div class="flex items-center justify-between gap-4">
                            <div class="flex items-center">
                                <XCircle class="size-5 text-red-500 shrink-0" />
                                <p class="ml-3 text-sm text-red-800 dark:text-red-200">
                                    Payment failed. Please update your billing info.
                                </p>
                            </div>
                            <Button size="sm" variant="destructive">Fix Payment</Button>
                        </div>
                    </div>
                </CardContent>
            </Card>

            <!-- Dismissible Alerts -->
            <Card>
                <CardHeader>
                    <CardTitle>Dismissible Alerts</CardTitle>
                    <CardDescription>Alerts that can be closed by the user</CardDescription>
                </CardHeader>
                <CardContent class="space-y-4">
                    <div v-if="showInfoAlert" class="rounded-md bg-blue-50 dark:bg-blue-950 border border-blue-200 dark:border-blue-800 p-4">
                        <div class="flex">
                            <Info class="size-5 text-blue-500 shrink-0" />
                            <div class="ml-3 flex-1">
                                <p class="text-sm text-blue-800 dark:text-blue-200">
                                    This is a dismissible info alert. Click the X to close it.
                                </p>
                            </div>
                            <button @click="showInfoAlert = false" class="ml-3 text-blue-500 hover:text-blue-700 cursor-pointer">
                                <X class="size-5" />
                            </button>
                        </div>
                    </div>
                    <div v-if="showSuccessAlert" class="rounded-md bg-green-50 dark:bg-green-950 border border-green-200 dark:border-green-800 p-4">
                        <div class="flex">
                            <CheckCircle class="size-5 text-green-500 shrink-0" />
                            <div class="ml-3 flex-1">
                                <p class="text-sm text-green-800 dark:text-green-200">
                                    This is a dismissible success alert. Click the X to close it.
                                </p>
                            </div>
                            <button @click="showSuccessAlert = false" class="ml-3 text-green-500 hover:text-green-700 cursor-pointer">
                                <X class="size-5" />
                            </button>
                        </div>
                    </div>
                    <div v-if="showWarningAlert" class="rounded-md bg-yellow-50 dark:bg-yellow-950 border border-yellow-200 dark:border-yellow-800 p-4">
                        <div class="flex">
                            <AlertTriangle class="size-5 text-yellow-500 shrink-0" />
                            <div class="ml-3 flex-1">
                                <p class="text-sm text-yellow-800 dark:text-yellow-200">
                                    This is a dismissible warning alert. Click the X to close it.
                                </p>
                            </div>
                            <button @click="showWarningAlert = false" class="ml-3 text-yellow-500 hover:text-yellow-700 cursor-pointer">
                                <X class="size-5" />
                            </button>
                        </div>
                    </div>
                    <div v-if="showErrorAlert" class="rounded-md bg-red-50 dark:bg-red-950 border border-red-200 dark:border-red-800 p-4">
                        <div class="flex">
                            <XCircle class="size-5 text-red-500 shrink-0" />
                            <div class="ml-3 flex-1">
                                <p class="text-sm text-red-800 dark:text-red-200">
                                    This is a dismissible error alert. Click the X to close it.
                                </p>
                            </div>
                            <button @click="showErrorAlert = false" class="ml-3 text-red-500 hover:text-red-700 cursor-pointer">
                                <X class="size-5" />
                            </button>
                        </div>
                    </div>
                    <Button v-if="!showInfoAlert || !showSuccessAlert || !showWarningAlert || !showErrorAlert" variant="outline" size="sm" @click="resetAlerts">
                        Reset Alerts
                    </Button>
                </CardContent>
            </Card>

            <!-- Neutral Alerts -->
            <Card>
                <CardHeader>
                    <CardTitle>Neutral Alerts</CardTitle>
                    <CardDescription>Clean alerts with colored icons and standard borders</CardDescription>
                </CardHeader>
                <CardContent class="space-y-4">
                    <div class="rounded-md border p-4">
                        <div class="flex">
                            <Info class="size-5 text-blue-500 shrink-0" />
                            <div class="ml-3">
                                <p class="text-sm text-foreground">
                                    This is a general information message with a neutral background.
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="rounded-md border p-4">
                        <div class="flex">
                            <Info class="size-5 text-blue-500 shrink-0" />
                            <div class="ml-3">
                                <h3 class="text-sm font-medium text-foreground">Did you know?</h3>
                                <p class="mt-1 text-sm text-muted-foreground">
                                    You can customize your dashboard by dragging and dropping widgets to rearrange them.
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="rounded-md border p-4">
                        <div class="flex items-center justify-between gap-4">
                            <div class="flex items-center">
                                <Info class="size-5 text-blue-500 shrink-0" />
                                <p class="ml-3 text-sm text-foreground">
                                    Your session will expire in 10 minutes.
                                </p>
                            </div>
                            <Button size="sm" variant="outline">Extend Session</Button>
                        </div>
                    </div>
                    <div class="rounded-md border p-4">
                        <div class="flex">
                            <CheckCircle class="size-5 text-green-500 shrink-0" />
                            <div class="ml-3">
                                <p class="text-sm text-foreground">
                                    All systems are operational.
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="rounded-md border p-4">
                        <div class="flex">
                            <AlertTriangle class="size-5 text-yellow-500 shrink-0" />
                            <div class="ml-3">
                                <p class="text-sm text-foreground">
                                    Scheduled maintenance tonight from 2-4 AM.
                                </p>
                            </div>
                        </div>
                    </div>
                </CardContent>
            </Card>

            <!-- Compact Alerts -->
            <Card>
                <CardHeader>
                    <CardTitle>Compact Alerts</CardTitle>
                    <CardDescription>Minimal inline alert styles</CardDescription>
                </CardHeader>
                <CardContent class="space-y-3">
                    <div class="flex items-center gap-2 text-sm text-blue-600 dark:text-blue-400">
                        <Info class="size-4" />
                        <span>This is an inline info message.</span>
                    </div>
                    <div class="flex items-center gap-2 text-sm text-green-600 dark:text-green-400">
                        <CheckCircle class="size-4" />
                        <span>This is an inline success message.</span>
                    </div>
                    <div class="flex items-center gap-2 text-sm text-yellow-600 dark:text-yellow-400">
                        <AlertTriangle class="size-4" />
                        <span>This is an inline warning message.</span>
                    </div>
                    <div class="flex items-center gap-2 text-sm text-red-600 dark:text-red-400">
                        <XCircle class="size-4" />
                        <span>This is an inline error message.</span>
                    </div>
                </CardContent>
            </Card>
        </div>
    </LayoutApp>
</template>

<script setup lang="ts">
import { ref } from 'vue';
import { AdminLayout as LayoutApp } from '@/components/app';
import { Card, CardHeader, CardTitle, CardDescription, CardContent } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Errors } from '@/components/ui/form';
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
