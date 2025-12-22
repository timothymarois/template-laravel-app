<template>
    <LayoutApp
        title="Components - Table"
        pageTitle="Data"
        :pageNavItems="sideNavItems"
        :pageTabs="dataTabs"
    >
        <div class="space-y-6">
            <Card>
                <CardHeader>
                    <CardTitle>Table</CardTitle>
                    <CardDescription>Basic HTML table with consistent styling</CardDescription>
                </CardHeader>
                <CardContent>
                    <Table>
                        <TableHeader>
                            <TableRow>
                                <TableHead class="w-[100px]">ID</TableHead>
                                <TableHead>Name</TableHead>
                                <TableHead>Email</TableHead>
                                <TableHead>Role</TableHead>
                                <TableHead class="text-right">Actions</TableHead>
                            </TableRow>
                        </TableHeader>
                        <TableBody>
                            <TableRow v-for="user in sampleUsers" :key="user.id">
                                <TableCell class="font-medium">{{ user.id }}</TableCell>
                                <TableCell>{{ user.name }}</TableCell>
                                <TableCell>{{ user.email }}</TableCell>
                                <TableCell>
                                    <Badge variant="outline">{{ user.role }}</Badge>
                                </TableCell>
                                <TableCell class="text-right">
                                    <Button variant="ghost" size="icon-sm">
                                        <MoreHorizontal class="size-4" />
                                    </Button>
                                </TableCell>
                            </TableRow>
                        </TableBody>
                    </Table>
                </CardContent>
            </Card>

            <Card>
                <CardHeader>
                    <CardTitle>Table with Selection</CardTitle>
                    <CardDescription>Table with checkbox selection</CardDescription>
                </CardHeader>
                <CardContent>
                    <div class="border rounded-lg">
                        <div class="flex items-center justify-between p-4 border-b bg-muted/30">
                            <div class="flex items-center gap-2">
                                <Input placeholder="Search..." clearable class="w-64" />
                                <Button variant="outline" size="sm">
                                    <Filter class="size-4 mr-1" />
                                    Filter
                                </Button>
                            </div>
                            <Button size="sm">
                                <Plus class="size-4 mr-1" />
                                Add User
                            </Button>
                        </div>
                        <Table>
                            <TableHeader>
                                <TableRow>
                                    <TableHead class="w-12">
                                        <Checkbox />
                                    </TableHead>
                                    <TableHead>Name</TableHead>
                                    <TableHead>Email</TableHead>
                                    <TableHead>Status</TableHead>
                                    <TableHead class="w-16"></TableHead>
                                </TableRow>
                            </TableHeader>
                            <TableBody>
                                <TableRow v-for="user in contextUsers" :key="user.id">
                                    <TableCell>
                                        <Checkbox />
                                    </TableCell>
                                    <TableCell class="font-medium">{{ user.name }}</TableCell>
                                    <TableCell>{{ user.email }}</TableCell>
                                    <TableCell>
                                        <Badge :variant="user.status === 'Active' ? 'default' : 'secondary'">
                                            {{ user.status }}
                                        </Badge>
                                    </TableCell>
                                    <TableCell>
                                        <ButtonMenu :items="rowActions" />
                                    </TableCell>
                                </TableRow>
                            </TableBody>
                        </Table>
                    </div>
                </CardContent>
            </Card>
        </div>
    </LayoutApp>
</template>

<script setup lang="ts">
import { AdminLayout as LayoutApp } from '@/components/app';
import { Card, CardHeader, CardTitle, CardDescription, CardContent } from '@/components/ui/card';
import { Button, ButtonMenu } from '@/components/ui/button';
import { Badge } from '@/components/ui/badge';
import { Input, Checkbox } from '@/components/ui/form';
import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from '@/components/ui/table';
import { MoreHorizontal, Pencil, Trash2, Copy, Filter, Plus } from 'lucide-vue-next';
import { useShowcaseNav } from '../_composables/useShowcaseNav';

const { sideNavItems, dataTabs } = useShowcaseNav();

const sampleUsers = [
    { id: 1, name: 'John Doe', email: 'john@example.com', role: 'Admin' },
    { id: 2, name: 'Jane Smith', email: 'jane@example.com', role: 'Editor' },
    { id: 3, name: 'Bob Wilson', email: 'bob@example.com', role: 'User' },
];

const contextUsers = [
    { id: 1, name: 'John Doe', email: 'john@example.com', status: 'Active' },
    { id: 2, name: 'Jane Smith', email: 'jane@example.com', status: 'Active' },
    { id: 3, name: 'Bob Wilson', email: 'bob@example.com', status: 'Pending' },
];

const rowActions = [
    { label: 'Edit', icon: Pencil, click: () => {} },
    { label: 'Duplicate', icon: Copy, click: () => {} },
    { separator: true },
    { label: 'Delete', icon: Trash2, click: () => {} },
];
</script>
