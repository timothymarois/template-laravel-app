<template>
    <LayoutApp
        title="Components - Dialog & Sheet"
        pageTitle="Actions"
        :pageNavItems="sideNavItems"
        :pageTabs="actionsTabs"
    >
        <div class="space-y-4">
            <!-- Dialog Examples -->
            <Card>
                <CardHeader>
                    <CardTitle>Dialog</CardTitle>
                    <CardDescription>Modal dialogs for focused interactions</CardDescription>
                </CardHeader>
                <CardContent class="space-y-4">
                    <!-- Basic Dialog -->
                    <div>
                        <h4 class="text-sm font-medium mb-3">Basic Dialog</h4>
                        <Button @click="showBasicDialog = true">Open Dialog</Button>
                        <Dialog v-model:visible="showBasicDialog" header="Dialog Title">
                            <p class="text-muted-foreground">This is a basic dialog with some content. You can put any content here.</p>
                            <template #footer>
                                <Button variant="outline" @click="showBasicDialog = false">Cancel</Button>
                                <Button @click="showBasicDialog = false">Confirm</Button>
                            </template>
                        </Dialog>
                    </div>

                    <!-- Confirmation Dialogs -->
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

                    <!-- Draggable Dialog -->
                    <div>
                        <h4 class="text-sm font-medium mb-3">Draggable Dialog</h4>
                        <Button @click="showDraggableDialog = true">Open Draggable Dialog</Button>
                        <DialogBase v-model:open="showDraggableDialog" :modal="false">
                            <DialogContent draggable class="max-w-md">
                                <DialogHeader>
                                    <DialogTitle>Draggable Dialog</DialogTitle>
                                    <DialogDescription>Drag the header to move this dialog around.</DialogDescription>
                                </DialogHeader>
                                <div class="py-4">
                                    <p class="text-sm text-muted-foreground">This dialog can be dragged by its header. Try clicking and dragging the title area to reposition the dialog on the screen.</p>
                                </div>
                                <DialogFooter>
                                    <Button variant="outline" @click="showDraggableDialog = false">Cancel</Button>
                                    <Button @click="showDraggableDialog = false">Confirm</Button>
                                </DialogFooter>
                            </DialogContent>
                        </DialogBase>
                    </div>

                    <!-- Form Dialog -->
                    <div>
                        <h4 class="text-sm font-medium mb-3">Form Dialog</h4>
                        <Button @click="showFormDialog = true">Add New Item</Button>
                        <DialogBase v-model:open="showFormDialog">
                            <DialogContent class="p-0 gap-0 max-w-md">
                                <DialogHeader class="p-6 border-b">
                                    <DialogTitle>Add New Item</DialogTitle>
                                </DialogHeader>
                                <div class="p-6 space-y-4">
                                    <LabelField label="Name" name="itemName" required>
                                        <Input id="itemName" placeholder="Enter name" fluid />
                                    </LabelField>
                                    <LabelField label="Description" name="itemDescription">
                                        <Input id="itemDescription" placeholder="Enter description" fluid />
                                    </LabelField>
                                    <LabelField label="Category" name="itemCategory">
                                        <Select id="itemCategory" :options="categoryOptions" placeholder="Select category" fluid />
                                    </LabelField>
                                </div>
                                <DialogFooter class="p-6 border-t">
                                    <Button variant="outline" @click="showFormDialog = false">Cancel</Button>
                                    <Button @click="showFormDialog = false">Save Item</Button>
                                </DialogFooter>
                            </DialogContent>
                        </DialogBase>
                    </div>

                    <!-- Form Dialog with Errors -->
                    <div>
                        <h4 class="text-sm font-medium mb-3">Form Dialog with Validation</h4>
                        <Button @click="showErrorFormDialog = true">Open Form with Validation</Button>
                        <DialogBase v-model:open="showErrorFormDialog">
                            <DialogContent class="p-0 gap-0 max-w-md">
                                <DialogHeader class="p-6 border-b">
                                    <DialogTitle>Create Project</DialogTitle>
                                    <DialogDescription>Fill in the project details below.</DialogDescription>
                                </DialogHeader>
                                <div class="p-6 space-y-4">
                                    <LabelField label="Project Name" name="projectName" required :error="formErrors.projectName">
                                        <Input id="projectName" v-model="projectForm.name" placeholder="Enter project name" fluid :invalid="!!formErrors.projectName" />
                                    </LabelField>
                                    <LabelField label="Description" name="projectDesc">
                                        <Input id="projectDesc" v-model="projectForm.description" placeholder="Enter description" fluid />
                                    </LabelField>
                                    <LabelField label="Team Lead" name="teamLead" required :error="formErrors.teamLead">
                                        <Select id="teamLead" v-model="projectForm.teamLead" :options="teamLeadOptions" placeholder="Select team lead" fluid :invalid="!!formErrors.teamLead" />
                                    </LabelField>
                                </div>
                                <DialogFooter class="p-6 border-t sm:flex-col sm:items-stretch gap-4">
                                    <Errors
                                        v-if="Object.keys(formErrors).length > 0"
                                        :errors="Object.values(formErrors)"
                                        title="Please fix the following errors"
                                    />
                                    <div class="flex gap-2 justify-end">
                                        <Button variant="outline" @click="closeErrorFormDialog">Cancel</Button>
                                        <Button @click="submitErrorForm">Create Project</Button>
                                    </div>
                                </DialogFooter>
                            </DialogContent>
                        </DialogBase>
                    </div>

                    <!-- Gradient Header Dialog -->
                    <div>
                        <h4 class="text-sm font-medium mb-3">Gradient Header Dialog</h4>
                        <div class="flex gap-2">
                            <Button variant="outline" @click="showGradientDialog = true">Primary Gradient</Button>
                            <Button variant="outline" @click="showGradientDialog2 = true">Blue Gradient</Button>
                            <Button variant="outline" @click="showGradientDialog3 = true">Purple Gradient</Button>
                        </div>

                        <!-- Primary Gradient -->
                        <DialogBase v-model:open="showGradientDialog">
                            <DialogContent class="p-0 gap-0 border-0 overflow-hidden max-w-md [&>button]:hidden">
                                <DialogHeader class="bg-gradient-to-r from-primary to-primary/70 text-primary-foreground p-6 relative">
                                    <DialogClose class="absolute right-4 top-4 size-8 rounded-full flex items-center justify-center bg-white/20 hover:bg-white/40 transition-colors cursor-pointer">
                                        <X class="size-4 text-white" />
                                    </DialogClose>
                                    <DialogTitle class="text-primary-foreground">Upgrade to Pro</DialogTitle>
                                    <DialogDescription class="text-primary-foreground/80">
                                        Unlock all features and get unlimited access.
                                    </DialogDescription>
                                </DialogHeader>
                                <div class="p-6 space-y-4">
                                    <div class="flex items-center gap-3">
                                        <Check class="size-5 text-green-500" />
                                        <span class="text-sm">Unlimited projects</span>
                                    </div>
                                    <div class="flex items-center gap-3">
                                        <Check class="size-5 text-green-500" />
                                        <span class="text-sm">Priority support</span>
                                    </div>
                                    <div class="flex items-center gap-3">
                                        <Check class="size-5 text-green-500" />
                                        <span class="text-sm">Advanced analytics</span>
                                    </div>
                                </div>
                                <DialogFooter class="border-t p-6">
                                    <Button variant="outline" @click="showGradientDialog = false">Maybe Later</Button>
                                    <Button @click="showGradientDialog = false">Upgrade Now</Button>
                                </DialogFooter>
                            </DialogContent>
                        </DialogBase>

                        <!-- Blue Gradient -->
                        <DialogBase v-model:open="showGradientDialog2">
                            <DialogContent class="p-0 gap-0 border-0 overflow-hidden max-w-md [&>button]:hidden">
                                <DialogHeader class="bg-gradient-to-r from-blue-600 to-cyan-500 text-white p-6 relative">
                                    <DialogClose class="absolute right-4 top-4 size-8 rounded-full flex items-center justify-center bg-white/20 hover:bg-white/40 transition-colors cursor-pointer">
                                        <X class="size-4 text-white" />
                                    </DialogClose>
                                    <DialogTitle class="text-white">Welcome Aboard!</DialogTitle>
                                    <DialogDescription class="text-white/80">
                                        Let's get you started with a quick tour.
                                    </DialogDescription>
                                </DialogHeader>
                                <div class="p-6">
                                    <p class="text-sm text-muted-foreground">We're excited to have you here. This quick tour will help you get familiar with the key features.</p>
                                </div>
                                <DialogFooter class="border-t p-6">
                                    <Button variant="outline" @click="showGradientDialog2 = false">Skip Tour</Button>
                                    <Button @click="showGradientDialog2 = false">Start Tour</Button>
                                </DialogFooter>
                            </DialogContent>
                        </DialogBase>

                        <!-- Purple Gradient -->
                        <DialogBase v-model:open="showGradientDialog3">
                            <DialogContent class="p-0 gap-0 border-0 overflow-hidden max-w-md [&>button]:hidden">
                                <DialogHeader class="bg-gradient-to-r from-purple-600 to-pink-500 text-white p-6 relative">
                                    <DialogClose class="absolute right-4 top-4 size-8 rounded-full flex items-center justify-center bg-white/20 hover:bg-white/40 transition-colors cursor-pointer">
                                        <X class="size-4 text-white" />
                                    </DialogClose>
                                    <DialogTitle class="text-white">New Feature Available</DialogTitle>
                                </DialogHeader>
                                <div class="p-6">
                                    <p class="text-sm text-muted-foreground">We've added some exciting new features based on your feedback. Take a moment to explore what's new.</p>
                                </div>
                                <DialogFooter class="border-t p-6">
                                    <Button variant="outline" @click="showGradientDialog3 = false">Dismiss</Button>
                                    <Button @click="showGradientDialog3 = false">Explore</Button>
                                </DialogFooter>
                            </DialogContent>
                        </DialogBase>
                    </div>

                    <!-- Scrollable Content Dialog -->
                    <div>
                        <h4 class="text-sm font-medium mb-3">Scrollable Content Dialog</h4>
                        <Button variant="outline" @click="showScrollDialog = true">Open Large Dialog</Button>
                        <DialogBase v-model:open="showScrollDialog">
                            <DialogContent class="p-0 gap-0 max-w-2xl">
                                <DialogHeader class="p-6 border-b">
                                    <DialogTitle>Terms of Service</DialogTitle>
                                    <DialogDescription>Please read and accept our terms of service.</DialogDescription>
                                </DialogHeader>
                                <div class="p-6 space-y-4 max-h-[50vh] overflow-y-auto">
                                    <p class="text-sm text-muted-foreground">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.</p>
                                    <p class="text-sm text-muted-foreground">Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.</p>
                                    <p class="text-sm text-muted-foreground">Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium, totam rem aperiam, eaque ipsa quae ab illo inventore veritatis et quasi architecto beatae vitae dicta sunt explicabo.</p>
                                    <p class="text-sm text-muted-foreground">Nemo enim ipsam voluptatem quia voluptas sit aspernatur aut odit aut fugit, sed quia consequuntur magni dolores eos qui ratione voluptatem sequi nesciunt. Neque porro quisquam est, qui dolorem ipsum quia dolor sit amet.</p>
                                    <p class="text-sm text-muted-foreground">At vero eos et accusamus et iusto odio dignissimos ducimus qui blanditiis praesentium voluptatum deleniti atque corrupti quos dolores et quas molestias excepturi sint occaecati cupiditate non provident.</p>
                                    <p class="text-sm text-muted-foreground">Similique sunt in culpa qui officia deserunt mollitia animi, id est laborum et dolorum fuga. Et harum quidem rerum facilis est et expedita distinctio. Nam libero tempore, cum soluta nobis est eligendi optio.</p>
                                    <p class="text-sm text-muted-foreground">Temporibus autem quibusdam et aut officiis debitis aut rerum necessitatibus saepe eveniet ut et voluptates repudiandae sint et molestiae non recusandae. Itaque earum rerum hic tenetur a sapiente delectus.</p>
                                    <p class="text-sm text-muted-foreground">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris.</p>
                                </div>
                                <DialogFooter class="p-6 border-t">
                                    <Button variant="outline" @click="showScrollDialog = false">Decline</Button>
                                    <Button @click="showScrollDialog = false">Accept Terms</Button>
                                </DialogFooter>
                            </DialogContent>
                        </DialogBase>
                    </div>

                    <!-- Large Scrollable Page Dialog -->
                    <div>
                        <h4 class="text-sm font-medium mb-3">Large Dialog (Page Scrolls)</h4>
                        <Button variant="outline" @click="showLargeDialog = true">Open Large Dialog</Button>
                        <DialogBase v-model:open="showLargeDialog">
                            <DialogScrollContent class="p-0 gap-0 max-w-2xl">
                                <DialogHeader class="p-6 border-b">
                                    <DialogTitle>Privacy Policy</DialogTitle>
                                    <DialogDescription>Last updated: December 2024</DialogDescription>
                                </DialogHeader>
                                <div class="p-6 space-y-4">
                                    <h3 class="font-semibold">1. Introduction</h3>
                                    <p class="text-sm text-muted-foreground">Welcome to our Privacy Policy. This document explains how we collect, use, disclose, and safeguard your information when you visit our website or use our services. Please read this privacy policy carefully. If you do not agree with the terms of this privacy policy, please do not access the site.</p>

                                    <h3 class="font-semibold">2. Information We Collect</h3>
                                    <p class="text-sm text-muted-foreground">We may collect information about you in a variety of ways. The information we may collect on the Site includes personal data, such as your name, shipping address, email address, and telephone number, and demographic information, such as your age, gender, hometown, and interests.</p>

                                    <h3 class="font-semibold">3. Use of Your Information</h3>
                                    <p class="text-sm text-muted-foreground">Having accurate information about you permits us to provide you with a smooth, efficient, and customized experience. Specifically, we may use information collected about you via the Site to create and manage your account, process transactions, send you emails regarding your account, and respond to inquiries.</p>

                                    <h3 class="font-semibold">4. Disclosure of Your Information</h3>
                                    <p class="text-sm text-muted-foreground">We may share information we have collected about you in certain situations. Your information may be disclosed as follows: by law or to protect rights, third-party service providers, marketing communications, and business transfers.</p>

                                    <h3 class="font-semibold">5. Security of Your Information</h3>
                                    <p class="text-sm text-muted-foreground">We use administrative, technical, and physical security measures to help protect your personal information. While we have taken reasonable steps to secure the personal information you provide to us, please be aware that despite our efforts, no security measures are perfect or impenetrable.</p>

                                    <h3 class="font-semibold">6. Policy for Children</h3>
                                    <p class="text-sm text-muted-foreground">We do not knowingly solicit information from or market to children under the age of 13. If we learn that we have collected personal information from a child under age 13 without verification of parental consent, we will delete that information as quickly as possible.</p>

                                    <h3 class="font-semibold">7. Controls for Do-Not-Track Features</h3>
                                    <p class="text-sm text-muted-foreground">Most web browsers and some mobile operating systems include a Do-Not-Track feature or setting you can activate to signal your privacy preference not to have data about your online browsing activities monitored and collected.</p>

                                    <h3 class="font-semibold">8. Options Regarding Your Information</h3>
                                    <p class="text-sm text-muted-foreground">You may at any time review or change the information in your account or terminate your account by logging into your account settings and updating your account, contacting us using the contact information provided, or noting that upon your request to terminate your account, we will deactivate or delete your account.</p>

                                    <h3 class="font-semibold">9. California Privacy Rights</h3>
                                    <p class="text-sm text-muted-foreground">California Civil Code Section 1798.83 permits our users who are California residents to request certain information regarding our disclosure of personal information to third parties for their direct marketing purposes.</p>

                                    <h3 class="font-semibold">10. Contact Us</h3>
                                    <p class="text-sm text-muted-foreground">If you have questions or comments about this Privacy Policy, please contact us at privacy@example.com or by mail at 123 Privacy Street, Suite 100, San Francisco, CA 94102.</p>
                                </div>
                                <DialogFooter class="p-6 border-t">
                                    <Button variant="outline" @click="showLargeDialog = false">Close</Button>
                                    <Button @click="showLargeDialog = false">I Agree</Button>
                                </DialogFooter>
                            </DialogScrollContent>
                        </DialogBase>
                    </div>

                    <!-- Alert Style Dialog -->
                    <div>
                        <h4 class="text-sm font-medium mb-3">Alert Dialogs</h4>
                        <div class="flex gap-2">
                            <Button variant="outline" @click="showInfoDialog = true">
                                <Info class="size-4 mr-2" />
                                Info
                            </Button>
                            <Button variant="outline" @click="showWarningDialog = true">
                                <AlertTriangle class="size-4 mr-2" />
                                Warning
                            </Button>
                            <Button variant="outline" @click="showSuccessDialog = true">
                                <CheckCircle class="size-4 mr-2" />
                                Success
                            </Button>
                        </div>

                        <!-- Info Dialog -->
                        <DialogBase v-model:open="showInfoDialog">
                            <DialogContent class="max-w-sm">
                                <div class="flex flex-col items-center text-center gap-4 py-4">
                                    <div class="p-3 bg-blue-100 dark:bg-blue-900/30 rounded-full">
                                        <Info class="size-6 text-blue-600 dark:text-blue-400" />
                                    </div>
                                    <div class="space-y-2">
                                        <DialogTitle>Information</DialogTitle>
                                        <DialogDescription>This is an informational message to help guide you through the process.</DialogDescription>
                                    </div>
                                </div>
                                <DialogFooter class="sm:justify-center">
                                    <Button @click="showInfoDialog = false">Got it</Button>
                                </DialogFooter>
                            </DialogContent>
                        </DialogBase>

                        <!-- Warning Dialog -->
                        <DialogBase v-model:open="showWarningDialog">
                            <DialogContent class="max-w-sm">
                                <div class="flex flex-col items-center text-center gap-4 py-4">
                                    <div class="p-3 bg-yellow-100 dark:bg-yellow-900/30 rounded-full">
                                        <AlertTriangle class="size-6 text-yellow-600 dark:text-yellow-400" />
                                    </div>
                                    <div class="space-y-2">
                                        <DialogTitle>Warning</DialogTitle>
                                        <DialogDescription>This action may have unintended consequences. Please proceed with caution.</DialogDescription>
                                    </div>
                                </div>
                                <DialogFooter class="sm:justify-center">
                                    <Button variant="outline" @click="showWarningDialog = false">Cancel</Button>
                                    <Button @click="showWarningDialog = false">Continue</Button>
                                </DialogFooter>
                            </DialogContent>
                        </DialogBase>

                        <!-- Success Dialog -->
                        <DialogBase v-model:open="showSuccessDialog">
                            <DialogContent class="max-w-sm">
                                <div class="flex flex-col items-center text-center gap-4 py-4">
                                    <div class="p-3 bg-green-100 dark:bg-green-900/30 rounded-full">
                                        <CheckCircle class="size-6 text-green-600 dark:text-green-400" />
                                    </div>
                                    <div class="space-y-2">
                                        <DialogTitle>Success!</DialogTitle>
                                        <DialogDescription>Your changes have been saved successfully.</DialogDescription>
                                    </div>
                                </div>
                                <DialogFooter class="sm:justify-center">
                                    <Button @click="showSuccessDialog = false">Done</Button>
                                </DialogFooter>
                            </DialogContent>
                        </DialogBase>
                    </div>
                </CardContent>
            </Card>

            <!-- Sheet Examples -->
            <Card>
                <CardHeader>
                    <CardTitle>Sheet</CardTitle>
                    <CardDescription>Sliding panels from screen edges</CardDescription>
                </CardHeader>
                <CardContent class="space-y-4">
                    <!-- Side Positions -->
                    <div>
                        <h4 class="text-sm font-medium mb-3">Side Positions</h4>
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
                    </div>

                    <!-- Sheet with Form -->
                    <div>
                        <h4 class="text-sm font-medium mb-3">Sheet with Form</h4>
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
                    </div>

                    <!-- Settings Sheet -->
                    <div>
                        <h4 class="text-sm font-medium mb-3">Settings Panel</h4>
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
                                            <label class="flex items-start justify-between gap-4 cursor-pointer">
                                                <div>
                                                    <div class="text-sm font-medium">Email notifications</div>
                                                    <div class="text-xs text-muted-foreground">Receive updates about your account via email.</div>
                                                </div>
                                                <Checkbox v-model="settingsForm.emailNotifications" class="mt-0.5" />
                                            </label>
                                            <label class="flex items-start justify-between gap-4 cursor-pointer">
                                                <div>
                                                    <div class="text-sm font-medium">Push notifications</div>
                                                    <div class="text-xs text-muted-foreground">Get instant alerts on your device.</div>
                                                </div>
                                                <Checkbox v-model="settingsForm.pushNotifications" class="mt-0.5" />
                                            </label>
                                            <label class="flex items-start justify-between gap-4 cursor-pointer">
                                                <div>
                                                    <div class="text-sm font-medium">Marketing emails</div>
                                                    <div class="text-xs text-muted-foreground">Receive tips, product updates and more.</div>
                                                </div>
                                                <Checkbox v-model="settingsForm.marketingEmails" class="mt-0.5" />
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
                    </div>

                    <!-- Drawer Form with Tabs -->
                    <div>
                        <h4 class="text-sm font-medium mb-3">Drawer Form with Tabs</h4>
                        <div class="flex gap-2">
                            <Button @click="showDrawerForm = true">Add Employee</Button>
                            <Button variant="outline" @click="showDrawerFormWithErrors = true">With Validation Errors</Button>
                        </div>

                        <!-- Basic Drawer Form -->
                        <DrawerForm
                            v-model="showDrawerForm"
                            title="Add Employee"
                            :tabs="[{ title: 'Personal Info' }, { title: 'Employment' }, { title: 'Documents', disabled: true }]"
                            position="right"
                            width="550px"
                            :loading="drawerLoading"
                            @submit="handleDrawerSubmit"
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
                        </DrawerForm>

                        <!-- Drawer Form with Errors -->
                        <DrawerForm
                            v-model="showDrawerFormWithErrors"
                            title="Add Employee (With Errors)"
                            :tabs="[{ title: 'Personal Info' }, { title: 'Employment' }]"
                            position="right"
                            width="550px"
                            :loading="drawerLoadingErrors"
                            :errors="drawerFormErrors"
                            @submit="handleDrawerSubmitWithErrors"
                        >
                            <!-- Tab 1: Personal Info -->
                            <div class="space-y-4">
                                <div class="grid grid-cols-2 gap-4">
                                    <LabelField label="First Name" name="errFirstName" required :error="drawerFormErrors.firstName">
                                        <Input id="errFirstName" v-model="employeeFormErrors.firstName" placeholder="John" fluid :invalid="!!drawerFormErrors.firstName" />
                                    </LabelField>
                                    <LabelField label="Last Name" name="errLastName" required :error="drawerFormErrors.lastName">
                                        <Input id="errLastName" v-model="employeeFormErrors.lastName" placeholder="Doe" fluid :invalid="!!drawerFormErrors.lastName" />
                                    </LabelField>
                                </div>
                                <LabelField label="Email" name="errEmail" required :error="drawerFormErrors.email">
                                    <Input id="errEmail" v-model="employeeFormErrors.email" type="email" placeholder="john.doe@company.com" fluid :invalid="!!drawerFormErrors.email">
                                        <template #icon><Mail class="size-4" /></template>
                                    </Input>
                                </LabelField>
                            </div>

                            <!-- Tab 2: Employment -->
                            <template #tab-1>
                                <div class="space-y-4">
                                    <LabelField label="Department" name="errDepartment" required :error="drawerFormErrors.department">
                                        <Select id="errDepartment" v-model="employeeFormErrors.department" :options="deptOptions" placeholder="Select department" fluid :invalid="!!drawerFormErrors.department" />
                                    </LabelField>
                                    <LabelField label="Position" name="errPosition" required :error="drawerFormErrors.position">
                                        <Input id="errPosition" v-model="employeeFormErrors.position" placeholder="Software Engineer" fluid :invalid="!!drawerFormErrors.position" />
                                    </LabelField>
                                </div>
                            </template>
                        </DrawerForm>
                    </div>

                    <!-- Detail View Sheet -->
                    <div>
                        <h4 class="text-sm font-medium mb-3">Detail View</h4>
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
import { Input, Select, LabelField, Checkbox, Errors } from '@/components/ui/form';
import { Badge } from '@/components/ui/badge';
import {
    Dialog,
    DialogBase,
    DialogClose,
    DialogConfirmation,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogScrollContent,
    DialogTitle,
} from '@/components/ui/dialog';
import {
    Sheet,
    SheetContent,
    SheetDescription,
    SheetFooter,
    SheetHeader,
    SheetTitle,
    SheetTrigger,
} from '@/components/ui/sheet';
import { DrawerForm } from '@/components/ui/drawer';
import { Check, Info, AlertTriangle, CheckCircle, Settings, X, Mail, Phone, User } from 'lucide-vue-next';
import { useShowcaseNav } from '../_composables/useShowcaseNav';

const { sideNavItems, actionsTabs } = useShowcaseNav();

// Dialog states
const showBasicDialog = ref(false);
const showConfirmDialog = ref(false);
const showDestructiveDialog = ref(false);
const showDraggableDialog = ref(false);
const showFormDialog = ref(false);
const showErrorFormDialog = ref(false);

// Form with validation state
const projectForm = ref({
    name: '',
    description: '',
    teamLead: '',
});
const formErrors = ref<Record<string, string>>({});

const teamLeadOptions = [
    { label: 'John Doe', value: 'john' },
    { label: 'Jane Smith', value: 'jane' },
    { label: 'Bob Wilson', value: 'bob' },
];

const submitErrorForm = () => {
    formErrors.value = {};

    if (!projectForm.value.name) {
        formErrors.value.projectName = 'Project name is required';
    }
    if (!projectForm.value.teamLead) {
        formErrors.value.teamLead = 'Team lead is required';
    }

    if (Object.keys(formErrors.value).length === 0) {
        showErrorFormDialog.value = false;
        projectForm.value = { name: '', description: '', teamLead: '' };
    }
};

const closeErrorFormDialog = () => {
    showErrorFormDialog.value = false;
    formErrors.value = {};
    projectForm.value = { name: '', description: '', teamLead: '' };
};
const showGradientDialog = ref(false);
const showGradientDialog2 = ref(false);
const showGradientDialog3 = ref(false);
const showScrollDialog = ref(false);
const showLargeDialog = ref(false);
const showInfoDialog = ref(false);
const showWarningDialog = ref(false);
const showSuccessDialog = ref(false);

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

// Drawer Form states
const showDrawerForm = ref(false);
const showDrawerFormWithErrors = ref(false);
const drawerLoading = ref(false);
const drawerLoadingErrors = ref(false);
const drawerFormErrors = ref<Record<string, string>>({});

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

const handleDrawerSubmit = () => {
    drawerLoading.value = true;
    setTimeout(() => {
        drawerLoading.value = false;
        showDrawerForm.value = false;
    }, 1500);
};

const handleDrawerSubmitWithErrors = () => {
    drawerLoadingErrors.value = true;
    setTimeout(() => {
        drawerLoadingErrors.value = false;
        drawerFormErrors.value = {
            firstName: 'First name is required',
            email: 'Please enter a valid email address',
            department: 'Department is required',
        };
    }, 1000);
};

// Options
const categoryOptions = [
    { label: 'Electronics', value: 'electronics' },
    { label: 'Clothing', value: 'clothing' },
    { label: 'Books', value: 'books' },
];

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
