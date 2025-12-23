<template>
    <LayoutApp
        title="Components - Card"
        pageTitle="Display"
        :pageNavItems="sideNavItems"
        :pageTabs="displayTabs"
    >
        <div class="space-y-4">
            <!-- Basic Cards -->
            <div class="grid grid-cols-4 gap-4">
                <Card class="flex flex-col">
                    <CardHeader>
                        <CardTitle>Basic Card</CardTitle>
                        <CardDescription>Header and content only</CardDescription>
                    </CardHeader>
                    <CardContent class="flex-1">
                        <p class="text-sm text-muted-foreground">This is the main content area of the card.</p>
                    </CardContent>
                </Card>

                <Card class="flex flex-col">
                    <CardHeader>
                        <CardTitle>With Footer</CardTitle>
                        <CardDescription>Card with action buttons</CardDescription>
                    </CardHeader>
                    <CardContent class="flex-1">
                        <p class="text-sm text-muted-foreground">Content with footer actions below.</p>
                    </CardContent>
                    <CardFooter class="border-t pt-4 justify-end gap-2">
                        <Button variant="outline" size="sm">Cancel</Button>
                        <Button size="sm">Save</Button>
                    </CardFooter>
                </Card>

                <Card class="flex flex-col">
                    <CardHeader>
                        <CardTitle>Content Only</CardTitle>
                    </CardHeader>
                    <CardContent class="flex-1">
                        <p class="text-sm text-muted-foreground">Card without description, just title and content.</p>
                    </CardContent>
                </Card>

                <Card class="flex flex-col">
                    <CardHeader>
                        <CardTitle>Full Footer</CardTitle>
                        <CardDescription>Footer spans full width</CardDescription>
                    </CardHeader>
                    <CardContent class="flex-1">
                        <p class="text-sm text-muted-foreground">Footer button takes full width.</p>
                    </CardContent>
                    <CardFooter class="border-t pt-4">
                        <Button fluid>Submit</Button>
                    </CardFooter>
                </Card>
            </div>

            <!-- Style Variations -->
            <div class="grid grid-cols-4 gap-4">
                <Card class="flex flex-col">
                    <CardHeader class="border-b">
                        <CardTitle>Divided</CardTitle>
                        <CardDescription>Border below header</CardDescription>
                    </CardHeader>
                    <CardContent class="flex-1 pt-6">
                        <p class="text-sm text-muted-foreground">Header is visually separated from content.</p>
                    </CardContent>
                </Card>

                <Card class="flex flex-col bg-muted/50">
                    <CardHeader>
                        <CardTitle>Muted</CardTitle>
                        <CardDescription>Subtle background</CardDescription>
                    </CardHeader>
                    <CardContent class="flex-1">
                        <p class="text-sm text-muted-foreground">Muted background for less emphasis.</p>
                    </CardContent>
                </Card>

                <Card class="flex flex-col border-primary">
                    <CardHeader>
                        <CardTitle>Highlighted</CardTitle>
                        <CardDescription>Primary border color</CardDescription>
                    </CardHeader>
                    <CardContent class="flex-1">
                        <p class="text-sm text-muted-foreground">Accent border for emphasis.</p>
                    </CardContent>
                </Card>

                <Card class="flex flex-col hover:border-primary/50 transition-colors cursor-pointer">
                    <CardHeader>
                        <CardTitle>Clickable</CardTitle>
                        <CardDescription>Hover to see effect</CardDescription>
                    </CardHeader>
                    <CardContent class="flex-1">
                        <p class="text-sm text-muted-foreground">Interactive card with hover state.</p>
                    </CardContent>
                </Card>
            </div>

            <!-- Content Examples -->
            <div class="grid grid-cols-4 gap-4">
                <Card class="flex flex-col">
                    <CardHeader class="pb-2">
                        <CardDescription>Total Users</CardDescription>
                        <CardTitle class="text-2xl">2,350</CardTitle>
                    </CardHeader>
                    <CardContent>
                        <p class="text-xs text-muted-foreground">+12.5% from last month</p>
                    </CardContent>
                </Card>

                <Card class="flex flex-col">
                    <CardHeader class="pb-2">
                        <CardDescription>Orders</CardDescription>
                        <CardTitle class="text-2xl">1,247</CardTitle>
                    </CardHeader>
                    <CardContent>
                        <p class="text-xs text-muted-foreground">+8.2% from last month</p>
                    </CardContent>
                </Card>

                <Card class="flex flex-col">
                    <CardHeader class="pb-2">
                        <CardDescription>Revenue</CardDescription>
                        <CardTitle class="text-2xl">$45,231</CardTitle>
                    </CardHeader>
                    <CardContent>
                        <p class="text-xs text-muted-foreground">+20.1% from last month</p>
                    </CardContent>
                </Card>

                <Card class="flex flex-col">
                    <CardHeader class="pb-2">
                        <CardDescription>Active Now</CardDescription>
                        <CardTitle class="text-2xl">573</CardTitle>
                    </CardHeader>
                    <CardContent>
                        <p class="text-xs text-muted-foreground">+201 since last hour</p>
                    </CardContent>
                </Card>
            </div>

            <!-- Cards with Actions -->
            <div class="grid grid-cols-2 gap-4">
                <Card class="flex flex-col">
                    <CardHeader class="flex flex-row items-center justify-between space-y-0">
                        <div class="space-y-1">
                            <CardTitle>Recent Activity</CardTitle>
                            <CardDescription>Latest updates from your team</CardDescription>
                        </div>
                        <DropdownMenu>
                            <DropdownMenuTrigger as-child>
                                <Button variant="ghost" size="icon" class="h-8 w-8">
                                    <Ellipsis class="h-4 w-4" />
                                    <span class="sr-only">Open menu</span>
                                </Button>
                            </DropdownMenuTrigger>
                            <DropdownMenuContent align="end">
                                <DropdownMenuItem>View all</DropdownMenuItem>
                                <DropdownMenuItem>Mark as read</DropdownMenuItem>
                                <DropdownMenuSeparator />
                                <DropdownMenuItem>Settings</DropdownMenuItem>
                            </DropdownMenuContent>
                        </DropdownMenu>
                    </CardHeader>
                    <CardContent class="flex-1">
                        <div class="divide-y">
                            <div class="flex justify-between items-center py-2">
                                <span class="text-sm">Item One</span>
                                <Badge>Active</Badge>
                            </div>
                            <div class="flex justify-between items-center py-2">
                                <span class="text-sm">Item Two</span>
                                <Badge variant="secondary">Pending</Badge>
                            </div>
                            <div class="flex justify-between items-center py-2">
                                <span class="text-sm">Item Three</span>
                                <Badge variant="outline">Draft</Badge>
                            </div>
                        </div>
                    </CardContent>
                </Card>

                <Card class="flex flex-col">
                    <CardHeader class="flex flex-row items-center justify-between space-y-0 border-b">
                        <div class="space-y-1">
                            <CardTitle>Team Members</CardTitle>
                            <CardDescription>Manage your team access</CardDescription>
                        </div>
                        <DropdownMenu>
                            <DropdownMenuTrigger as-child>
                                <Button variant="ghost" size="icon" class="h-8 w-8">
                                    <Ellipsis class="h-4 w-4" />
                                    <span class="sr-only">Open menu</span>
                                </Button>
                            </DropdownMenuTrigger>
                            <DropdownMenuContent align="end">
                                <DropdownMenuItem>Add member</DropdownMenuItem>
                                <DropdownMenuItem>Export list</DropdownMenuItem>
                                <DropdownMenuSeparator />
                                <DropdownMenuItem>Team settings</DropdownMenuItem>
                            </DropdownMenuContent>
                        </DropdownMenu>
                    </CardHeader>
                    <CardContent class="flex-1 p-0">
                        <div class="divide-y">
                            <div class="flex justify-between items-center px-6 py-3 hover:bg-muted/50 transition-colors">
                                <span class="text-sm">John Doe</span>
                                <Badge>Admin</Badge>
                            </div>
                            <div class="flex justify-between items-center px-6 py-3 hover:bg-muted/50 transition-colors">
                                <span class="text-sm">Jane Smith</span>
                                <Badge variant="secondary">Editor</Badge>
                            </div>
                            <div class="flex justify-between items-center px-6 py-3 hover:bg-muted/50 transition-colors">
                                <span class="text-sm">Bob Johnson</span>
                                <Badge variant="outline">Viewer</Badge>
                            </div>
                        </div>
                    </CardContent>
                </Card>
            </div>

            <!-- Edge to Edge Table -->
            <div class="grid grid-cols-2 gap-4">
                <Card class="flex flex-col">
                    <CardHeader class="flex flex-row items-center justify-between space-y-0 border-b">
                        <div class="space-y-1">
                            <CardTitle>Recent Orders</CardTitle>
                            <CardDescription>Table-like content without padding</CardDescription>
                        </div>
                        <DropdownMenu>
                            <DropdownMenuTrigger as-child>
                                <Button variant="ghost" size="icon" class="h-8 w-8">
                                    <Ellipsis class="h-4 w-4" />
                                    <span class="sr-only">Open menu</span>
                                </Button>
                            </DropdownMenuTrigger>
                            <DropdownMenuContent align="end">
                                <DropdownMenuItem>View all orders</DropdownMenuItem>
                                <DropdownMenuItem>Export</DropdownMenuItem>
                            </DropdownMenuContent>
                        </DropdownMenu>
                    </CardHeader>
                    <CardContent class="flex-1 p-0">
                        <div class="divide-y">
                            <div class="grid grid-cols-3 px-6 py-2 text-xs font-medium text-muted-foreground bg-muted/30">
                                <span>Name</span>
                                <span>Status</span>
                                <span class="text-right">Amount</span>
                            </div>
                            <div class="grid grid-cols-3 px-6 py-3 text-sm">
                                <span>Order #1234</span>
                                <span><Badge variant="outline" class="text-xs">Shipped</Badge></span>
                                <span class="text-right">$250.00</span>
                            </div>
                            <div class="grid grid-cols-3 px-6 py-3 text-sm">
                                <span>Order #1235</span>
                                <span><Badge class="text-xs">Delivered</Badge></span>
                                <span class="text-right">$125.00</span>
                            </div>
                            <div class="grid grid-cols-3 px-6 py-3 text-sm">
                                <span>Order #1236</span>
                                <span><Badge variant="secondary" class="text-xs">Processing</Badge></span>
                                <span class="text-right">$89.00</span>
                            </div>
                        </div>
                    </CardContent>
                </Card>
            </div>
        </div>
    </LayoutApp>
</template>

<script setup lang="ts">
import { AdminLayout as LayoutApp } from '@/components/app';
import { Card, CardHeader, CardTitle, CardDescription, CardContent, CardFooter } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Badge } from '@/components/ui/badge';
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuItem,
    DropdownMenuSeparator,
    DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu';
import { Ellipsis } from 'lucide-vue-next';
import { useShowcaseNav } from '../_composables/useShowcaseNav';

const { sideNavItems, displayTabs } = useShowcaseNav();
</script>
