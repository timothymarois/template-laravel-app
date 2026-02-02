<template>
    <LayoutApp
        title="Components - Command"
        pageTitle="Command"
        :pageSidebarItems="sidebarItems"
        pageSidebarTitle="Components"
    >
        <div class="space-y-4">
            <!-- Basic Command -->
            <Card>
                <CardHeader>
                    <CardTitle>Command Palette</CardTitle>
                    <CardDescription>A command menu with search functionality for quick actions</CardDescription>
                </CardHeader>
                <CardContent>
                    <div class="rounded-md border">
                        <Command class="rounded-lg">
                            <CommandInput placeholder="Type a command or search..." />
                            <CommandList>
                                <CommandEmpty>No results found.</CommandEmpty>
                                <CommandGroup heading="Suggestions">
                                    <CommandItem value="calendar">
                                        <Calendar class="mr-2 h-4 w-4" />
                                        Calendar
                                    </CommandItem>
                                    <CommandItem value="search">
                                        <Search class="mr-2 h-4 w-4" />
                                        Search
                                    </CommandItem>
                                    <CommandItem value="calculator">
                                        <Calculator class="mr-2 h-4 w-4" />
                                        Calculator
                                    </CommandItem>
                                </CommandGroup>
                                <CommandSeparator />
                                <CommandGroup heading="Settings">
                                    <CommandItem value="profile">
                                        <User class="mr-2 h-4 w-4" />
                                        Profile
                                        <CommandShortcut>Ctrl+P</CommandShortcut>
                                    </CommandItem>
                                    <CommandItem value="billing">
                                        <CreditCard class="mr-2 h-4 w-4" />
                                        Billing
                                        <CommandShortcut>Ctrl+B</CommandShortcut>
                                    </CommandItem>
                                    <CommandItem value="settings">
                                        <Settings class="mr-2 h-4 w-4" />
                                        Settings
                                        <CommandShortcut>Ctrl+S</CommandShortcut>
                                    </CommandItem>
                                </CommandGroup>
                            </CommandList>
                        </Command>
                    </div>
                </CardContent>
            </Card>

            <!-- Command Dialog -->
            <Card>
                <CardHeader>
                    <CardTitle>Command Dialog</CardTitle>
                    <CardDescription>Command menu in a dialog, typically triggered by a keyboard shortcut</CardDescription>
                </CardHeader>
                <CardContent>
                    <div class="space-y-4">
                        <div class="flex items-center gap-4">
                            <Button @click="showCommandDialog = true">
                                Open Command Palette
                            </Button>
                            <p class="text-sm text-muted-foreground">
                                Press <kbd class="px-2 py-1 text-xs bg-muted rounded">Ctrl</kbd> + <kbd class="px-2 py-1 text-xs bg-muted rounded">K</kbd> to open
                            </p>
                        </div>
                        <CommandDialog v-model:open="showCommandDialog">
                            <CommandInput placeholder="Type a command or search..." />
                            <CommandList>
                                <CommandEmpty>No results found.</CommandEmpty>
                                <CommandGroup heading="Suggestions">
                                    <CommandItem value="calendar" @select="handleSelect('calendar')">
                                        <Calendar class="mr-2 h-4 w-4" />
                                        Calendar
                                    </CommandItem>
                                    <CommandItem value="search" @select="handleSelect('search')">
                                        <Search class="mr-2 h-4 w-4" />
                                        Search Docs
                                    </CommandItem>
                                    <CommandItem value="calculator" @select="handleSelect('calculator')">
                                        <Calculator class="mr-2 h-4 w-4" />
                                        Calculator
                                    </CommandItem>
                                </CommandGroup>
                                <CommandSeparator />
                                <CommandGroup heading="Settings">
                                    <CommandItem value="profile" @select="handleSelect('profile')">
                                        <User class="mr-2 h-4 w-4" />
                                        Profile
                                        <CommandShortcut>Ctrl+P</CommandShortcut>
                                    </CommandItem>
                                    <CommandItem value="billing" @select="handleSelect('billing')">
                                        <CreditCard class="mr-2 h-4 w-4" />
                                        Billing
                                        <CommandShortcut>Ctrl+B</CommandShortcut>
                                    </CommandItem>
                                    <CommandItem value="settings" @select="handleSelect('settings')">
                                        <Settings class="mr-2 h-4 w-4" />
                                        Settings
                                        <CommandShortcut>Ctrl+S</CommandShortcut>
                                    </CommandItem>
                                </CommandGroup>
                            </CommandList>
                        </CommandDialog>
                    </div>
                    <div class="mt-4 p-3 bg-muted rounded-md text-sm">
                        <p class="font-medium mb-1">Usage tips:</p>
                        <ul class="list-disc list-inside text-muted-foreground space-y-1">
                            <li>Use <code class="bg-background px-1 rounded">CommandDialog</code> for keyboard-triggered command palettes</li>
                            <li>Add <code class="bg-background px-1 rounded">@select</code> handlers to CommandItem for actions</li>
                            <li>Use <code class="bg-background px-1 rounded">CommandShortcut</code> to display keyboard shortcuts</li>
                        </ul>
                    </div>
                </CardContent>
            </Card>

            <!-- Grouped Commands -->
            <Card>
                <CardHeader>
                    <CardTitle>Grouped Commands</CardTitle>
                    <CardDescription>Commands organized into logical groups with separators</CardDescription>
                </CardHeader>
                <CardContent>
                    <div class="rounded-md border">
                        <Command class="rounded-lg">
                            <CommandInput placeholder="Search actions..." />
                            <CommandList>
                                <CommandEmpty>No actions found.</CommandEmpty>
                                <CommandGroup heading="Navigation">
                                    <CommandItem value="home">
                                        <Home class="mr-2 h-4 w-4" />
                                        Go to Home
                                    </CommandItem>
                                    <CommandItem value="dashboard">
                                        <LayoutDashboard class="mr-2 h-4 w-4" />
                                        Go to Dashboard
                                    </CommandItem>
                                    <CommandItem value="projects">
                                        <Folder class="mr-2 h-4 w-4" />
                                        View Projects
                                    </CommandItem>
                                </CommandGroup>
                                <CommandSeparator />
                                <CommandGroup heading="Actions">
                                    <CommandItem value="new-file">
                                        <FilePlus class="mr-2 h-4 w-4" />
                                        New File
                                        <CommandShortcut>Ctrl+N</CommandShortcut>
                                    </CommandItem>
                                    <CommandItem value="new-folder">
                                        <FolderPlus class="mr-2 h-4 w-4" />
                                        New Folder
                                    </CommandItem>
                                    <CommandItem value="upload">
                                        <Upload class="mr-2 h-4 w-4" />
                                        Upload File
                                        <CommandShortcut>Ctrl+U</CommandShortcut>
                                    </CommandItem>
                                </CommandGroup>
                                <CommandSeparator />
                                <CommandGroup heading="Help">
                                    <CommandItem value="docs">
                                        <FileText class="mr-2 h-4 w-4" />
                                        Documentation
                                    </CommandItem>
                                    <CommandItem value="support">
                                        <LifeBuoy class="mr-2 h-4 w-4" />
                                        Support
                                    </CommandItem>
                                </CommandGroup>
                            </CommandList>
                        </Command>
                    </div>
                </CardContent>
            </Card>

            <!-- Disabled Items -->
            <Card>
                <CardHeader>
                    <CardTitle>Disabled Items</CardTitle>
                    <CardDescription>Command items can be disabled when actions are unavailable</CardDescription>
                </CardHeader>
                <CardContent>
                    <div class="rounded-md border max-w-md">
                        <Command class="rounded-lg">
                            <CommandInput placeholder="Search..." />
                            <CommandList>
                                <CommandEmpty>No results found.</CommandEmpty>
                                <CommandGroup heading="Account">
                                    <CommandItem value="profile-enabled">
                                        <User class="mr-2 h-4 w-4" />
                                        View Profile
                                    </CommandItem>
                                    <CommandItem value="edit-profile" disabled>
                                        <UserCog class="mr-2 h-4 w-4" />
                                        Edit Profile (Locked)
                                    </CommandItem>
                                    <CommandItem value="billing-enabled">
                                        <CreditCard class="mr-2 h-4 w-4" />
                                        Manage Billing
                                    </CommandItem>
                                    <CommandItem value="delete" disabled>
                                        <Trash class="mr-2 h-4 w-4" />
                                        Delete Account (Admin Only)
                                    </CommandItem>
                                </CommandGroup>
                            </CommandList>
                        </Command>
                    </div>
                </CardContent>
            </Card>
        </div>
    </LayoutApp>
</template>

<script setup lang="ts">
import { ref, onMounted, onUnmounted } from 'vue';
import { AppLayout as LayoutApp } from '@/components/app';
import { Card, CardHeader, CardTitle, CardDescription, CardContent } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import {
    Command,
    CommandDialog,
    CommandInput,
    CommandList,
    CommandEmpty,
    CommandGroup,
    CommandItem,
    CommandSeparator,
    CommandShortcut,
} from '@/components/ui/command';
import {
    Calendar,
    Search,
    Calculator,
    User,
    UserCog,
    CreditCard,
    Settings,
    Home,
    LayoutDashboard,
    Folder,
    FilePlus,
    FolderPlus,
    Upload,
    FileText,
    LifeBuoy,
    Trash,
} from 'lucide-vue-next';
import { useShowcaseNav } from '../_composables/useShowcaseNav';

const { sidebarItems } = useShowcaseNav();

const showCommandDialog = ref(false);

const handleSelect = (_value: string) => {
    showCommandDialog.value = false;
};

// Keyboard shortcut to open command dialog
const handleKeyDown = (e: KeyboardEvent) => {
    if (e.key === 'k' && (e.metaKey || e.ctrlKey)) {
        e.preventDefault();
        showCommandDialog.value = !showCommandDialog.value;
    }
};

onMounted(() => {
    document.addEventListener('keydown', handleKeyDown);
});

onUnmounted(() => {
    document.removeEventListener('keydown', handleKeyDown);
});
</script>
