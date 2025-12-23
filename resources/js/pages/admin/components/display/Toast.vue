<template>
    <LayoutApp
        title="Components - Toast"
        pageTitle="Toast"
        :pageSidebarItems="sidebarItems"
        pageSidebarTitle="Components"
    >
        <div class="space-y-4">
            <!-- Basic Toasts -->
            <Card>
                <CardHeader>
                    <CardTitle>Basic Toasts</CardTitle>
                    <CardDescription>Simple notification toasts for user feedback</CardDescription>
                </CardHeader>
                <CardContent>
                    <div class="flex gap-2 flex-wrap">
                        <Button variant="outline" @click="showDefault">Default</Button>
                        <Button variant="outline" @click="showDescription">With Description</Button>
                        <Button variant="outline" @click="showLong">Long Message</Button>
                    </div>
                </CardContent>
            </Card>

            <!-- Toast Types -->
            <Card>
                <CardHeader>
                    <CardTitle>Toast Types</CardTitle>
                    <CardDescription>Different toast variants for various contexts</CardDescription>
                </CardHeader>
                <CardContent>
                    <div class="flex gap-2 flex-wrap">
                        <Button variant="outline" @click="showSuccess">
                            <CheckCircle class="size-4 mr-2 text-green-500" />
                            Success
                        </Button>
                        <Button variant="outline" @click="showError">
                            <XCircle class="size-4 mr-2 text-destructive" />
                            Error
                        </Button>
                        <Button variant="outline" @click="showWarning">
                            <AlertTriangle class="size-4 mr-2 text-yellow-500" />
                            Warning
                        </Button>
                        <Button variant="outline" @click="showInfo">
                            <Info class="size-4 mr-2 text-blue-500" />
                            Info
                        </Button>
                    </div>
                </CardContent>
            </Card>

            <!-- Toast with Actions -->
            <Card>
                <CardHeader>
                    <CardTitle>Toast with Actions</CardTitle>
                    <CardDescription>Interactive toasts with action buttons</CardDescription>
                </CardHeader>
                <CardContent>
                    <div class="flex gap-2 flex-wrap">
                        <Button variant="outline" @click="showWithAction">With Action</Button>
                        <Button variant="outline" @click="showWithCancel">With Cancel</Button>
                        <Button variant="outline" @click="showWithBothActions">Action & Cancel</Button>
                    </div>
                </CardContent>
            </Card>

            <!-- Promise Toast -->
            <Card>
                <CardHeader>
                    <CardTitle>Promise Toast</CardTitle>
                    <CardDescription>Loading states that resolve to success or error</CardDescription>
                </CardHeader>
                <CardContent>
                    <div class="flex gap-2 flex-wrap">
                        <Button variant="outline" @click="showPromiseSuccess">
                            <Loader2 class="size-4 mr-2" />
                            Promise (Success)
                        </Button>
                        <Button variant="outline" @click="showPromiseError">
                            <Loader2 class="size-4 mr-2" />
                            Promise (Error)
                        </Button>
                        <Button variant="outline" @click="showSaveData">
                            <Save class="size-4 mr-2" />
                            Save Data
                        </Button>
                    </div>
                </CardContent>
            </Card>

            <!-- Toast Configuration -->
            <Card>
                <CardHeader>
                    <CardTitle>Toast Configuration</CardTitle>
                    <CardDescription>Toasts are configured globally via the Toaster component</CardDescription>
                </CardHeader>
                <CardContent class="space-y-4">
                    <div class="p-4 bg-muted rounded-lg space-y-2">
                        <p class="text-sm font-medium">Current Configuration</p>
                        <code class="text-xs bg-background px-2 py-1 rounded block">&lt;Toaster position="bottom-left" /&gt;</code>
                    </div>
                    <div class="text-sm text-muted-foreground">
                        <p class="mb-2">Available positions:</p>
                        <div class="grid grid-cols-3 gap-2 text-xs font-mono">
                            <span class="bg-muted px-2 py-1 rounded text-center">top-left</span>
                            <span class="bg-muted px-2 py-1 rounded text-center">top-center</span>
                            <span class="bg-muted px-2 py-1 rounded text-center">top-right</span>
                            <span class="bg-primary/10 text-primary px-2 py-1 rounded text-center border border-primary/20">bottom-left</span>
                            <span class="bg-muted px-2 py-1 rounded text-center">bottom-center</span>
                            <span class="bg-muted px-2 py-1 rounded text-center">bottom-right</span>
                        </div>
                    </div>
                    <p class="text-xs text-muted-foreground">Position is set on the <code class="bg-muted px-1 py-0.5 rounded">&lt;Toaster&gt;</code> component in your app layout, not per-toast.</p>
                </CardContent>
            </Card>

            <!-- Custom Duration -->
            <Card>
                <CardHeader>
                    <CardTitle>Custom Duration</CardTitle>
                    <CardDescription>Control how long toasts stay visible</CardDescription>
                </CardHeader>
                <CardContent>
                    <div class="flex gap-2 flex-wrap">
                        <Button variant="outline" @click="showDuration(1000)">1 second</Button>
                        <Button variant="outline" @click="showDuration(3000)">3 seconds</Button>
                        <Button variant="outline" @click="showDuration(5000)">5 seconds</Button>
                        <Button variant="outline" @click="showDuration(10000)">10 seconds</Button>
                        <Button variant="outline" @click="showInfinite">Infinite (dismiss manually)</Button>
                    </div>
                </CardContent>
            </Card>

            <!-- Rich Content -->
            <Card>
                <CardHeader>
                    <CardTitle>Rich Content</CardTitle>
                    <CardDescription>Toasts with custom JSX content</CardDescription>
                </CardHeader>
                <CardContent>
                    <div class="flex gap-2 flex-wrap">
                        <Button variant="outline" @click="showRichContent">
                            <User class="size-4 mr-2" />
                            User Notification
                        </Button>
                        <Button variant="outline" @click="showFileUploaded">
                            <Upload class="size-4 mr-2" />
                            File Uploaded
                        </Button>
                        <Button variant="outline" @click="showNewMessage">
                            <MessageSquare class="size-4 mr-2" />
                            New Message
                        </Button>
                    </div>
                </CardContent>
            </Card>

            <!-- Dismiss Toasts -->
            <Card>
                <CardHeader>
                    <CardTitle>Dismiss Toasts</CardTitle>
                    <CardDescription>Programmatically dismiss toasts</CardDescription>
                </CardHeader>
                <CardContent>
                    <div class="flex gap-2 flex-wrap">
                        <Button variant="outline" @click="showAndDismiss">Show & Auto Dismiss</Button>
                        <Button variant="outline" @click="showMultiple">Show Multiple</Button>
                        <Button variant="destructive" @click="dismissAll">Dismiss All</Button>
                    </div>
                </CardContent>
            </Card>

            <!-- Real World Examples -->
            <Card>
                <CardHeader>
                    <CardTitle>Real World Examples</CardTitle>
                    <CardDescription>Common toast patterns in applications</CardDescription>
                </CardHeader>
                <CardContent>
                    <div class="flex gap-2 flex-wrap">
                        <Button @click="showSaved">
                            <Save class="size-4 mr-2" />
                            Save Changes
                        </Button>
                        <Button variant="destructive" @click="showDeleted">
                            <Trash2 class="size-4 mr-2" />
                            Delete Item
                        </Button>
                        <Button variant="outline" @click="showCopied">
                            <Copy class="size-4 mr-2" />
                            Copy to Clipboard
                        </Button>
                        <Button variant="outline" @click="showEmailSent">
                            <Mail class="size-4 mr-2" />
                            Send Email
                        </Button>
                    </div>
                </CardContent>
            </Card>
        </div>
    </LayoutApp>
</template>

<script setup lang="ts">
import { h } from 'vue';
import { toast } from 'vue-sonner';
import { AdminLayout as LayoutApp } from '@/components/app';
import { Card, CardHeader, CardTitle, CardDescription, CardContent } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import {
    CheckCircle,
    XCircle,
    AlertTriangle,
    Info,
    Loader2,
    Save,
    User,
    Upload,
    MessageSquare,
    Trash2,
    Copy,
    Mail,
} from 'lucide-vue-next';
import { useShowcaseNav } from '../_composables/useShowcaseNav';

const { sidebarItems } = useShowcaseNav();

// Basic toasts
const showDefault = () => {
    toast('Event has been created');
};

const showDescription = () => {
    toast('Event has been created', {
        description: 'Monday, January 3rd at 6:00pm',
    });
};

const showLong = () => {
    toast('Your changes have been saved successfully', {
        description: 'All your data has been synchronized with the server. You can continue working on your project.',
    });
};

// Toast types
const showSuccess = () => {
    toast.success('Successfully saved!', {
        description: 'Your changes have been saved.',
    });
};

const showError = () => {
    toast.error('Something went wrong', {
        description: 'There was a problem with your request.',
    });
};

const showWarning = () => {
    toast.warning('Warning', {
        description: 'This action cannot be undone.',
    });
};

const showInfo = () => {
    toast.info('Did you know?', {
        description: 'You can customize toast notifications.',
    });
};

// Toast with actions
const showWithAction = () => {
    toast('File uploaded', {
        action: {
            label: 'View',
            onClick: () => toast.success('Opening file...'),
        },
    });
};

const showWithCancel = () => {
    toast('Item moved to trash', {
        cancel: {
            label: 'Undo',
            onClick: () => toast.success('Item restored'),
        },
    });
};

const showWithBothActions = () => {
    toast('New update available', {
        description: 'Version 2.0 is ready to install.',
        action: {
            label: 'Update',
            onClick: () => toast.success('Updating...'),
        },
        cancel: {
            label: 'Later',
            onClick: () => toast('Reminder set for tomorrow'),
        },
    });
};

// Promise toasts
const showPromiseSuccess = () => {
    toast.promise(
        new Promise((resolve) => setTimeout(resolve, 2000)),
        {
            loading: 'Loading...',
            success: 'Data loaded successfully!',
            error: 'Error loading data',
        }
    );
};

const showPromiseError = () => {
    toast.promise(
        new Promise((_, reject) => setTimeout(() => reject(new Error('Failed')), 2000)),
        {
            loading: 'Loading...',
            success: 'Data loaded successfully!',
            error: 'Error loading data',
        }
    );
};

const showSaveData = () => {
    toast.promise(
        new Promise((resolve) => setTimeout(resolve, 1500)),
        {
            loading: 'Saving your changes...',
            success: 'Changes saved successfully!',
            error: 'Failed to save changes',
        }
    );
};

// Duration
const showDuration = (duration: number) => {
    toast(`This toast will disappear in ${duration / 1000} seconds`, {
        duration,
    });
};

const showInfinite = () => {
    toast('This toast will stay until dismissed', {
        duration: Infinity,
        description: 'Click the X to dismiss',
    });
};

// Rich content
const showRichContent = () => {
    toast('John Doe commented on your post', {
        description: '"Great work on the new feature!"',
    });
};

const showFileUploaded = () => {
    toast.success('File uploaded successfully', {
        description: 'document.pdf (2.4 MB)',
    });
};

const showNewMessage = () => {
    toast('New message from Sarah', {
        description: 'Hey, are you free for a quick call?',
        action: {
            label: 'Reply',
            onClick: () => toast('Opening chat...'),
        },
    });
};

// Dismiss
let toastId: string | number;

const showAndDismiss = () => {
    toastId = toast('This will dismiss in 2 seconds...');
    setTimeout(() => {
        toast.dismiss(toastId);
        toast.success('Dismissed!');
    }, 2000);
};

const showMultiple = () => {
    toast('First notification');
    setTimeout(() => toast.success('Second notification'), 300);
    setTimeout(() => toast.info('Third notification'), 600);
    setTimeout(() => toast.warning('Fourth notification'), 900);
};

const dismissAll = () => {
    toast.dismiss();
};

// Real world examples
const showSaved = () => {
    toast.promise(
        new Promise((resolve) => setTimeout(resolve, 1000)),
        {
            loading: 'Saving...',
            success: 'Changes saved successfully!',
            error: 'Failed to save',
        }
    );
};

const showDeleted = () => {
    toast('Item deleted', {
        description: 'The item has been moved to trash.',
        cancel: {
            label: 'Undo',
            onClick: () => toast.success('Item restored'),
        },
    });
};

const showCopied = () => {
    toast.success('Copied to clipboard!');
};

const showEmailSent = () => {
    toast.promise(
        new Promise((resolve) => setTimeout(resolve, 1500)),
        {
            loading: 'Sending email...',
            success: 'Email sent successfully!',
            error: 'Failed to send email',
        }
    );
};
</script>
