<template>
    <LayoutApp
        title="Components - Tabs"
        pageTitle="Tabs"
        :pageSidebarItems="sidebarItems"
        pageSidebarTitle="Components"
    >
        <div class="space-y-4">
            <!-- Underline Tabs -->
            <Card>
                <CardHeader>
                    <CardTitle>Underline Tabs</CardTitle>
                    <CardDescription>Classic underline style tabs for traditional navigation</CardDescription>
                </CardHeader>
                <CardContent>
                    <Tabs default-value="account" variant="underline">
                        <TabsList>
                            <TabsTrigger value="account">Account</TabsTrigger>
                            <TabsTrigger value="password">Password</TabsTrigger>
                            <TabsTrigger value="notifications">Notifications</TabsTrigger>
                        </TabsList>
                        <TabsContent value="account">
                            <div class="space-y-4">
                                <h4 class="font-medium">Account Settings</h4>
                                <p class="text-sm text-muted-foreground">
                                    Manage your account settings and preferences. Update your profile information,
                                    change your email address, or configure your account security options.
                                </p>
                                <div class="flex gap-2">
                                    <Button size="sm">Save Changes</Button>
                                    <Button size="sm" variant="outline">Cancel</Button>
                                </div>
                            </div>
                        </TabsContent>
                        <TabsContent value="password">
                            <div class="space-y-4">
                                <h4 class="font-medium">Password Settings</h4>
                                <p class="text-sm text-muted-foreground">
                                    Update your password to keep your account secure. We recommend using a strong,
                                    unique password that you don't use elsewhere.
                                </p>
                                <Button size="sm">Change Password</Button>
                            </div>
                        </TabsContent>
                        <TabsContent value="notifications">
                            <div class="space-y-4">
                                <h4 class="font-medium">Notification Preferences</h4>
                                <p class="text-sm text-muted-foreground">
                                    Configure how and when you receive notifications. Choose between email,
                                    push notifications, or in-app alerts.
                                </p>
                                <Button size="sm">Update Preferences</Button>
                            </div>
                        </TabsContent>
                    </Tabs>
                </CardContent>
            </Card>

            <!-- Pills Tabs -->
            <Card>
                <CardHeader>
                    <CardTitle>Pills Tabs</CardTitle>
                    <CardDescription>Pill-style tabs with rounded backgrounds</CardDescription>
                </CardHeader>
                <CardContent>
                    <Tabs default-value="overview" variant="pills">
                        <TabsList>
                            <TabsTrigger value="overview">Overview</TabsTrigger>
                            <TabsTrigger value="analytics">Analytics</TabsTrigger>
                            <TabsTrigger value="reports">Reports</TabsTrigger>
                            <TabsTrigger value="settings">Settings</TabsTrigger>
                        </TabsList>
                        <TabsContent value="overview">
                            <div class="rounded-lg border p-4">
                                <div class="flex items-center gap-4">
                                    <LayoutDashboard class="size-8 text-primary" />
                                    <div>
                                        <h4 class="font-medium">Dashboard Overview</h4>
                                        <p class="text-sm text-muted-foreground">View your key metrics and performance indicators at a glance.</p>
                                    </div>
                                </div>
                            </div>
                        </TabsContent>
                        <TabsContent value="analytics">
                            <div class="rounded-lg border p-4">
                                <div class="flex items-center gap-4">
                                    <BarChart3 class="size-8 text-primary" />
                                    <div>
                                        <h4 class="font-medium">Analytics Dashboard</h4>
                                        <p class="text-sm text-muted-foreground">Deep dive into your data with detailed analytics and visualizations.</p>
                                    </div>
                                </div>
                            </div>
                        </TabsContent>
                        <TabsContent value="reports">
                            <div class="rounded-lg border p-4">
                                <div class="flex items-center gap-4">
                                    <FileText class="size-8 text-primary" />
                                    <div>
                                        <h4 class="font-medium">Reports Center</h4>
                                        <p class="text-sm text-muted-foreground">Generate and download detailed reports for your business.</p>
                                    </div>
                                </div>
                            </div>
                        </TabsContent>
                        <TabsContent value="settings">
                            <div class="rounded-lg border p-4">
                                <div class="flex items-center gap-4">
                                    <Settings class="size-8 text-primary" />
                                    <div>
                                        <h4 class="font-medium">System Settings</h4>
                                        <p class="text-sm text-muted-foreground">Configure your application preferences and integrations.</p>
                                    </div>
                                </div>
                            </div>
                        </TabsContent>
                    </Tabs>
                </CardContent>
            </Card>

            <!-- Boxed Tabs -->
            <Card>
                <CardHeader>
                    <CardTitle>Boxed Tabs</CardTitle>
                    <CardDescription>Segmented control style with equal-width tabs</CardDescription>
                </CardHeader>
                <CardContent>
                    <Tabs default-value="daily" variant="boxed">
                        <TabsList class="w-full max-w-md">
                            <TabsTrigger value="daily">Daily</TabsTrigger>
                            <TabsTrigger value="weekly">Weekly</TabsTrigger>
                            <TabsTrigger value="monthly">Monthly</TabsTrigger>
                        </TabsList>
                        <TabsContent value="daily">
                            <div class="grid grid-cols-3 gap-4">
                                <div class="rounded-lg border p-4 text-center">
                                    <p class="text-2xl font-bold">1,234</p>
                                    <p class="text-sm text-muted-foreground">Visitors Today</p>
                                </div>
                                <div class="rounded-lg border p-4 text-center">
                                    <p class="text-2xl font-bold">56</p>
                                    <p class="text-sm text-muted-foreground">New Orders</p>
                                </div>
                                <div class="rounded-lg border p-4 text-center">
                                    <p class="text-2xl font-bold">$2.4k</p>
                                    <p class="text-sm text-muted-foreground">Revenue</p>
                                </div>
                            </div>
                        </TabsContent>
                        <TabsContent value="weekly">
                            <div class="grid grid-cols-3 gap-4">
                                <div class="rounded-lg border p-4 text-center">
                                    <p class="text-2xl font-bold">8,456</p>
                                    <p class="text-sm text-muted-foreground">Visitors This Week</p>
                                </div>
                                <div class="rounded-lg border p-4 text-center">
                                    <p class="text-2xl font-bold">384</p>
                                    <p class="text-sm text-muted-foreground">New Orders</p>
                                </div>
                                <div class="rounded-lg border p-4 text-center">
                                    <p class="text-2xl font-bold">$16.8k</p>
                                    <p class="text-sm text-muted-foreground">Revenue</p>
                                </div>
                            </div>
                        </TabsContent>
                        <TabsContent value="monthly">
                            <div class="grid grid-cols-3 gap-4">
                                <div class="rounded-lg border p-4 text-center">
                                    <p class="text-2xl font-bold">32,891</p>
                                    <p class="text-sm text-muted-foreground">Visitors This Month</p>
                                </div>
                                <div class="rounded-lg border p-4 text-center">
                                    <p class="text-2xl font-bold">1,456</p>
                                    <p class="text-sm text-muted-foreground">New Orders</p>
                                </div>
                                <div class="rounded-lg border p-4 text-center">
                                    <p class="text-2xl font-bold">$67.2k</p>
                                    <p class="text-sm text-muted-foreground">Revenue</p>
                                </div>
                            </div>
                        </TabsContent>
                    </Tabs>
                </CardContent>
            </Card>

            <!-- Controlled Tabs -->
            <Card>
                <CardHeader>
                    <CardTitle>Controlled Tabs</CardTitle>
                    <CardDescription>Programmatically control the active tab with v-model</CardDescription>
                </CardHeader>
                <CardContent class="space-y-4">
                    <div class="flex gap-2">
                        <Button size="sm" variant="outline" @click="activeTab = 'tab1'">Go to Tab 1</Button>
                        <Button size="sm" variant="outline" @click="activeTab = 'tab2'">Go to Tab 2</Button>
                        <Button size="sm" variant="outline" @click="activeTab = 'tab3'">Go to Tab 3</Button>
                    </div>
                    <p class="text-sm text-muted-foreground">Current tab: <code class="bg-muted px-1 py-0.5 rounded">{{ activeTab }}</code></p>
                    <Tabs v-model="activeTab" variant="pills">
                        <TabsList>
                            <TabsTrigger value="tab1">Tab 1</TabsTrigger>
                            <TabsTrigger value="tab2">Tab 2</TabsTrigger>
                            <TabsTrigger value="tab3">Tab 3</TabsTrigger>
                        </TabsList>
                        <TabsContent value="tab1">
                            <div class="p-4 bg-muted/50 rounded-lg">
                                <p>Content for Tab 1. Click the buttons above to switch tabs programmatically.</p>
                            </div>
                        </TabsContent>
                        <TabsContent value="tab2">
                            <div class="p-4 bg-muted/50 rounded-lg">
                                <p>Content for Tab 2. This demonstrates controlled tab state.</p>
                            </div>
                        </TabsContent>
                        <TabsContent value="tab3">
                            <div class="p-4 bg-muted/50 rounded-lg">
                                <p>Content for Tab 3. The active tab can be changed via v-model.</p>
                            </div>
                        </TabsContent>
                    </Tabs>
                </CardContent>
            </Card>

            <!-- Disabled Tabs -->
            <Card>
                <CardHeader>
                    <CardTitle>Disabled Tabs</CardTitle>
                    <CardDescription>Individual tabs can be disabled</CardDescription>
                </CardHeader>
                <CardContent>
                    <Tabs default-value="active" variant="underline">
                        <TabsList>
                            <TabsTrigger value="active">Active</TabsTrigger>
                            <TabsTrigger value="disabled" disabled>Disabled</TabsTrigger>
                            <TabsTrigger value="another">Another Tab</TabsTrigger>
                            <TabsTrigger value="also-disabled" disabled>Also Disabled</TabsTrigger>
                        </TabsList>
                        <TabsContent value="active">
                            <p class="text-sm text-muted-foreground">This tab is active and clickable.</p>
                        </TabsContent>
                        <TabsContent value="another">
                            <p class="text-sm text-muted-foreground">This is another active tab.</p>
                        </TabsContent>
                    </Tabs>
                </CardContent>
            </Card>

            <!-- Tabs with Icons -->
            <Card>
                <CardHeader>
                    <CardTitle>Tabs with Icons</CardTitle>
                    <CardDescription>Enhance tabs with icons for better visual hierarchy</CardDescription>
                </CardHeader>
                <CardContent>
                    <Tabs default-value="profile" variant="pills">
                        <TabsList>
                            <TabsTrigger value="profile" class="gap-2">
                                <User class="size-4" />
                                Profile
                            </TabsTrigger>
                            <TabsTrigger value="billing" class="gap-2">
                                <CreditCard class="size-4" />
                                Billing
                            </TabsTrigger>
                            <TabsTrigger value="team" class="gap-2">
                                <Users class="size-4" />
                                Team
                            </TabsTrigger>
                        </TabsList>
                        <TabsContent value="profile">
                            <div class="flex items-center gap-4 p-4 border rounded-lg">
                                <div class="size-16 rounded-full bg-primary/10 flex items-center justify-center">
                                    <User class="size-8 text-primary" />
                                </div>
                                <div>
                                    <h4 class="font-medium">John Doe</h4>
                                    <p class="text-sm text-muted-foreground">john@example.com</p>
                                </div>
                            </div>
                        </TabsContent>
                        <TabsContent value="billing">
                            <div class="p-4 border rounded-lg">
                                <h4 class="font-medium mb-2">Payment Method</h4>
                                <div class="flex items-center gap-2 text-sm text-muted-foreground">
                                    <CreditCard class="size-4" />
                                    <span>Visa ending in 4242</span>
                                </div>
                            </div>
                        </TabsContent>
                        <TabsContent value="team">
                            <div class="p-4 border rounded-lg">
                                <h4 class="font-medium mb-2">Team Members</h4>
                                <p class="text-sm text-muted-foreground">You have 5 team members in your organization.</p>
                            </div>
                        </TabsContent>
                    </Tabs>
                </CardContent>
            </Card>

            <!-- Full Width Header Tabs -->
            <Card class="overflow-hidden">
                <Tabs default-value="overview" variant="underline">
                    <div class="border-b">
                        <TabsList class="w-full justify-start rounded-none border-b-0 px-6">
                            <TabsTrigger value="overview">Overview</TabsTrigger>
                            <TabsTrigger value="activity">Activity</TabsTrigger>
                            <TabsTrigger value="settings">Settings</TabsTrigger>
                            <TabsTrigger value="members">Members</TabsTrigger>
                        </TabsList>
                    </div>
                    <CardContent class="pt-6">
                        <TabsContent value="overview" class="mt-0">
                            <div class="space-y-4">
                                <h4 class="font-medium">Project Overview</h4>
                                <p class="text-sm text-muted-foreground">
                                    This example shows tabs spanning edge-to-edge in the card header area.
                                    The tabs replace the traditional card header and provide navigation
                                    that feels integrated with the card itself.
                                </p>
                                <div class="grid grid-cols-3 gap-4">
                                    <div class="rounded-lg border p-4 text-center">
                                        <p class="text-2xl font-bold text-primary">24</p>
                                        <p class="text-sm text-muted-foreground">Tasks</p>
                                    </div>
                                    <div class="rounded-lg border p-4 text-center">
                                        <p class="text-2xl font-bold text-primary">8</p>
                                        <p class="text-sm text-muted-foreground">In Progress</p>
                                    </div>
                                    <div class="rounded-lg border p-4 text-center">
                                        <p class="text-2xl font-bold text-primary">16</p>
                                        <p class="text-sm text-muted-foreground">Completed</p>
                                    </div>
                                </div>
                            </div>
                        </TabsContent>
                        <TabsContent value="activity" class="mt-0">
                            <div class="space-y-4">
                                <h4 class="font-medium">Recent Activity</h4>
                                <div class="space-y-3">
                                    <div class="flex items-center gap-3 text-sm">
                                        <div class="size-2 rounded-full bg-green-500" />
                                        <span class="text-muted-foreground">John completed task "Update dashboard"</span>
                                        <span class="ml-auto text-xs text-muted-foreground">2h ago</span>
                                    </div>
                                    <div class="flex items-center gap-3 text-sm">
                                        <div class="size-2 rounded-full bg-blue-500" />
                                        <span class="text-muted-foreground">Sarah added new member</span>
                                        <span class="ml-auto text-xs text-muted-foreground">4h ago</span>
                                    </div>
                                    <div class="flex items-center gap-3 text-sm">
                                        <div class="size-2 rounded-full bg-yellow-500" />
                                        <span class="text-muted-foreground">Project deadline updated</span>
                                        <span class="ml-auto text-xs text-muted-foreground">1d ago</span>
                                    </div>
                                </div>
                            </div>
                        </TabsContent>
                        <TabsContent value="settings" class="mt-0">
                            <div class="space-y-4">
                                <h4 class="font-medium">Project Settings</h4>
                                <p class="text-sm text-muted-foreground">Configure your project preferences and integrations.</p>
                            </div>
                        </TabsContent>
                        <TabsContent value="members" class="mt-0">
                            <div class="space-y-4">
                                <h4 class="font-medium">Team Members</h4>
                                <p class="text-sm text-muted-foreground">Manage who has access to this project.</p>
                            </div>
                        </TabsContent>
                    </CardContent>
                </Tabs>
            </Card>

            <!-- Full Width Boxed Header -->
            <Card class="overflow-hidden">
                <Tabs default-value="all" variant="boxed">
                    <div class="bg-muted/30 p-4">
                        <TabsList class="w-full">
                            <TabsTrigger value="all">All Items</TabsTrigger>
                            <TabsTrigger value="active">Active</TabsTrigger>
                            <TabsTrigger value="archived">Archived</TabsTrigger>
                        </TabsList>
                    </div>
                    <CardContent class="pt-6">
                        <TabsContent value="all" class="mt-0">
                            <p class="text-sm text-muted-foreground">Showing all 156 items in your collection.</p>
                        </TabsContent>
                        <TabsContent value="active" class="mt-0">
                            <p class="text-sm text-muted-foreground">Showing 89 active items.</p>
                        </TabsContent>
                        <TabsContent value="archived" class="mt-0">
                            <p class="text-sm text-muted-foreground">Showing 67 archived items.</p>
                        </TabsContent>
                    </CardContent>
                </Tabs>
            </Card>

            <!-- Vertical Comparison -->
            <div class="grid grid-cols-3 gap-4">
                <Card>
                    <CardHeader>
                        <CardTitle class="text-base">Underline</CardTitle>
                    </CardHeader>
                    <CardContent>
                        <Tabs default-value="a" variant="underline">
                            <TabsList>
                                <TabsTrigger value="a">Tab A</TabsTrigger>
                                <TabsTrigger value="b">Tab B</TabsTrigger>
                            </TabsList>
                            <TabsContent value="a">
                                <p class="text-sm">Underline content A</p>
                            </TabsContent>
                            <TabsContent value="b">
                                <p class="text-sm">Underline content B</p>
                            </TabsContent>
                        </Tabs>
                    </CardContent>
                </Card>
                <Card>
                    <CardHeader>
                        <CardTitle class="text-base">Pills</CardTitle>
                    </CardHeader>
                    <CardContent>
                        <Tabs default-value="a" variant="pills">
                            <TabsList>
                                <TabsTrigger value="a">Tab A</TabsTrigger>
                                <TabsTrigger value="b">Tab B</TabsTrigger>
                            </TabsList>
                            <TabsContent value="a">
                                <p class="text-sm">Pills content A</p>
                            </TabsContent>
                            <TabsContent value="b">
                                <p class="text-sm">Pills content B</p>
                            </TabsContent>
                        </Tabs>
                    </CardContent>
                </Card>
                <Card>
                    <CardHeader>
                        <CardTitle class="text-base">Boxed</CardTitle>
                    </CardHeader>
                    <CardContent>
                        <Tabs default-value="a" variant="boxed">
                            <TabsList>
                                <TabsTrigger value="a">Tab A</TabsTrigger>
                                <TabsTrigger value="b">Tab B</TabsTrigger>
                            </TabsList>
                            <TabsContent value="a">
                                <p class="text-sm">Boxed content A</p>
                            </TabsContent>
                            <TabsContent value="b">
                                <p class="text-sm">Boxed content B</p>
                            </TabsContent>
                        </Tabs>
                    </CardContent>
                </Card>
            </div>
        </div>
    </LayoutApp>
</template>

<script setup lang="ts">
import { ref } from 'vue';
import { AdminLayout as LayoutApp } from '@/components/app';
import { Card, CardHeader, CardTitle, CardDescription, CardContent } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Tabs, TabsList, TabsTrigger, TabsContent } from '@/components/ui/tabs';
import {
    LayoutDashboard,
    BarChart3,
    FileText,
    Settings,
    User,
    CreditCard,
    Users,
} from 'lucide-vue-next';
import { useShowcaseNav } from '../_composables/useShowcaseNav';

const { sidebarItems } = useShowcaseNav();

const activeTab = ref('tab1');
</script>
