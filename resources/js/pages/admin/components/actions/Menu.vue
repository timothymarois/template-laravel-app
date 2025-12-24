<template>
    <LayoutApp
        title="Components - Menu"
        pageTitle="Menu"
        :pageSidebarItems="sidebarItems"
        pageSidebarTitle="Components"
    >
        <div class="space-y-4">
            <!-- Button Menu and Dropdown Menu side by side -->
            <div class="grid grid-cols-2 gap-4">
                <Card>
                    <CardHeader>
                        <CardTitle>Button Menu</CardTitle>
                        <CardDescription>Dropdown menus with actions</CardDescription>
                    </CardHeader>
                    <CardContent class="space-y-4">
                        <div>
                            <h4 class="text-sm font-medium mb-3">Default (Vertical Dots)</h4>
                            <div class="flex gap-4">
                                <ButtonMenu :items="basicMenuItems" />
                            </div>
                        </div>

                        <div>
                            <h4 class="text-sm font-medium mb-3">Custom Trigger</h4>
                            <div class="flex gap-4">
                                <ButtonMenu :items="basicMenuItems">
                                    <template #trigger>
                                        <Button variant="outline" size="sm">
                                            Actions
                                            <ChevronDown class="size-4" />
                                        </Button>
                                    </template>
                                </ButtonMenu>
                                <ButtonMenu :items="basicMenuItems">
                                    <template #trigger>
                                        <Button variant="ghost" size="icon-sm">
                                            <MoreHorizontal class="size-4" />
                                        </Button>
                                    </template>
                                </ButtonMenu>
                            </div>
                        </div>

                        <div>
                            <h4 class="text-sm font-medium mb-3">With Icons and Separators</h4>
                            <ButtonMenu :items="fullMenuItems">
                                <template #trigger>
                                    <Button variant="outline">
                                        <Settings class="size-4" />
                                        Options
                                    </Button>
                                </template>
                            </ButtonMenu>
                        </div>

                        <div>
                            <h4 class="text-sm font-medium mb-3">With Disabled Items</h4>
                            <ButtonMenu :items="menuWithDisabled">
                                <template #trigger>
                                    <Button variant="outline">
                                        File Actions
                                        <ChevronDown class="size-4" />
                                    </Button>
                                </template>
                            </ButtonMenu>
                        </div>
                    </CardContent>
                </Card>

                <Card>
                    <CardHeader>
                        <CardTitle>Dropdown Menu</CardTitle>
                        <CardDescription>Advanced menus with checkboxes, radios, and submenus</CardDescription>
                    </CardHeader>
                    <CardContent class="space-y-4">
                        <div>
                            <h4 class="text-sm font-medium mb-3">With Checkbox Items</h4>
                            <DropdownMenu>
                                <DropdownMenuTrigger as-child>
                                    <Button variant="outline">
                                        View Options
                                        <ChevronDown class="size-4" />
                                    </Button>
                                </DropdownMenuTrigger>
                                <DropdownMenuContent class="w-48">
                                    <DropdownMenuLabel>Toggle Columns</DropdownMenuLabel>
                                    <DropdownMenuSeparator />
                                    <DropdownMenuCheckboxItem v-model:checked="showName">Name</DropdownMenuCheckboxItem>
                                    <DropdownMenuCheckboxItem v-model:checked="showEmail">Email</DropdownMenuCheckboxItem>
                                    <DropdownMenuCheckboxItem v-model:checked="showStatus">Status</DropdownMenuCheckboxItem>
                                </DropdownMenuContent>
                            </DropdownMenu>
                        </div>

                        <div>
                            <h4 class="text-sm font-medium mb-3">With Radio Items</h4>
                            <DropdownMenu>
                                <DropdownMenuTrigger as-child>
                                    <Button variant="outline">
                                        Sort By
                                        <ChevronDown class="size-4" />
                                    </Button>
                                </DropdownMenuTrigger>
                                <DropdownMenuContent class="w-48">
                                    <DropdownMenuRadioGroup v-model="sortBy">
                                        <DropdownMenuRadioItem value="name">Name</DropdownMenuRadioItem>
                                        <DropdownMenuRadioItem value="date">Date</DropdownMenuRadioItem>
                                        <DropdownMenuRadioItem value="status">Status</DropdownMenuRadioItem>
                                    </DropdownMenuRadioGroup>
                                </DropdownMenuContent>
                            </DropdownMenu>
                        </div>

                        <div>
                            <h4 class="text-sm font-medium mb-3">With Submenu</h4>
                            <DropdownMenu>
                                <DropdownMenuTrigger as-child>
                                    <Button variant="outline">
                                        More Options
                                        <ChevronDown class="size-4" />
                                    </Button>
                                </DropdownMenuTrigger>
                                <DropdownMenuContent class="w-48">
                                    <DropdownMenuItem>
                                        <Copy class="size-4 mr-2" />
                                        Copy
                                    </DropdownMenuItem>
                                    <DropdownMenuItem>
                                        <Pencil class="size-4 mr-2" />
                                        Edit
                                    </DropdownMenuItem>
                                    <DropdownMenuSeparator />
                                    <DropdownMenuSub>
                                        <DropdownMenuSubTrigger>
                                            <Share class="size-4 mr-2" />
                                            Share
                                        </DropdownMenuSubTrigger>
                                        <DropdownMenuSubContent>
                                            <DropdownMenuItem>Email</DropdownMenuItem>
                                            <DropdownMenuItem>Slack</DropdownMenuItem>
                                            <DropdownMenuItem>Copy Link</DropdownMenuItem>
                                        </DropdownMenuSubContent>
                                    </DropdownMenuSub>
                                    <DropdownMenuSeparator />
                                    <DropdownMenuItem class="text-destructive">
                                        <Trash2 class="size-4 mr-2" />
                                        Delete
                                    </DropdownMenuItem>
                                </DropdownMenuContent>
                            </DropdownMenu>
                        </div>

                        <div>
                            <h4 class="text-sm font-medium mb-3">With Disabled Items</h4>
                            <DropdownMenu>
                                <DropdownMenuTrigger as-child>
                                    <Button variant="outline">
                                        Account
                                        <ChevronDown class="size-4" />
                                    </Button>
                                </DropdownMenuTrigger>
                                <DropdownMenuContent class="w-48">
                                    <DropdownMenuLabel>My Account</DropdownMenuLabel>
                                    <DropdownMenuSeparator />
                                    <DropdownMenuItem>
                                        <User class="size-4 mr-2" />
                                        Profile
                                    </DropdownMenuItem>
                                    <DropdownMenuItem>
                                        <Settings class="size-4 mr-2" />
                                        Settings
                                    </DropdownMenuItem>
                                    <DropdownMenuItem disabled>
                                        <CreditCard class="size-4 mr-2" />
                                        Billing (Unavailable)
                                    </DropdownMenuItem>
                                    <DropdownMenuSeparator />
                                    <DropdownMenuItem disabled>
                                        <Download class="size-4 mr-2" />
                                        Export Data (Premium)
                                    </DropdownMenuItem>
                                    <DropdownMenuItem>
                                        <LogOut class="size-4 mr-2" />
                                        Log out
                                    </DropdownMenuItem>
                                </DropdownMenuContent>
                            </DropdownMenu>
                        </div>
                    </CardContent>
                </Card>
            </div>

            <!-- Context Menu full width -->
            <Card>
                <CardHeader>
                    <CardTitle>Context Menu</CardTitle>
                    <CardDescription>Right-click menus for contextual actions</CardDescription>
                </CardHeader>
                <CardContent class="space-y-6">
                    <div class="grid grid-cols-2 gap-6">
                        <div>
                            <h4 class="text-sm font-medium mb-3">Basic Context Menu</h4>
                            <ContextMenu>
                                <ContextMenuTrigger>
                                    <div class="flex h-32 w-full items-center justify-center rounded-md border border-dashed text-sm text-muted-foreground">
                                        Right click here
                                    </div>
                                </ContextMenuTrigger>
                                <ContextMenuContent class="w-48">
                                    <ContextMenuItem>
                                        <ArrowLeft class="size-4 mr-2" />
                                        Back
                                        <ContextMenuShortcut>Alt+Left</ContextMenuShortcut>
                                    </ContextMenuItem>
                                    <ContextMenuItem disabled>
                                        <ArrowRight class="size-4 mr-2" />
                                        Forward
                                        <ContextMenuShortcut>Alt+Right</ContextMenuShortcut>
                                    </ContextMenuItem>
                                    <ContextMenuItem>
                                        <RotateCcw class="size-4 mr-2" />
                                        Reload
                                        <ContextMenuShortcut>Ctrl+R</ContextMenuShortcut>
                                    </ContextMenuItem>
                                    <ContextMenuSeparator />
                                    <ContextMenuItem>
                                        <Bookmark class="size-4 mr-2" />
                                        Add Bookmark
                                    </ContextMenuItem>
                                </ContextMenuContent>
                            </ContextMenu>
                        </div>

                        <div>
                            <h4 class="text-sm font-medium mb-3">With Checkboxes and Radios</h4>
                            <ContextMenu>
                                <ContextMenuTrigger>
                                    <div class="flex h-32 w-full items-center justify-center rounded-md border border-dashed text-sm text-muted-foreground">
                                        Right click here
                                    </div>
                                </ContextMenuTrigger>
                                <ContextMenuContent class="w-56">
                                    <ContextMenuLabel>View Options</ContextMenuLabel>
                                    <ContextMenuSeparator />
                                    <ContextMenuCheckboxItem v-model:checked="contextShowToolbar">
                                        Show Toolbar
                                    </ContextMenuCheckboxItem>
                                    <ContextMenuCheckboxItem v-model:checked="contextShowStatusbar">
                                        Show Status Bar
                                    </ContextMenuCheckboxItem>
                                    <ContextMenuCheckboxItem v-model:checked="contextShowSidebar">
                                        Show Sidebar
                                    </ContextMenuCheckboxItem>
                                    <ContextMenuSeparator />
                                    <ContextMenuLabel>Zoom</ContextMenuLabel>
                                    <ContextMenuRadioGroup v-model="contextZoom">
                                        <ContextMenuRadioItem value="50">50%</ContextMenuRadioItem>
                                        <ContextMenuRadioItem value="100">100%</ContextMenuRadioItem>
                                        <ContextMenuRadioItem value="150">150%</ContextMenuRadioItem>
                                        <ContextMenuRadioItem value="200">200%</ContextMenuRadioItem>
                                    </ContextMenuRadioGroup>
                                </ContextMenuContent>
                            </ContextMenu>
                        </div>

                        <div>
                            <h4 class="text-sm font-medium mb-3">With Submenu</h4>
                            <ContextMenu>
                                <ContextMenuTrigger>
                                    <div class="flex h-32 w-full items-center justify-center rounded-md border border-dashed text-sm text-muted-foreground">
                                        Right click here
                                    </div>
                                </ContextMenuTrigger>
                                <ContextMenuContent class="w-48">
                                    <ContextMenuItem>
                                        <Copy class="size-4 mr-2" />
                                        Copy
                                        <ContextMenuShortcut>Ctrl+C</ContextMenuShortcut>
                                    </ContextMenuItem>
                                    <ContextMenuItem>
                                        <Scissors class="size-4 mr-2" />
                                        Cut
                                        <ContextMenuShortcut>Ctrl+X</ContextMenuShortcut>
                                    </ContextMenuItem>
                                    <ContextMenuItem>
                                        <ClipboardPaste class="size-4 mr-2" />
                                        Paste
                                        <ContextMenuShortcut>Ctrl+V</ContextMenuShortcut>
                                    </ContextMenuItem>
                                    <ContextMenuSeparator />
                                    <ContextMenuSub>
                                        <ContextMenuSubTrigger>
                                            <Share class="size-4 mr-2" />
                                            Share
                                        </ContextMenuSubTrigger>
                                        <ContextMenuSubContent class="w-40">
                                            <ContextMenuItem>
                                                <Mail class="size-4 mr-2" />
                                                Email
                                            </ContextMenuItem>
                                            <ContextMenuItem>
                                                <MessageSquare class="size-4 mr-2" />
                                                Message
                                            </ContextMenuItem>
                                            <ContextMenuSeparator />
                                            <ContextMenuItem>
                                                <LinkIcon class="size-4 mr-2" />
                                                Copy Link
                                            </ContextMenuItem>
                                        </ContextMenuSubContent>
                                    </ContextMenuSub>
                                    <ContextMenuSeparator />
                                    <ContextMenuItem class="text-destructive">
                                        <Trash2 class="size-4 mr-2" />
                                        Delete
                                        <ContextMenuShortcut>Del</ContextMenuShortcut>
                                    </ContextMenuItem>
                                </ContextMenuContent>
                            </ContextMenu>
                        </div>

                        <div>
                            <h4 class="text-sm font-medium mb-3">File Explorer Style</h4>
                            <ContextMenu>
                                <ContextMenuTrigger>
                                    <div class="flex h-32 w-full items-center justify-center rounded-md border border-dashed text-sm text-muted-foreground">
                                        Right click here
                                    </div>
                                </ContextMenuTrigger>
                                <ContextMenuContent class="w-56">
                                    <ContextMenuSub>
                                        <ContextMenuSubTrigger>
                                            <FolderPlus class="size-4 mr-2" />
                                            New
                                        </ContextMenuSubTrigger>
                                        <ContextMenuSubContent class="w-40">
                                            <ContextMenuItem>
                                                <Folder class="size-4 mr-2" />
                                                Folder
                                            </ContextMenuItem>
                                            <ContextMenuItem>
                                                <FileText class="size-4 mr-2" />
                                                Text Document
                                            </ContextMenuItem>
                                            <ContextMenuItem>
                                                <FileImage class="size-4 mr-2" />
                                                Image
                                            </ContextMenuItem>
                                        </ContextMenuSubContent>
                                    </ContextMenuSub>
                                    <ContextMenuSeparator />
                                    <ContextMenuSub>
                                        <ContextMenuSubTrigger>
                                            <ArrowUpDown class="size-4 mr-2" />
                                            Sort by
                                        </ContextMenuSubTrigger>
                                        <ContextMenuSubContent class="w-40">
                                            <ContextMenuRadioGroup v-model="contextSortBy">
                                                <ContextMenuRadioItem value="name">Name</ContextMenuRadioItem>
                                                <ContextMenuRadioItem value="date">Date Modified</ContextMenuRadioItem>
                                                <ContextMenuRadioItem value="type">Type</ContextMenuRadioItem>
                                                <ContextMenuRadioItem value="size">Size</ContextMenuRadioItem>
                                            </ContextMenuRadioGroup>
                                        </ContextMenuSubContent>
                                    </ContextMenuSub>
                                    <ContextMenuSub>
                                        <ContextMenuSubTrigger>
                                            <LayoutGrid class="size-4 mr-2" />
                                            View
                                        </ContextMenuSubTrigger>
                                        <ContextMenuSubContent class="w-40">
                                            <ContextMenuRadioGroup v-model="contextView">
                                                <ContextMenuRadioItem value="icons">Icons</ContextMenuRadioItem>
                                                <ContextMenuRadioItem value="list">List</ContextMenuRadioItem>
                                                <ContextMenuRadioItem value="details">Details</ContextMenuRadioItem>
                                            </ContextMenuRadioGroup>
                                        </ContextMenuSubContent>
                                    </ContextMenuSub>
                                    <ContextMenuSeparator />
                                    <ContextMenuItem>
                                        <RotateCcw class="size-4 mr-2" />
                                        Refresh
                                    </ContextMenuItem>
                                    <ContextMenuSeparator />
                                    <ContextMenuItem>
                                        <Info class="size-4 mr-2" />
                                        Properties
                                    </ContextMenuItem>
                                </ContextMenuContent>
                            </ContextMenu>
                        </div>
                    </div>
                </CardContent>
            </Card>
        </div>
    </LayoutApp>
</template>

<script setup lang="ts">
import { ref } from 'vue';
import { AppLayout as LayoutApp } from '@/components/app';
import { Card, CardHeader, CardTitle, CardDescription, CardContent } from '@/components/ui/card';
import { Button, ButtonMenu } from '@/components/ui/button';
import {
    DropdownMenu,
    DropdownMenuCheckboxItem,
    DropdownMenuContent,
    DropdownMenuItem,
    DropdownMenuLabel,
    DropdownMenuRadioGroup,
    DropdownMenuRadioItem,
    DropdownMenuSeparator,
    DropdownMenuSub,
    DropdownMenuSubContent,
    DropdownMenuSubTrigger,
    DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu';
import {
    ContextMenu,
    ContextMenuCheckboxItem,
    ContextMenuContent,
    ContextMenuItem,
    ContextMenuLabel,
    ContextMenuRadioGroup,
    ContextMenuRadioItem,
    ContextMenuSeparator,
    ContextMenuShortcut,
    ContextMenuSub,
    ContextMenuSubContent,
    ContextMenuSubTrigger,
    ContextMenuTrigger,
} from '@/components/ui/context-menu';
import {
    Settings,
    ChevronDown,
    MoreHorizontal,
    Copy,
    Pencil,
    Share,
    Trash2,
    User,
    CreditCard,
    Download,
    LogOut,
    Save,
    FileText,
    Printer,
    ArrowLeft,
    ArrowRight,
    RotateCcw,
    Bookmark,
    Scissors,
    ClipboardPaste,
    Mail,
    MessageSquare,
    Link as LinkIcon,
    FolderPlus,
    Folder,
    FileImage,
    ArrowUpDown,
    LayoutGrid,
    Info,
} from 'lucide-vue-next';
import { useShowcaseNav } from '../_composables/useShowcaseNav';

const { sidebarItems } = useShowcaseNav();

// Dropdown Menu state
const showName = ref(true);
const showEmail = ref(true);
const showStatus = ref(false);
const sortBy = ref('name');

// Context Menu state
const contextShowToolbar = ref(true);
const contextShowStatusbar = ref(true);
const contextShowSidebar = ref(false);
const contextZoom = ref('100');
const contextSortBy = ref('name');
const contextView = ref('icons');

const basicMenuItems = [
    { label: 'Edit', icon: Pencil, click: () => {} },
    { label: 'Duplicate', icon: Copy, click: () => {} },
    { separator: true },
    { label: 'Delete', icon: Trash2, click: () => {} },
];

const fullMenuItems = [
    { label: 'Edit', icon: Pencil, click: () => {} },
    { label: 'Duplicate', icon: Copy, click: () => {} },
    { separator: true },
    { label: 'Share', icon: Share, click: () => {} },
    { separator: true },
    { label: 'Delete', icon: Trash2, click: () => {} },
];

const menuWithDisabled = [
    { label: 'Save', icon: Save, click: () => {} },
    { label: 'Save As...', icon: FileText, click: () => {} },
    { separator: true },
    { label: 'Print', icon: Printer, click: () => {}, disabled: true },
    { label: 'Export PDF', icon: Download, click: () => {}, disabled: true },
    { separator: true },
    { label: 'Delete', icon: Trash2, click: () => {}, class: 'text-destructive' },
];
</script>
