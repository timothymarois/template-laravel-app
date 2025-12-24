<template>
    <LayoutApp
        title="Components - Sheet"
        pageTitle="Sheet"
        :pageSidebarItems="sidebarItems"
        pageSidebarTitle="Components"
    >
        <div class="space-y-4">
            <!-- Sheet Examples -->
            <Card>
                <CardHeader>
                    <CardTitle>Sheet Positions</CardTitle>
                    <CardDescription>Sliding panels from screen edges</CardDescription>
                </CardHeader>
                <CardContent>
                    <div class="flex gap-2 flex-wrap">
                        <Sheet v-model:open="showRightSheet">
                            <SheetTrigger as-child>
                                <Button variant="outline">Right</Button>
                            </SheetTrigger>
                            <SheetContent side="right">
                                <SheetHeader>
                                    <SheetTitle>Right Sheet</SheetTitle>
                                    <SheetDescription>This sheet slides in from the right.</SheetDescription>
                                </SheetHeader>
                                <div class="py-4">
                                    <p class="text-sm text-muted-foreground">Sheet content goes here. This is the most common position for detail panels and forms.</p>
                                </div>
                            </SheetContent>
                        </Sheet>

                        <Sheet v-model:open="showLeftSheet">
                            <SheetTrigger as-child>
                                <Button variant="outline">Left</Button>
                            </SheetTrigger>
                            <SheetContent side="left">
                                <SheetHeader>
                                    <SheetTitle>Left Sheet</SheetTitle>
                                    <SheetDescription>This sheet slides in from the left.</SheetDescription>
                                </SheetHeader>
                                <div class="py-4">
                                    <p class="text-sm text-muted-foreground">Often used for navigation menus or filters.</p>
                                </div>
                            </SheetContent>
                        </Sheet>

                        <Sheet v-model:open="showTopSheet">
                            <SheetTrigger as-child>
                                <Button variant="outline">Top</Button>
                            </SheetTrigger>
                            <SheetContent side="top">
                                <SheetHeader>
                                    <SheetTitle>Top Sheet</SheetTitle>
                                    <SheetDescription>This sheet slides in from the top.</SheetDescription>
                                </SheetHeader>
                                <div class="py-4">
                                    <p class="text-sm text-muted-foreground">Useful for notifications or announcements.</p>
                                </div>
                            </SheetContent>
                        </Sheet>

                        <Sheet v-model:open="showBottomSheet">
                            <SheetTrigger as-child>
                                <Button variant="outline">Bottom</Button>
                            </SheetTrigger>
                            <SheetContent side="bottom">
                                <SheetHeader>
                                    <SheetTitle>Bottom Sheet</SheetTitle>
                                    <SheetDescription>This sheet slides in from the bottom.</SheetDescription>
                                </SheetHeader>
                                <div class="py-4">
                                    <p class="text-sm text-muted-foreground">Common on mobile for action sheets.</p>
                                </div>
                            </SheetContent>
                        </Sheet>
                    </div>
                </CardContent>
            </Card>

            <!-- Sheet with Form -->
            <Card>
                <CardHeader>
                    <CardTitle>Sheet with Form</CardTitle>
                    <CardDescription>Use sheets for forms and data entry</CardDescription>
                </CardHeader>
                <CardContent>
                    <Sheet v-model:open="showFormSheet">
                        <SheetTrigger as-child>
                            <Button>Add User</Button>
                        </SheetTrigger>
                        <SheetContent side="right" class="w-[400px] sm:w-[480px] flex flex-col p-0">
                            <SheetHeader class="px-6 py-4 border-b">
                                <SheetTitle>Add User</SheetTitle>
                                <SheetDescription>Create a new user account.</SheetDescription>
                            </SheetHeader>
                            <div class="flex-1 overflow-y-auto px-6 py-4">
                                <div class="rounded-lg border bg-card p-4 space-y-4">
                                    <h4 class="font-medium text-sm">Account Details</h4>
                                    <div class="space-y-4">
                                        <LabelField label="Full Name" name="userName" required>
                                            <Input id="userName" placeholder="John Doe" fluid>
                                                <template #icon><User class="size-4" /></template>
                                            </Input>
                                        </LabelField>
                                        <LabelField label="Email Address" name="userEmail" required>
                                            <Input id="userEmail" type="email" placeholder="john@example.com" fluid>
                                                <template #icon><Mail class="size-4" /></template>
                                            </Input>
                                        </LabelField>
                                        <div class="grid grid-cols-2 gap-4">
                                            <LabelField label="Role" name="userRole" required>
                                                <Select id="userRole" :options="roleOptions" placeholder="Select role" fluid />
                                            </LabelField>
                                            <LabelField label="Department" name="userDept">
                                                <Select id="userDept" :options="deptOptions" placeholder="Select" fluid />
                                            </LabelField>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <SheetFooter class="px-6 py-4 border-t">
                                <Button variant="outline" @click="showFormSheet = false">Cancel</Button>
                                <Button @click="showFormSheet = false">Create User</Button>
                            </SheetFooter>
                        </SheetContent>
                    </Sheet>
                </CardContent>
            </Card>

            <!-- Settings Sheet -->
            <Card>
                <CardHeader>
                    <CardTitle>Settings Panel</CardTitle>
                    <CardDescription>Sheet with settings and preferences</CardDescription>
                </CardHeader>
                <CardContent>
                    <Sheet v-model:open="showSettingsSheet">
                        <SheetTrigger as-child>
                            <Button variant="outline">
                                <Settings class="size-4 mr-2" />
                                Settings
                            </Button>
                        </SheetTrigger>
                        <SheetContent side="right" class="w-[420px] flex flex-col p-0">
                            <SheetHeader class="px-6 py-4 border-b">
                                <SheetTitle>Settings</SheetTitle>
                                <SheetDescription>Manage your account preferences.</SheetDescription>
                            </SheetHeader>
                            <div class="flex-1 overflow-y-auto">
                                <!-- Notifications Section -->
                                <div class="px-6 py-4 border-b">
                                    <h4 class="text-sm font-medium mb-1">Notifications</h4>
                                    <p class="text-xs text-muted-foreground mb-4">Choose how you want to be notified.</p>
                                    <div class="space-y-4">
                                        <label class="flex items-center justify-between gap-4 cursor-pointer">
                                            <div>
                                                <div class="text-sm font-medium">Email notifications</div>
                                                <div class="text-xs text-muted-foreground">Receive updates about your account via email.</div>
                                            </div>
                                            <Switch v-model="settingsForm.emailNotifications" />
                                        </label>
                                        <label class="flex items-center justify-between gap-4 cursor-pointer">
                                            <div>
                                                <div class="text-sm font-medium">Push notifications</div>
                                                <div class="text-xs text-muted-foreground">Get instant alerts on your device.</div>
                                            </div>
                                            <Switch v-model="settingsForm.pushNotifications" />
                                        </label>
                                        <label class="flex items-center justify-between gap-4 cursor-pointer">
                                            <div>
                                                <div class="text-sm font-medium">Marketing emails</div>
                                                <div class="text-xs text-muted-foreground">Receive tips, product updates and more.</div>
                                            </div>
                                            <Switch v-model="settingsForm.marketingEmails" />
                                        </label>
                                    </div>
                                </div>
                                <!-- Appearance Section -->
                                <div class="px-6 py-4 border-b">
                                    <h4 class="text-sm font-medium mb-1">Appearance</h4>
                                    <p class="text-xs text-muted-foreground mb-4">Customize how the app looks and feels.</p>
                                    <div class="space-y-4">
                                        <LabelField label="Theme" name="settingsTheme">
                                            <Select id="settingsTheme" v-model="settingsForm.theme" :options="themeOptions" fluid />
                                        </LabelField>
                                        <LabelField label="Language" name="settingsLanguage">
                                            <Select id="settingsLanguage" v-model="settingsForm.language" :options="langOptions" fluid />
                                        </LabelField>
                                    </div>
                                </div>
                                <!-- Privacy Section -->
                                <div class="px-6 py-4">
                                    <h4 class="text-sm font-medium mb-1">Privacy</h4>
                                    <p class="text-xs text-muted-foreground mb-4">Control your privacy settings.</p>
                                    <div class="space-y-4">
                                        <label class="flex items-start justify-between gap-4 cursor-pointer">
                                            <div>
                                                <div class="text-sm font-medium">Profile visibility</div>
                                                <div class="text-xs text-muted-foreground">Allow others to see your profile.</div>
                                            </div>
                                            <Checkbox v-model="settingsForm.profileVisible" class="mt-0.5" />
                                        </label>
                                        <label class="flex items-start justify-between gap-4 cursor-pointer">
                                            <div>
                                                <div class="text-sm font-medium">Activity status</div>
                                                <div class="text-xs text-muted-foreground">Show when you're online.</div>
                                            </div>
                                            <Checkbox v-model="settingsForm.activityStatus" class="mt-0.5" />
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <SheetFooter class="px-6 py-4 border-t">
                                <Button variant="outline" @click="showSettingsSheet = false">Cancel</Button>
                                <Button @click="showSettingsSheet = false">Save Changes</Button>
                            </SheetFooter>
                        </SheetContent>
                    </Sheet>
                </CardContent>
            </Card>

            <!-- Sheet Form with Tabs -->
            <Card>
                <CardHeader>
                    <CardTitle>Sheet Form with Tabs</CardTitle>
                    <CardDescription>Complex forms with tabbed navigation</CardDescription>
                </CardHeader>
                <CardContent>
                    <div class="flex gap-2">
                        <Button @click="showSheetForm = true">Add Employee</Button>
                        <Button variant="outline" @click="showSheetFormWithErrors = true">With Validation Errors</Button>
                    </div>

                    <!-- Basic Sheet Form -->
                    <SheetForm
                        v-model="showSheetForm"
                        title="Add Employee"
                        :tabs="[{ title: 'Personal Info' }, { title: 'Employment' }, { title: 'Documents', disabled: true }]"
                        position="right"
                        width="550px"
                        :loading="sheetLoading"
                        @submit="handleSheetSubmit"
                    >
                        <!-- Tab 1: Personal Info -->
                        <div class="space-y-4">
                            <div class="grid grid-cols-2 gap-4">
                                <LabelField label="First Name" name="firstName" required>
                                    <Input id="firstName" v-model="employeeForm.firstName" placeholder="John" fluid />
                                </LabelField>
                                <LabelField label="Last Name" name="lastName" required>
                                    <Input id="lastName" v-model="employeeForm.lastName" placeholder="Doe" fluid />
                                </LabelField>
                            </div>
                            <LabelField label="Email" name="email" required>
                                <Input id="email" v-model="employeeForm.email" type="email" placeholder="john.doe@company.com" fluid>
                                    <template #icon><Mail class="size-4" /></template>
                                </Input>
                            </LabelField>
                            <LabelField label="Phone" name="phone">
                                <Input id="phone" v-model="employeeForm.phone" placeholder="+1 (555) 123-4567" fluid>
                                    <template #icon><Phone class="size-4" /></template>
                                </Input>
                            </LabelField>
                            <LabelField label="Date of Birth" name="dob">
                                <Input id="dob" v-model="employeeForm.dob" type="date" fluid />
                            </LabelField>
                        </div>

                        <!-- Tab 2: Employment -->
                        <template #tab-1>
                            <div class="space-y-4">
                                <LabelField label="Department" name="department" required>
                                    <Select id="department" v-model="employeeForm.department" :options="deptOptions" placeholder="Select department" fluid />
                                </LabelField>
                                <LabelField label="Position" name="position" required>
                                    <Input id="position" v-model="employeeForm.position" placeholder="Software Engineer" fluid />
                                </LabelField>
                                <LabelField label="Manager" name="manager">
                                    <Select id="manager" v-model="employeeForm.manager" :options="managerOptions" placeholder="Select manager" fluid />
                                </LabelField>
                                <div class="grid grid-cols-2 gap-4">
                                    <LabelField label="Start Date" name="startDate" required>
                                        <Input id="startDate" v-model="employeeForm.startDate" type="date" fluid />
                                    </LabelField>
                                    <LabelField label="Employment Type" name="type">
                                        <Select id="type" v-model="employeeForm.type" :options="employmentTypeOptions" placeholder="Select type" fluid />
                                    </LabelField>
                                </div>
                                <LabelField label="Notes" name="notes">
                                    <Input id="notes" v-model="employeeForm.notes" placeholder="Additional notes..." fluid />
                                </LabelField>
                            </div>
                        </template>
                    </SheetForm>

                    <!-- Sheet Form with Errors -->
                    <SheetForm
                        v-model="showSheetFormWithErrors"
                        title="Add Employee (With Errors)"
                        :tabs="[{ title: 'Personal Info' }, { title: 'Employment' }]"
                        position="right"
                        width="550px"
                        :loading="sheetLoadingErrors"
                        :errors="sheetFormErrors"
                        @submit="handleSheetSubmitWithErrors"
                    >
                        <!-- Tab 1: Personal Info -->
                        <div class="space-y-4">
                            <div class="grid grid-cols-2 gap-4">
                                <LabelField label="First Name" name="errFirstName" required :error="sheetFormErrors.firstName">
                                    <Input id="errFirstName" v-model="employeeFormErrors.firstName" placeholder="John" fluid :invalid="!!sheetFormErrors.firstName" />
                                </LabelField>
                                <LabelField label="Last Name" name="errLastName" required :error="sheetFormErrors.lastName">
                                    <Input id="errLastName" v-model="employeeFormErrors.lastName" placeholder="Doe" fluid :invalid="!!sheetFormErrors.lastName" />
                                </LabelField>
                            </div>
                            <LabelField label="Email" name="errEmail" required :error="sheetFormErrors.email">
                                <Input id="errEmail" v-model="employeeFormErrors.email" type="email" placeholder="john.doe@company.com" fluid :invalid="!!sheetFormErrors.email">
                                    <template #icon><Mail class="size-4" /></template>
                                </Input>
                            </LabelField>
                        </div>

                        <!-- Tab 2: Employment -->
                        <template #tab-1>
                            <div class="space-y-4">
                                <LabelField label="Department" name="errDepartment" required :error="sheetFormErrors.department">
                                    <Select id="errDepartment" v-model="employeeFormErrors.department" :options="deptOptions" placeholder="Select department" fluid :invalid="!!sheetFormErrors.department" />
                                </LabelField>
                                <LabelField label="Position" name="errPosition" required :error="sheetFormErrors.position">
                                    <Input id="errPosition" v-model="employeeFormErrors.position" placeholder="Software Engineer" fluid :invalid="!!sheetFormErrors.position" />
                                </LabelField>
                            </div>
                        </template>
                    </SheetForm>
                </CardContent>
            </Card>

            <!-- Detail View Sheet -->
            <Card>
                <CardHeader>
                    <CardTitle>Detail View</CardTitle>
                    <CardDescription>Sheet with rich content and styling</CardDescription>
                </CardHeader>
                <CardContent>
                    <Sheet v-model:open="showDetailSheet">
                        <SheetTrigger as-child>
                            <Button variant="outline">View Details</Button>
                        </SheetTrigger>
                        <SheetContent side="right" class="w-[500px] p-0">
                            <div class="bg-gradient-to-r from-indigo-500 to-purple-600 p-6 text-white">
                                <SheetTitle class="text-white text-xl">Order #12345</SheetTitle>
                                <SheetDescription class="text-white/80">Placed on December 20, 2024</SheetDescription>
                            </div>
                            <div class="p-6 space-y-4">
                                <div class="flex justify-between items-center">
                                    <span class="text-sm text-muted-foreground">Status</span>
                                    <Badge class="bg-green-500 text-white">Completed</Badge>
                                </div>
                                <div class="border-t" />
                                <div class="space-y-3">
                                    <h4 class="font-medium">Items</h4>
                                    <div class="space-y-2">
                                        <div class="flex justify-between text-sm">
                                            <span>Product A x2</span>
                                            <span>$50.00</span>
                                        </div>
                                        <div class="flex justify-between text-sm">
                                            <span>Product B x1</span>
                                            <span>$75.00</span>
                                        </div>
                                        <div class="flex justify-between text-sm">
                                            <span>Shipping</span>
                                            <span>$10.00</span>
                                        </div>
                                    </div>
                                    <div class="border-t" />
                                    <div class="flex justify-between font-medium">
                                        <span>Total</span>
                                        <span>$135.00</span>
                                    </div>
                                </div>
                            </div>
                            <SheetFooter class="border-t p-6">
                                <Button variant="outline" @click="showDetailSheet = false">Close</Button>
                                <Button>Download Invoice</Button>
                            </SheetFooter>
                        </SheetContent>
                    </Sheet>
                </CardContent>
            </Card>
        </div>
    </LayoutApp>
</template>

<script setup lang="ts">
import { ref } from 'vue';
import { AppLayout as LayoutApp } from '@/components/app';
import { Card, CardHeader, CardTitle, CardDescription, CardContent } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Select } from '@/components/ui/select-popover';
import { Checkbox } from '@/components/ui/checkbox';
import { LabelField } from '@/components/ui/label';
import { Switch } from '@/components/ui/switch';
import { Badge } from '@/components/ui/badge';
import {
    Sheet,
    SheetContent,
    SheetDescription,
    SheetFooter,
    SheetForm,
    SheetHeader,
    SheetTitle,
    SheetTrigger,
} from '@/components/ui/sheet';
import { Settings, Mail, Phone, User } from 'lucide-vue-next';
import { useShowcaseNav } from '../_composables/useShowcaseNav';

const { sidebarItems } = useShowcaseNav();

// Sheet states
const showRightSheet = ref(false);
const showLeftSheet = ref(false);
const showTopSheet = ref(false);
const showBottomSheet = ref(false);
const showFormSheet = ref(false);
const showSettingsSheet = ref(false);
const showDetailSheet = ref(false);

// Settings form state
const settingsForm = ref({
    emailNotifications: true,
    pushNotifications: false,
    marketingEmails: true,
    theme: 'system',
    language: 'en',
    profileVisible: true,
    activityStatus: false,
});

// Sheet Form states
const showSheetForm = ref(false);
const showSheetFormWithErrors = ref(false);
const sheetLoading = ref(false);
const sheetLoadingErrors = ref(false);
const sheetFormErrors = ref<Record<string, string>>({});

const employeeForm = ref({
    firstName: '',
    lastName: '',
    email: '',
    phone: '',
    dob: '',
    department: '',
    position: '',
    manager: '',
    startDate: '',
    type: '',
    notes: '',
});

const employeeFormErrors = ref({
    firstName: '',
    lastName: '',
    email: '',
    department: '',
    position: '',
});

const handleSheetSubmit = () => {
    sheetLoading.value = true;
    setTimeout(() => {
        sheetLoading.value = false;
        showSheetForm.value = false;
    }, 1500);
};

const handleSheetSubmitWithErrors = () => {
    sheetLoadingErrors.value = true;
    setTimeout(() => {
        sheetLoadingErrors.value = false;
        sheetFormErrors.value = {
            firstName: 'First name is required',
            email: 'Please enter a valid email address',
            department: 'Department is required',
        };
    }, 1000);
};

// Options
const roleOptions = [
    { label: 'Admin', value: 'admin' },
    { label: 'Editor', value: 'editor' },
    { label: 'Viewer', value: 'viewer' },
];

const deptOptions = [
    { label: 'Engineering', value: 'engineering' },
    { label: 'Design', value: 'design' },
    { label: 'Marketing', value: 'marketing' },
    { label: 'Sales', value: 'sales' },
];

const managerOptions = [
    { label: 'Sarah Johnson', value: 'sarah' },
    { label: 'Michael Chen', value: 'michael' },
    { label: 'Emily Rodriguez', value: 'emily' },
];

const employmentTypeOptions = [
    { label: 'Full-time', value: 'full-time' },
    { label: 'Part-time', value: 'part-time' },
    { label: 'Contract', value: 'contract' },
    { label: 'Intern', value: 'intern' },
];

const themeOptions = [
    { label: 'Light', value: 'light' },
    { label: 'Dark', value: 'dark' },
    { label: 'System', value: 'system' },
];

const langOptions = [
    { label: 'English', value: 'en' },
    { label: 'Spanish', value: 'es' },
    { label: 'French', value: 'fr' },
];
</script>
