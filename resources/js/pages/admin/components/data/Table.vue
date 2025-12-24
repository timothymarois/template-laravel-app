<template>
    <LayoutApp
        title="Components - Table"
        pageTitle="Table"
        :pageSidebarItems="sidebarItems"
        pageSidebarTitle="Components"
    >
        <div class="space-y-4">
            <!-- Scrollable Table with Fixed Header -->
            <Card>
                <CardHeader class="border-b">
                    <CardTitle>Users</CardTitle>
                    <CardDescription>Scrollable table with fixed header, sorting, search, pagination, and column customization (500 rows)</CardDescription>
                    <CardAction>
                        <div class="flex items-center gap-2">
                            <Input
                                v-model="userSearch"
                                placeholder="Search users..."
                                clearable
                                class="w-64"
                            />
                            <CustomizeColumns
                                v-model="activeUserColumns"
                                :columns="userColumnDefs"
                                :defaultColumns="defaultUserColumns"
                                :sort="{ column: sortColumn, direction: sortDirection }"
                                :defaultSort="{ column: null, direction: null }"
                                @update:sort="handleSortUpdate"
                            />
                            <Button>
                                <Plus class="size-4 mr-2" />
                                Add User
                            </Button>
                        </div>
                    </CardAction>
                </CardHeader>
                <CardContent class="p-0">
                    <!-- Scrollable container with fixed height -->
                    <div class="max-h-[400px] overflow-auto">
                        <Table>
                            <!-- Sticky header -->
                            <TableHeader class="sticky top-0 bg-background z-10 shadow-sm">
                                <TableRow>
                                    <TableHead class="w-12 text-center">
                                        <Checkbox
                                            :modelValue="headerCheckboxState"
                                            @update:modelValue="toggleAllUsers"
                                        />
                                    </TableHead>
                                    <TableHead
                                        v-if="isUserColumnVisible('name')"
                                        class="cursor-pointer select-none hover:bg-muted/50"
                                        @click="toggleSort('name')"
                                    >
                                        <div class="flex items-center gap-1">
                                            Name
                                            <ArrowUp v-if="sortColumn === 'name' && sortDirection === 'asc'" class="size-4" />
                                            <ArrowDown v-else-if="sortColumn === 'name' && sortDirection === 'desc'" class="size-4" />
                                            <ArrowUpDown v-else class="size-4 text-muted-foreground/50" />
                                        </div>
                                    </TableHead>
                                    <TableHead
                                        v-if="isUserColumnVisible('email')"
                                        class="cursor-pointer select-none hover:bg-muted/50"
                                        @click="toggleSort('email')"
                                    >
                                        <div class="flex items-center gap-1">
                                            Email
                                            <ArrowUp v-if="sortColumn === 'email' && sortDirection === 'asc'" class="size-4" />
                                            <ArrowDown v-else-if="sortColumn === 'email' && sortDirection === 'desc'" class="size-4" />
                                            <ArrowUpDown v-else class="size-4 text-muted-foreground/50" />
                                        </div>
                                    </TableHead>
                                    <TableHead
                                        v-if="isUserColumnVisible('role')"
                                        class="cursor-pointer select-none hover:bg-muted/50"
                                        @click="toggleSort('role')"
                                    >
                                        <div class="flex items-center gap-1">
                                            Role
                                            <ArrowUp v-if="sortColumn === 'role' && sortDirection === 'asc'" class="size-4" />
                                            <ArrowDown v-else-if="sortColumn === 'role' && sortDirection === 'desc'" class="size-4" />
                                            <ArrowUpDown v-else class="size-4 text-muted-foreground/50" />
                                        </div>
                                    </TableHead>
                                    <TableHead
                                        v-if="isUserColumnVisible('department')"
                                        class="cursor-pointer select-none hover:bg-muted/50"
                                        @click="toggleSort('department')"
                                    >
                                        <div class="flex items-center gap-1">
                                            Department
                                            <ArrowUp v-if="sortColumn === 'department' && sortDirection === 'asc'" class="size-4" />
                                            <ArrowDown v-else-if="sortColumn === 'department' && sortDirection === 'desc'" class="size-4" />
                                            <ArrowUpDown v-else class="size-4 text-muted-foreground/50" />
                                        </div>
                                    </TableHead>
                                    <TableHead
                                        v-if="isUserColumnVisible('status')"
                                        class="cursor-pointer select-none hover:bg-muted/50"
                                        @click="toggleSort('status')"
                                    >
                                        <div class="flex items-center gap-1">
                                            Status
                                            <ArrowUp v-if="sortColumn === 'status' && sortDirection === 'asc'" class="size-4" />
                                            <ArrowDown v-else-if="sortColumn === 'status' && sortDirection === 'desc'" class="size-4" />
                                            <ArrowUpDown v-else class="size-4 text-muted-foreground/50" />
                                        </div>
                                    </TableHead>
                                </TableRow>
                            </TableHeader>
                            <TableBody>
                                <TableRow
                                    v-for="user in paginatedUsers"
                                    :key="user.id"
                                    :class="selectedUsers.includes(user.id) ? 'bg-yellow-50 dark:bg-yellow-950/30' : ''"
                                >
                                    <TableCell class="text-center">
                                        <Checkbox
                                            :modelValue="selectedUsers.includes(user.id)"
                                            @update:modelValue="(checked) => toggleUser(user.id, checked)"
                                        />
                                    </TableCell>
                                    <TableCell v-if="isUserColumnVisible('name')" class="font-medium">{{ user.name }}</TableCell>
                                    <TableCell v-if="isUserColumnVisible('email')" class="text-muted-foreground">{{ user.email }}</TableCell>
                                    <TableCell v-if="isUserColumnVisible('role')">
                                        <Badge variant="outline">{{ user.role }}</Badge>
                                    </TableCell>
                                    <TableCell v-if="isUserColumnVisible('department')" class="text-muted-foreground">{{ user.department }}</TableCell>
                                    <TableCell v-if="isUserColumnVisible('status')">
                                        <Badge :class="getStatusClass(user.status)">
                                            {{ user.status }}
                                        </Badge>
                                    </TableCell>
                                </TableRow>
                            </TableBody>
                        </Table>
                    </div>
                </CardContent>
                <CardFooter class="border-t py-4 px-6 justify-between">
                    <span class="text-sm text-muted-foreground">
                        Showing {{ Math.min((currentPage - 1) * itemsPerPage + 1, filteredUsers.length) }}-{{ Math.min(currentPage * itemsPerPage, filteredUsers.length) }} of {{ filteredUsers.length }} users
                        <template v-if="filteredUsers.length !== allUsers.length">
                            (filtered from {{ allUsers.length }})
                        </template>
                    </span>
                    <Pagination
                        v-if="totalPages > 1"
                        v-slot="{ page }"
                        v-model:page="currentPage"
                        :total="filteredUsers.length"
                        :items-per-page="itemsPerPage"
                        class="mx-0 w-auto"
                    >
                        <PaginationContent>
                            <PaginationFirst />
                            <PaginationPrevious />
                            <template v-for="pageNum in totalPages" :key="pageNum">
                                <PaginationItem
                                    v-if="pageNum === 1 || pageNum === totalPages || (pageNum >= currentPage - 1 && pageNum <= currentPage + 1)"
                                    :value="pageNum"
                                    as-child
                                >
                                    <Button
                                        variant="outline"
                                        size="icon-sm"
                                        :class="{ 'bg-primary text-primary-foreground': page === pageNum }"
                                    >
                                        {{ pageNum }}
                                    </Button>
                                </PaginationItem>
                                <PaginationEllipsis
                                    v-else-if="pageNum === 2 || pageNum === totalPages - 1"
                                />
                            </template>
                            <PaginationNext />
                            <PaginationLast />
                        </PaginationContent>
                    </Pagination>
                </CardFooter>
            </Card>

            <!-- Table with Tabs Filter -->
            <Card>
                <Tabs v-model="activeTab" variant="underline">
                    <div class="flex items-center justify-between border-b px-6">
                        <TabsList class="border-b-0">
                            <TabsTrigger v-for="tab in statusTabs" :key="tab.value" :value="tab.value">
                                {{ tab.label }}
                                <Badge variant="secondary" class="ml-2 text-xs">{{ tab.count }}</Badge>
                            </TabsTrigger>
                        </TabsList>
                        <div class="flex items-center gap-2 py-3">
                            <Input placeholder="Search..." clearable class="w-64" />
                            <CustomizeColumns
                                v-model="activeOrderColumns"
                                :columns="orderColumnDefs"
                                :defaultColumns="defaultOrderColumns"
                            />
                            <Button variant="outline">Export</Button>
                        </div>
                    </div>
                    <CardContent class="p-0">
                        <Table>
                            <TableHeader>
                                <TableRow>
                                    <TableHead v-if="isColumnVisible('order')" class="pl-6">Order</TableHead>
                                    <TableHead v-if="isColumnVisible('customer')">Customer</TableHead>
                                    <TableHead v-if="isColumnVisible('date')">Date</TableHead>
                                    <TableHead v-if="isColumnVisible('status')">Status</TableHead>
                                    <TableHead v-if="isColumnVisible('amount')" class="text-right pr-6">Amount</TableHead>
                                </TableRow>
                            </TableHeader>
                            <TableBody>
                                <TableRow v-for="order in orders" :key="order.id">
                                    <TableCell v-if="isColumnVisible('order')" class="pl-6 font-medium">{{ order.id }}</TableCell>
                                    <TableCell v-if="isColumnVisible('customer')">{{ order.customer }}</TableCell>
                                    <TableCell v-if="isColumnVisible('date')" class="text-muted-foreground">{{ order.date }}</TableCell>
                                    <TableCell v-if="isColumnVisible('status')">
                                        <Badge :class="getOrderStatusClass(order.status)">
                                            {{ order.status }}
                                        </Badge>
                                    </TableCell>
                                    <TableCell v-if="isColumnVisible('amount')" class="text-right pr-6 font-medium">{{ order.amount }}</TableCell>
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
                </Tabs>
            </Card>

            <!-- Compact Table -->
            <Card>
                <CardHeader class="border-b">
                    <CardTitle>Recent Activity</CardTitle>
                    <CardAction>
                        <Button variant="ghost">View All</Button>
                    </CardAction>
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
import { ref, computed, watch } from 'vue';
import { AppLayout as LayoutApp } from '@/components/app';
import { Card, CardHeader, CardTitle, CardDescription, CardContent, CardFooter, CardAction } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Badge } from '@/components/ui/badge';
import { Input } from '@/components/ui/input';
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
import { CustomizeColumns } from '@/components/ui/data-table';
import { Tabs, TabsList, TabsTrigger } from '@/components/ui/tabs';
import { Plus, ArrowUp, ArrowDown, ArrowUpDown } from 'lucide-vue-next';
import { useShowcaseNav } from '../_composables/useShowcaseNav';

const { sidebarItems } = useShowcaseNav();

// ============================================
// First Table: Scrollable with Fixed Header
// ============================================

// Generate 100 users dynamically
const firstNames = ['John', 'Jane', 'Bob', 'Alice', 'Charlie', 'Diana', 'Edward', 'Fiona', 'George', 'Hannah'];
const lastNames = ['Smith', 'Johnson', 'Williams', 'Brown', 'Jones', 'Davis', 'Miller', 'Wilson', 'Moore', 'Taylor'];
const roles = ['Admin', 'Editor', 'User', 'Viewer'];
const departments = ['Engineering', 'Design', 'Marketing', 'Sales', 'Support', 'HR', 'Finance', 'Operations'];
const statuses = ['Active', 'Pending', 'Inactive'];

const allUsers = Array.from({ length: 500 }, (_, i) => ({
    id: i + 1,
    name: `${firstNames[i % firstNames.length]} ${lastNames[Math.floor(i / firstNames.length) % lastNames.length]}`,
    email: `user${i + 1}@example.com`,
    role: roles[i % roles.length],
    department: departments[i % departments.length],
    status: statuses[i % statuses.length],
}));

// Pagination state
const currentPage = ref(1);
const itemsPerPage = 100;

// Sorting state
type SortDirection = 'asc' | 'desc' | null;
const sortColumn = ref<string | null>(null);
const sortDirection = ref<SortDirection>(null);

const toggleSort = (column: string) => {
    if (sortColumn.value === column) {
        // Cycle through: asc -> desc -> null
        if (sortDirection.value === 'asc') {
            sortDirection.value = 'desc';
        } else if (sortDirection.value === 'desc') {
            sortColumn.value = null;
            sortDirection.value = null;
        }
    } else {
        sortColumn.value = column;
        sortDirection.value = 'asc';
    }
    // Reset to first page when sorting changes
    currentPage.value = 1;
};

// Handle sort update from CustomizeColumns (e.g., reset to default)
const handleSortUpdate = (sort: { column: string | null; direction: SortDirection }) => {
    sortColumn.value = sort.column;
    sortDirection.value = sort.direction;
    currentPage.value = 1;
};

// Search state
const userSearch = ref('');

// Column customization
const userColumnDefs = [
    { key: 'name', header: 'Name' },
    { key: 'email', header: 'Email' },
    { key: 'role', header: 'Role' },
    { key: 'department', header: 'Department' },
    { key: 'status', header: 'Status' },
];
const defaultUserColumns = ['name', 'email', 'role', 'status'];
const activeUserColumns = ref([...defaultUserColumns]);

const isUserColumnVisible = (key: string) => activeUserColumns.value.includes(key);

// Filtered users based on search
const filteredUsers = computed(() => {
    let result = allUsers;

    // Apply search filter
    if (userSearch.value.trim()) {
        const search = userSearch.value.toLowerCase();
        result = result.filter(user =>
            user.name.toLowerCase().includes(search) ||
            user.email.toLowerCase().includes(search) ||
            user.role.toLowerCase().includes(search) ||
            user.department.toLowerCase().includes(search) ||
            user.status.toLowerCase().includes(search)
        );
    }

    // Apply sorting
    if (sortColumn.value && sortDirection.value) {
        const col = sortColumn.value as keyof typeof allUsers[0];
        const dir = sortDirection.value === 'asc' ? 1 : -1;
        result = [...result].sort((a, b) => {
            const aVal = String(a[col]).toLowerCase();
            const bVal = String(b[col]).toLowerCase();
            return aVal.localeCompare(bVal) * dir;
        });
    }

    return result;
});

// Reset to first page when search changes
watch(userSearch, () => {
    currentPage.value = 1;
});

// Paginated users for display
const paginatedUsers = computed(() => {
    const start = (currentPage.value - 1) * itemsPerPage;
    const end = start + itemsPerPage;
    return filteredUsers.value.slice(start, end);
});

// Total pages
const totalPages = computed(() => Math.ceil(filteredUsers.value.length / itemsPerPage));

// Selection state
const selectedUsers = ref<number[]>([]);

const headerCheckboxState = computed(() => {
    if (selectedUsers.value.length === 0) return false;
    if (selectedUsers.value.length === filteredUsers.value.length) return true;
    return 'indeterminate' as const;
});

const toggleAllUsers = () => {
    if (selectedUsers.value.length > 0) {
        selectedUsers.value = [];
    } else {
        selectedUsers.value = filteredUsers.value.map(u => u.id);
    }
};

const toggleUser = (id: number, checked: boolean | 'indeterminate') => {
    if (checked === true) {
        selectedUsers.value = [...selectedUsers.value, id];
    } else {
        selectedUsers.value = selectedUsers.value.filter(uid => uid !== id);
    }
};

// ============================================
// Second Table: Tabs Filter
// ============================================

const activeTab = ref('all');

const statusTabs = [
    { label: 'All', value: 'all', count: 24 },
    { label: 'Completed', value: 'completed', count: 12 },
    { label: 'Pending', value: 'pending', count: 8 },
    { label: 'Cancelled', value: 'cancelled', count: 4 },
];

const orders = [
    { id: '#ORD-001', customer: 'John Doe', date: 'Dec 20, 2024', status: 'Completed', amount: '$250.00' },
    { id: '#ORD-002', customer: 'Jane Smith', date: 'Dec 19, 2024', status: 'Processing', amount: '$125.00' },
    { id: '#ORD-003', customer: 'Bob Wilson', date: 'Dec 18, 2024', status: 'Pending', amount: '$89.00' },
    { id: '#ORD-004', customer: 'Alice Brown', date: 'Dec 17, 2024', status: 'Completed', amount: '$320.00' },
    { id: '#ORD-005', customer: 'Charlie Davis', date: 'Dec 16, 2024', status: 'Cancelled', amount: '$45.00' },
];

const orderColumnDefs = [
    { key: 'order', header: 'Order' },
    { key: 'customer', header: 'Customer' },
    { key: 'date', header: 'Date' },
    { key: 'status', header: 'Status' },
    { key: 'amount', header: 'Amount' },
];

const defaultOrderColumns = ['order', 'customer', 'date', 'status', 'amount'];
const activeOrderColumns = ref([...defaultOrderColumns]);

const isColumnVisible = (key: string) => activeOrderColumns.value.includes(key);

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
