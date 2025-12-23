<template>
    <LayoutApp
        title="Components - Menu"
        pageTitle="Menu"
        :pageSidebarItems="sidebarItems"
        pageSidebarTitle="Components"
    >
        <div class="space-y-4">
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
    </LayoutApp>
</template>

<script setup lang="ts">
import { ref } from 'vue';
import { AdminLayout as LayoutApp } from '@/components/app';
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
} from 'lucide-vue-next';
import { useShowcaseNav } from '../_composables/useShowcaseNav';

const { sidebarItems } = useShowcaseNav();

const showName = ref(true);
const showEmail = ref(true);
const showStatus = ref(false);
const sortBy = ref('name');

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
