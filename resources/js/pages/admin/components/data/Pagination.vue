<template>
    <LayoutApp
        title="Components - Pagination"
        pageTitle="Pagination"
        :pageSidebarItems="sidebarItems"
        pageSidebarTitle="Components"
    >
        <div class="space-y-4">
            <!-- Laravel Paginator -->
            <Card>
                <CardHeader>
                    <CardTitle>Laravel Paginator</CardTitle>
                    <CardDescription>Server-side pagination using Laravel's pagination links</CardDescription>
                </CardHeader>
                <CardContent class="space-y-4">
                    <div>
                        <h4 class="text-sm font-medium mb-3">Paginator (Laravel-style links)</h4>
                        <Paginator :links="paginationLinks" />
                    </div>

                    <div>
                        <h4 class="text-sm font-medium mb-3">With Per-Page Selector</h4>
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <span class="text-sm text-muted-foreground">Show</span>
                                <Select v-model="perPage" :options="perPageOptions" class="w-20" />
                                <span class="text-sm text-muted-foreground">per page</span>
                            </div>
                            <Paginator :links="paginationLinks" />
                        </div>
                    </div>

                    <div>
                        <h4 class="text-sm font-medium mb-3">Pagination States</h4>
                        <div class="space-y-4">
                            <div>
                                <p class="text-xs text-muted-foreground mb-2">First page (no previous)</p>
                                <Paginator :links="firstPageLinks" />
                            </div>
                            <div>
                                <p class="text-xs text-muted-foreground mb-2">Last page (no next)</p>
                                <Paginator :links="lastPageLinks" />
                            </div>
                        </div>
                    </div>
                </CardContent>
            </Card>

            <!-- Computed Pagination -->
            <Card>
                <CardHeader>
                    <CardTitle>Computed Pagination</CardTitle>
                    <CardDescription>Client-side pagination for in-memory data ({{ totalItems }} items, {{ itemsPerPage }} per page)</CardDescription>
                </CardHeader>
                <CardContent class="space-y-6">
                    <div>
                        <h4 class="text-sm font-medium mb-3">Basic Usage</h4>
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-muted-foreground">
                                Showing {{ (currentPage - 1) * itemsPerPage + 1 }}-{{ Math.min(currentPage * itemsPerPage, totalItems) }} of {{ totalItems }} items
                            </span>
                            <Pagination
                                v-slot="{ page }"
                                v-model:page="currentPage"
                                :total="totalItems"
                                :items-per-page="itemsPerPage"
                                class="mx-0 w-auto"
                            >
                                <PaginationContent>
                                    <PaginationFirst />
                                    <PaginationPrevious />
                                    <template v-for="pageNum in computedTotalPages" :key="pageNum">
                                        <PaginationItem
                                            v-if="shouldShowPage(pageNum)"
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
                                            v-else-if="pageNum === 2 || pageNum === computedTotalPages - 1"
                                        />
                                    </template>
                                    <PaginationNext />
                                    <PaginationLast />
                                </PaginationContent>
                            </Pagination>
                        </div>
                    </div>

                    <div>
                        <h4 class="text-sm font-medium mb-3">With Per-Page Selector</h4>
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <span class="text-sm text-muted-foreground">Show</span>
                                <Select v-model="computedPerPage" :options="computedPerPageOptions" class="w-20" />
                                <span class="text-sm text-muted-foreground">per page</span>
                            </div>
                            <Pagination
                                v-slot="{ page }"
                                v-model:page="currentPage"
                                :total="totalItems"
                                :items-per-page="itemsPerPage"
                                class="mx-0 w-auto"
                            >
                                <PaginationContent>
                                    <PaginationFirst />
                                    <PaginationPrevious />
                                    <template v-for="pageNum in computedTotalPages" :key="pageNum">
                                        <PaginationItem
                                            v-if="shouldShowPage(pageNum)"
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
                                            v-else-if="pageNum === 2 || pageNum === computedTotalPages - 1"
                                        />
                                    </template>
                                    <PaginationNext />
                                    <PaginationLast />
                                </PaginationContent>
                            </Pagination>
                        </div>
                    </div>

                    <div>
                        <h4 class="text-sm font-medium mb-3">Paginated Data Preview</h4>
                        <div class="border rounded-md">
                            <div class="grid grid-cols-5 gap-2 p-3">
                                <div
                                    v-for="item in paginatedItems"
                                    :key="item"
                                    class="bg-muted rounded px-3 py-2 text-center text-sm"
                                >
                                    Item {{ item }}
                                </div>
                            </div>
                        </div>
                    </div>
                </CardContent>
            </Card>
        </div>
    </LayoutApp>
</template>

<script setup lang="ts">
import { ref, computed, watch } from 'vue';
import { AppLayout as LayoutApp } from '@/components/app';
import { Card, CardHeader, CardTitle, CardDescription, CardContent } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Select } from '@/components/ui/select-popover';
import { Paginator } from '@/components/ui/data-table';
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
import { useShowcaseNav } from '../_composables/useShowcaseNav';

const { sidebarItems } = useShowcaseNav();

// ============================================
// Laravel Paginator Examples
// ============================================

const perPage = ref('10');

const perPageOptions = [
    { label: '5', value: '5' },
    { label: '10', value: '10' },
    { label: '25', value: '25' },
    { label: '50', value: '50' },
];

const paginationLinks = [
    { url: null, label: '&laquo; Previous', active: false },
    { url: '#', label: '1', active: false },
    { url: '#', label: '2', active: true },
    { url: '#', label: '3', active: false },
    { url: '#', label: '4', active: false },
    { url: '#', label: '5', active: false },
    { url: '#', label: 'Next &raquo;', active: false },
];

const firstPageLinks = [
    { url: null, label: '&laquo; Previous', active: false },
    { url: '#', label: '1', active: true },
    { url: '#', label: '2', active: false },
    { url: '#', label: '3', active: false },
    { url: '#', label: 'Next &raquo;', active: false },
];

const lastPageLinks = [
    { url: '#', label: '&laquo; Previous', active: false },
    { url: '#', label: '1', active: false },
    { url: '#', label: '2', active: false },
    { url: '#', label: '3', active: true },
    { url: null, label: 'Next &raquo;', active: false },
];

// ============================================
// Computed Pagination Examples
// ============================================

const totalItems = 87;
const allItems = Array.from({ length: totalItems }, (_, i) => i + 1);

const currentPage = ref(1);
const computedPerPage = ref('10');

const computedPerPageOptions = [
    { label: '5', value: '5' },
    { label: '10', value: '10' },
    { label: '25', value: '25' },
    { label: '50', value: '50' },
];

const itemsPerPage = computed(() => parseInt(computedPerPage.value, 10));

const computedTotalPages = computed(() => Math.ceil(totalItems / itemsPerPage.value));

const paginatedItems = computed(() => {
    const start = (currentPage.value - 1) * itemsPerPage.value;
    const end = start + itemsPerPage.value;
    return allItems.slice(start, end);
});

// Reset to page 1 when items per page changes
watch(computedPerPage, () => {
    currentPage.value = 1;
});

// Helper to determine which page numbers to show
const shouldShowPage = (pageNum: number) => {
    return pageNum === 1 ||
        pageNum === computedTotalPages.value ||
        (pageNum >= currentPage.value - 1 && pageNum <= currentPage.value + 1);
};
</script>
