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
                                    <LabelField label="Name" name="name" required>
                                        <Input placeholder="Enter name" fluid />
                                    </LabelField>
                                    <LabelField label="Description" name="description">
                                        <Input placeholder="Enter description" fluid />
                                    </LabelField>
                                    <LabelField label="Category" name="category">
                                        <Select :options="categoryOptions" placeholder="Select category" />
                                    </LabelField>
                                </div>
                                <DialogFooter class="p-6 border-t">
                                    <Button variant="outline" @click="showFormDialog = false">Cancel</Button>
                                    <Button @click="showFormDialog = false">Save Item</Button>
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
                            <SheetContent side="right" class="w-[400px] sm:w-[540px]">
                                <SheetHeader>
                                    <SheetTitle>Add User</SheetTitle>
                                    <SheetDescription>Create a new user account. Fill in all the required fields.</SheetDescription>
                                </SheetHeader>
                                <div class="py-6 space-y-4">
                                    <LabelField label="Full Name" name="userName" required>
                                        <Input placeholder="John Doe" fluid />
                                    </LabelField>
                                    <LabelField label="Email Address" name="userEmail" required>
                                        <Input type="email" placeholder="john@example.com" fluid />
                                    </LabelField>
                                    <LabelField label="Role" name="userRole" required>
                                        <Select :options="roleOptions" placeholder="Select role" />
                                    </LabelField>
                                    <LabelField label="Department" name="userDept">
                                        <Select :options="deptOptions" placeholder="Select department" />
                                    </LabelField>
                                    <LabelField label="Notes" name="userNotes">
                                        <Input placeholder="Any additional notes..." fluid />
                                    </LabelField>
                                </div>
                                <SheetFooter>
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
                            <SheetContent side="right" class="w-[400px]">
                                <SheetHeader>
                                    <SheetTitle>Settings</SheetTitle>
                                    <SheetDescription>Manage your preferences and account settings.</SheetDescription>
                                </SheetHeader>
                                <div class="py-6 space-y-4">
                                    <div class="space-y-4">
                                        <h4 class="text-sm font-medium">Notifications</h4>
                                        <div class="space-y-3">
                                            <label class="flex items-center justify-between">
                                                <span class="text-sm">Email notifications</span>
                                                <Checkbox :checked="true" />
                                            </label>
                                            <label class="flex items-center justify-between">
                                                <span class="text-sm">Push notifications</span>
                                                <Checkbox :checked="false" />
                                            </label>
                                            <label class="flex items-center justify-between">
                                                <span class="text-sm">Weekly digest</span>
                                                <Checkbox :checked="true" />
                                            </label>
                                        </div>
                                    </div>
                                    <div class="border-t" />
                                    <div class="space-y-4">
                                        <h4 class="text-sm font-medium">Appearance</h4>
                                        <LabelField label="Theme" name="theme">
                                            <Select :options="themeOptions" model-value="system" />
                                        </LabelField>
                                        <LabelField label="Language" name="language">
                                            <Select :options="langOptions" model-value="en" />
                                        </LabelField>
                                    </div>
                                </div>
                                <SheetFooter>
                                    <Button variant="outline" @click="showSettingsSheet = false">Cancel</Button>
                                    <Button @click="showSettingsSheet = false">Save Changes</Button>
                                </SheetFooter>
                            </SheetContent>
                        </Sheet>
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
import { Input, Select, LabelField, Checkbox } from '@/components/ui/form';
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
import { Check, Info, AlertTriangle, CheckCircle, Settings, X } from 'lucide-vue-next';
import { useShowcaseNav } from '../_composables/useShowcaseNav';

const { sideNavItems, actionsTabs } = useShowcaseNav();

// Dialog states
const showBasicDialog = ref(false);
const showConfirmDialog = ref(false);
const showDestructiveDialog = ref(false);
const showDraggableDialog = ref(false);
const showFormDialog = ref(false);
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
