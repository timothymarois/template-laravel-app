<template>
    <LayoutApp
        title="Components - Dialog & Sheet"
        pageTitle="Actions"
        :pageNavItems="sideNavItems"
        :pageTabs="actionsTabs"
    >
        <div class="space-y-6">
            <Card>
                <CardHeader>
                    <CardTitle>Dialog</CardTitle>
                    <CardDescription>Modal dialogs for focused interactions</CardDescription>
                </CardHeader>
                <CardContent class="space-y-6">
                    <div>
                        <h4 class="text-sm font-medium mb-3">Basic Dialog</h4>
                        <Button @click="showBasicDialog = true">Open Dialog</Button>
                        <Dialog v-model:visible="showBasicDialog" header="Dialog Title">
                            <p>This is a basic dialog with some content. You can put any content here.</p>
                            <template #footer>
                                <Button variant="outline" @click="showBasicDialog = false">Cancel</Button>
                                <Button @click="showBasicDialog = false">Confirm</Button>
                            </template>
                        </Dialog>
                    </div>

                    <div>
                        <h4 class="text-sm font-medium mb-3">Confirmation Dialog</h4>
                        <div class="flex gap-2">
                            <Button variant="outline" @click="showConfirmDialog = true">Show Confirmation</Button>
                            <Button variant="destructive" @click="showDestructiveDialog = true">Delete Item</Button>
                        </div>
                        <DialogConfirmation
                            v-model="showConfirmDialog"
                            title="Confirm Action"
                            message="Are you sure you want to proceed with this action?"
                            confirmLabel="Yes, Proceed"
                            @confirm="showConfirmDialog = false"
                        />
                        <DialogConfirmation
                            v-model="showDestructiveDialog"
                            title="Delete Item"
                            message="This action cannot be undone. This will permanently delete the item."
                            confirmLabel="Delete"
                            destructive
                            @confirm="showDestructiveDialog = false"
                        />
                    </div>

                    <div>
                        <h4 class="text-sm font-medium mb-3">Dialog with Form</h4>
                        <Button @click="showFormDialog = true">Add New Item</Button>
                        <Dialog v-model:visible="showFormDialog" header="Add New Item" class="max-w-md">
                            <div class="space-y-4">
                                <LabelField label="Name" name="name" required>
                                    <Input placeholder="Enter name" fluid />
                                </LabelField>
                                <LabelField label="Description" name="description">
                                    <Input placeholder="Enter description" fluid />
                                </LabelField>
                            </div>
                            <template #footer>
                                <Button variant="outline" @click="showFormDialog = false">Cancel</Button>
                                <Button @click="showFormDialog = false">Save</Button>
                            </template>
                        </Dialog>
                    </div>
                </CardContent>
            </Card>

            <Card>
                <CardHeader>
                    <CardTitle>Sheet</CardTitle>
                    <CardDescription>Sliding panels from screen edges</CardDescription>
                </CardHeader>
                <CardContent class="space-y-6">
                    <div>
                        <h4 class="text-sm font-medium mb-3">Side Positions</h4>
                        <div class="flex gap-2">
                            <Sheet v-model:open="showRightSheet">
                                <SheetTrigger as-child>
                                    <Button variant="outline">Right Sheet</Button>
                                </SheetTrigger>
                                <SheetContent side="right">
                                    <SheetHeader>
                                        <SheetTitle>Right Sheet</SheetTitle>
                                        <SheetDescription>This sheet slides in from the right.</SheetDescription>
                                    </SheetHeader>
                                    <div class="py-4">
                                        <p class="text-sm text-muted-foreground">Sheet content goes here.</p>
                                    </div>
                                </SheetContent>
                            </Sheet>

                            <Sheet v-model:open="showLeftSheet">
                                <SheetTrigger as-child>
                                    <Button variant="outline">Left Sheet</Button>
                                </SheetTrigger>
                                <SheetContent side="left">
                                    <SheetHeader>
                                        <SheetTitle>Left Sheet</SheetTitle>
                                        <SheetDescription>This sheet slides in from the left.</SheetDescription>
                                    </SheetHeader>
                                    <div class="py-4">
                                        <p class="text-sm text-muted-foreground">Sheet content goes here.</p>
                                    </div>
                                </SheetContent>
                            </Sheet>
                        </div>
                    </div>

                    <div>
                        <h4 class="text-sm font-medium mb-3">Sheet with Form</h4>
                        <Sheet v-model:open="showFormSheet">
                            <SheetTrigger as-child>
                                <Button>Add User</Button>
                            </SheetTrigger>
                            <SheetContent side="right" class="w-[400px]">
                                <SheetHeader>
                                    <SheetTitle>Add User</SheetTitle>
                                    <SheetDescription>Create a new user account</SheetDescription>
                                </SheetHeader>
                                <div class="py-4 space-y-4">
                                    <LabelField label="Name" name="userName" required>
                                        <Input placeholder="John Doe" fluid />
                                    </LabelField>
                                    <LabelField label="Email" name="userEmail" required>
                                        <Input type="email" placeholder="john@example.com" fluid />
                                    </LabelField>
                                    <LabelField label="Role" name="userRole">
                                        <Select :options="roleOptions" placeholder="Select role" />
                                    </LabelField>
                                </div>
                                <SheetFooter>
                                    <Button variant="outline" @click="showFormSheet = false">Cancel</Button>
                                    <Button @click="showFormSheet = false">Create User</Button>
                                </SheetFooter>
                            </SheetContent>
                        </Sheet>
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
import { Input, Select, LabelField } from '@/components/ui/form';
import { Dialog, DialogConfirmation } from '@/components/ui/dialog';
import {
    Sheet,
    SheetContent,
    SheetDescription,
    SheetFooter,
    SheetHeader,
    SheetTitle,
    SheetTrigger,
} from '@/components/ui/sheet';
import { useShowcaseNav } from '../_composables/useShowcaseNav';

const { sideNavItems, actionsTabs } = useShowcaseNav();

const showBasicDialog = ref(false);
const showConfirmDialog = ref(false);
const showDestructiveDialog = ref(false);
const showFormDialog = ref(false);
const showRightSheet = ref(false);
const showLeftSheet = ref(false);
const showFormSheet = ref(false);

const roleOptions = [
    { label: 'Admin', value: 'admin' },
    { label: 'User', value: 'user' },
];
</script>
