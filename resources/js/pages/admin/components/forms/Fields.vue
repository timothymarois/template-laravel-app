<template>
    <LayoutApp
        title="Components - Form Fields"
        pageTitle="Forms"
        :pageNavItems="sideNavItems"
        :pageTabs="formsTabs"
    >
        <div class="space-y-6">
            <Card>
                <CardHeader>
                    <CardTitle>Label Field</CardTitle>
                    <CardDescription>Form field wrapper with label, error, and tooltip support</CardDescription>
                </CardHeader>
                <CardContent class="space-y-6">
                    <div class="grid grid-cols-2 gap-4">
                        <LabelField label="Basic Label" name="basic">
                            <Input placeholder="Enter value" fluid />
                        </LabelField>

                        <LabelField label="Required Field" name="required" required>
                            <Input placeholder="Required input" fluid />
                        </LabelField>

                        <LabelField label="With Error" name="error" error="This field is required">
                            <Input placeholder="Has error" invalid fluid />
                        </LabelField>

                        <LabelField label="With Tooltip" name="tooltip" tooltip="This is helpful information about the field">
                            <Input placeholder="Hover the icon" fluid />
                        </LabelField>
                    </div>
                </CardContent>
            </Card>

            <Card>
                <CardHeader>
                    <CardTitle>In Context: Edit Profile Form</CardTitle>
                    <CardDescription>A realistic form using all form components together</CardDescription>
                </CardHeader>
                <CardContent>
                    <div class="max-w-xl space-y-4">
                        <div class="grid grid-cols-2 gap-4">
                            <LabelField label="First Name" name="firstName" required>
                                <Input v-model="profileForm.firstName" placeholder="John" fluid />
                            </LabelField>
                            <LabelField label="Last Name" name="lastName" required>
                                <Input v-model="profileForm.lastName" placeholder="Doe" fluid />
                            </LabelField>
                        </div>

                        <LabelField label="Email" name="email" required>
                            <Input v-model="profileForm.email" type="email" placeholder="john@example.com" fluid />
                        </LabelField>

                        <LabelField label="Role" name="role" required>
                            <Select v-model="profileForm.role" :options="roleOptions" placeholder="Select role" />
                        </LabelField>

                        <LabelField label="Status" name="status">
                            <RadioGroup v-model="profileForm.status" class="flex gap-6 mt-1">
                                <div class="flex items-center gap-2">
                                    <RadioGroupItem value="active" id="status-active" />
                                    <Label for="status-active">Active</Label>
                                </div>
                                <div class="flex items-center gap-2">
                                    <RadioGroupItem value="inactive" id="status-inactive" />
                                    <Label for="status-inactive">Inactive</Label>
                                </div>
                            </RadioGroup>
                        </LabelField>

                        <div class="pt-2">
                            <label class="flex items-center gap-2 cursor-pointer">
                                <Checkbox v-model="profileForm.notifications" />
                                <span class="text-sm">Receive email notifications</span>
                            </label>
                        </div>

                        <div class="flex justify-end gap-2 pt-4 border-t">
                            <Button variant="outline">Cancel</Button>
                            <Button>Save Changes</Button>
                        </div>
                    </div>
                </CardContent>
            </Card>
        </div>
    </LayoutApp>
</template>

<script setup lang="ts">
import { reactive } from 'vue';
import { AdminLayout as LayoutApp } from '@/components/app';
import { Card, CardHeader, CardTitle, CardDescription, CardContent } from '@/components/ui/card';
import { Input, Select, Checkbox, LabelField } from '@/components/ui/form';
import { Button } from '@/components/ui/button';
import { RadioGroup, RadioGroupItem } from '@/components/ui/radio-group';
import { Label } from '@/components/ui/label';
import { useShowcaseNav } from '../_composables/useShowcaseNav';

const { sideNavItems, formsTabs } = useShowcaseNav();

const profileForm = reactive({
    firstName: '',
    lastName: '',
    email: '',
    role: '',
    status: 'active',
    notifications: true,
});

const roleOptions = [
    { label: 'Admin', value: 'admin' },
    { label: 'Editor', value: 'editor' },
    { label: 'User', value: 'user' },
];
</script>
