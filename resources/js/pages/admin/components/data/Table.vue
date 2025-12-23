<template>
    <LayoutApp
        title="Components - Table"
        pageTitle="Data"
        :pageNavItems="sideNavItems"
        :pageTabs="dataTabs"
    >
        <div class="space-y-4">
            <!-- Basic Table in Card -->
            <Card>
                <CardHeader class="flex flex-row items-center justify-between space-y-0 border-b">
                    <div class="space-y-1">
                        <CardTitle>Users</CardTitle>
                        <CardDescription>Manage your team members</CardDescription>
                    </div>
                    <div class="flex items-center gap-2">
                        <Input placeholder="Search users..." clearable class="w-64" />
                        <Button variant="outline">
                            <Filter class="size-4" />
                        </Button>
                        <Button>
                            <Plus class="size-4 mr-2" />
                            Add User
                        </Button>
                    </div>
                </CardHeader>
                <CardContent class="p-0">
                    <Table>
                        <TableHeader>
                            <TableRow>
                                <TableHead class="w-12 text-center">
                                    <Checkbox
                                        :checked="headerCheckboxState"
                                        @update:checked="toggleAllUsers"
                                    />
                                </TableHead>
                                <TableHead>Name</TableHead>
                                <TableHead>Email</TableHead>
                                <TableHead>Role</TableHead>
                                <TableHead>Status</TableHead>
                            </TableRow>
                        </TableHeader>
                        <TableBody>
                            <TableRow
                                v-for="user in paginatedUsers"
                                :key="user.id"
                                :data-state="selectedUsers.includes(user.id) ? 'selected' : undefined"
                            >
                                <TableCell class="text-center">
                                    <Checkbox
                                        :checked="selectedUsers.includes(user.id)"
                                        @update:checked="(checked) => toggleUser(user.id, checked)"
                                    />
                                </TableCell>
                                <TableCell class="font-medium">{{ user.name }}</TableCell>
                                <TableCell class="text-muted-foreground">{{ user.email }}</TableCell>
                                <TableCell>
                                    <Badge variant="outline">{{ user.role }}</Badge>
                                </TableCell>
                                <TableCell>
                                    <Badge :class="getStatusClass(user.status)">
                                        {{ user.status }}
                                    </Badge>
                                </TableCell>
                            </TableRow>
                        </TableBody>
                    </Table>
                </CardContent>
                <CardFooter class="border-t py-4 px-6 justify-between">
                    <span class="text-sm text-muted-foreground">Showing 1-5 of {{ allUsers.length }} users</span>
                    <Pagination v-slot="{ page }" :total="allUsers.length" :items-per-page="5" :default-page="1" class="mx-0 w-auto">
                        <PaginationContent>
                            <PaginationPrevious />
                            <PaginationItem v-for="(item, index) in 3" :key="index" :value="item" as-child>
                                <Button variant="outline" size="icon-sm" :class="{ 'bg-primary text-primary-foreground': page === item }">
                                    {{ item }}
                                </Button>
                            </PaginationItem>
                            <PaginationNext />
                        </PaginationContent>
                    </Pagination>
                </CardFooter>
            </Card>

            <!-- Table with Tabs Filter -->
            <Card>
                <CardHeader class="flex flex-row items-center justify-between space-y-0 border-b pb-0">
                    <div class="flex items-center gap-4">
                        <button
                            v-for="tab in statusTabs"
                            :key="tab.value"
                            @click="activeTab = tab.value"
                            :class="[
                                'px-1 py-3 text-sm font-medium border-b-2 -mb-px transition-colors',
                                activeTab === tab.value
                                    ? 'border-primary text-foreground'
                                    : 'border-transparent text-muted-foreground hover:text-foreground'
                            ]"
                        >
                            {{ tab.label }}
                            <Badge variant="secondary" class="ml-2 text-xs">{{ tab.count }}</Badge>
                        </button>
                    </div>
                    <div class="flex items-center gap-2 pb-3">
                        <Input placeholder="Search..." clearable class="w-64" />
                        <Button variant="outline">Export</Button>
                    </div>
                </CardHeader>
                <CardContent class="p-0">
                    <Table>
                        <TableHeader>
                            <TableRow>
                                <TableHead class="pl-6">Order</TableHead>
                                <TableHead>Customer</TableHead>
                                <TableHead>Date</TableHead>
                                <TableHead>Status</TableHead>
                                <TableHead class="text-right pr-6">Amount</TableHead>
                            </TableRow>
                        </TableHeader>
                        <TableBody>
                            <TableRow v-for="order in orders" :key="order.id">
                                <TableCell class="pl-6 font-medium">{{ order.id }}</TableCell>
                                <TableCell>{{ order.customer }}</TableCell>
                                <TableCell class="text-muted-foreground">{{ order.date }}</TableCell>
                                <TableCell>
                                    <Badge :class="getOrderStatusClass(order.status)">
                                        {{ order.status }}
                                    </Badge>
                                </TableCell>
                                <TableCell class="text-right pr-6 font-medium">{{ order.amount }}</TableCell>
                            </TableRow>
                        </TableBody>
                    </Table>
                </CardContent>
                <CardFooter class="border-t py-4 px-6 justify-between">
                    <span class="text-sm text-muted-foreground">Showing 1-10 of 50 orders</span>
                    <Pagination v-slot="{ page }" :total="50" :items-per-page="10" :default-page="1" class="mx-0 w-auto">
                        <PaginationContent>
                            <PaginationFirst />
                            <PaginationPrevious />
                            <PaginationItem :value="1" as-child>
                                <Button variant="outline" size="icon-sm" :class="{ 'bg-primary text-primary-foreground': page === 1 }">1</Button>
                            </PaginationItem>
                            <PaginationItem :value="2" as-child>
                                <Button variant="outline" size="icon-sm" :class="{ 'bg-primary text-primary-foreground': page === 2 }">2</Button>
                            </PaginationItem>
                            <PaginationEllipsis />
                            <PaginationItem :value="5" as-child>
                                <Button variant="outline" size="icon-sm" :class="{ 'bg-primary text-primary-foreground': page === 5 }">5</Button>
                            </PaginationItem>
                            <PaginationNext />
                            <PaginationLast />
                        </PaginationContent>
                    </Pagination>
                </CardFooter>
            </Card>

            <!-- Compact Table -->
            <Card>
                <CardHeader class="flex flex-row items-center justify-between space-y-0 border-b">
                    <CardTitle>Recent Activity</CardTitle>
                    <Button variant="ghost">View All</Button>
                </CardHeader>
                <CardContent class="p-0">
                    <Table>
                        <TableBody>
                            <TableRow v-for="activity in activities" :key="activity.id">
                                <TableCell class="pl-6 w-10">
                                    <div :class="['size-2 rounded-full', activity.color]"></div>
                                </TableCell>
                                <TableCell class="font-medium">{{ activity.action }}</TableCell>
                                <TableCell class="text-muted-foreground">{{ activity.user }}</TableCell>
                                <TableCell class="text-right pr-6 text-muted-foreground text-sm">{{ activity.time }}</TableCell>
                            </TableRow>
                        </TableBody>
                    </Table>
                </CardContent>
            </Card>
        </div>
    </LayoutApp>
</template>

<script setup lang="ts">
import { ref, computed } from 'vue';
import { AdminLayout as LayoutApp } from '@/components/app';
import { Card, CardHeader, CardTitle, CardDescription, CardContent, CardFooter } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Badge } from '@/components/ui/badge';
import { Input } from '@/components/ui/form';
import { Checkbox } from '@/components/ui/checkbox';
import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from '@/components/ui/table';
import {
    Pagination,
    PaginationContent,
    PaginationEllipsis,
    PaginationFirst,
    PaginationItem,
    PaginationLast,
    PaginationNext,
    PaginationPrevious,
} from '@/components/ui/pagination';
import { Filter, Plus } from 'lucide-vue-next';
import { useShowcaseNav } from '../_composables/useShowcaseNav';

const { sideNavItems, dataTabs } = useShowcaseNav();

const activeTab = ref('all');
const selectedUsers = ref<number[]>([]);

const headerCheckboxState = computed(() => {
    if (selectedUsers.value.length === 0) return false;
    if (selectedUsers.value.length === paginatedUsers.length) return true;
    return 'indeterminate' as const;
});

const toggleAllUsers = () => {
    // If any are selected, deselect all. Otherwise, select all.
    if (selectedUsers.value.length > 0) {
        selectedUsers.value = [];
    } else {
        selectedUsers.value = paginatedUsers.map(u => u.id);
    }
};

const toggleUser = (id: number, checked: boolean | 'indeterminate') => {
    if (checked === true) {
        selectedUsers.value = [...selectedUsers.value, id];
    } else {
        selectedUsers.value = selectedUsers.value.filter(uid => uid !== id);
    }
};

const statusTabs = [
    { label: 'All', value: 'all', count: 24 },
    { label: 'Completed', value: 'completed', count: 12 },
    { label: 'Pending', value: 'pending', count: 8 },
    { label: 'Cancelled', value: 'cancelled', count: 4 },
];

const allUsers = [
    { id: 1, name: 'John Doe', email: 'john@example.com', role: 'Admin', status: 'Active' },
    { id: 2, name: 'Jane Smith', email: 'jane@example.com', role: 'Editor', status: 'Active' },
    { id: 3, name: 'Bob Wilson', email: 'bob@example.com', role: 'User', status: 'Pending' },
    { id: 4, name: 'Alice Brown', email: 'alice@example.com', role: 'Editor', status: 'Active' },
    { id: 5, name: 'Charlie Davis', email: 'charlie@example.com', role: 'User', status: 'Inactive' },
];

const paginatedUsers = allUsers.slice(0, 5);

const orders = [
    { id: '#ORD-001', customer: 'John Doe', date: 'Dec 20, 2024', status: 'Completed', amount: '$250.00' },
    { id: '#ORD-002', customer: 'Jane Smith', date: 'Dec 19, 2024', status: 'Processing', amount: '$125.00' },
    { id: '#ORD-003', customer: 'Bob Wilson', date: 'Dec 18, 2024', status: 'Pending', amount: '$89.00' },
    { id: '#ORD-004', customer: 'Alice Brown', date: 'Dec 17, 2024', status: 'Completed', amount: '$320.00' },
    { id: '#ORD-005', customer: 'Charlie Davis', date: 'Dec 16, 2024', status: 'Cancelled', amount: '$45.00' },
];

const activities = [
    { id: 1, action: 'Created new project', user: 'John Doe', time: '2 min ago', color: 'bg-green-500' },
    { id: 2, action: 'Updated settings', user: 'Jane Smith', time: '5 min ago', color: 'bg-blue-500' },
    { id: 3, action: 'Deleted file', user: 'Bob Wilson', time: '10 min ago', color: 'bg-red-500' },
    { id: 4, action: 'Invited team member', user: 'Alice Brown', time: '15 min ago', color: 'bg-purple-500' },
];

const getStatusClass = (status: string) => {
    switch (status) {
        case 'Active': return 'bg-green-500 hover:bg-green-500/80 border-transparent text-white';
        case 'Pending': return 'bg-yellow-500 hover:bg-yellow-500/80 border-transparent text-white';
        case 'Inactive': return 'bg-gray-500 hover:bg-gray-500/80 border-transparent text-white';
        default: return '';
    }
};

const getOrderStatusClass = (status: string) => {
    switch (status) {
        case 'Completed': return 'bg-green-500 hover:bg-green-500/80 border-transparent text-white';
        case 'Processing': return 'bg-blue-500 hover:bg-blue-500/80 border-transparent text-white';
        case 'Pending': return 'bg-yellow-500 hover:bg-yellow-500/80 border-transparent text-white';
        case 'Cancelled': return 'bg-red-500 hover:bg-red-500/80 border-transparent text-white';
        default: return '';
    }
};
</script>
