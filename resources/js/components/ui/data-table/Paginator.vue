<template>
    <nav class="flex items-center justify-center gap-1" aria-label="Pagination">
        <template v-for="(link, index) in links" :key="index">
            <!-- Previous Button -->
            <component
                v-if="isPrevious(link, index)"
                :is="getLinkComponent(link)"
                :href="link.url"
                :disabled="!link.url"
                :class="[
                    'inline-flex items-center justify-center size-9 rounded-md transition-colors',
                    'hover:bg-accent hover:text-accent-foreground',
                    !link.url ? 'opacity-40 pointer-events-none' : 'cursor-pointer'
                ]"
                :aria-label="link.label"
            >
                <ChevronLeft class="size-4" />
            </component>

            <!-- Next Button -->
            <component
                v-else-if="isNext(link, index)"
                :is="getLinkComponent(link)"
                :href="link.url"
                :disabled="!link.url"
                :class="[
                    'inline-flex items-center justify-center size-9 rounded-md transition-colors',
                    'hover:bg-accent hover:text-accent-foreground',
                    !link.url ? 'opacity-40 pointer-events-none' : 'cursor-pointer'
                ]"
                :aria-label="link.label"
            >
                <ChevronRight class="size-4" />
            </component>

            <!-- Page Numbers -->
            <component
                v-else-if="link.url || link.active"
                :is="getLinkComponent(link)"
                :href="link.url"
                :disabled="!link.url"
                :class="[
                    'inline-flex items-center justify-center min-w-9 h-9 px-3 text-sm font-medium rounded-md transition-colors',
                    link.active
                        ? 'bg-primary text-primary-foreground shadow-sm'
                        : 'hover:bg-accent hover:text-accent-foreground',
                    !link.url && !link.active ? 'opacity-40 pointer-events-none' : 'cursor-pointer'
                ]"
            >
                {{ getPageNumber(link.label) }}
            </component>

            <!-- Ellipsis -->
            <span
                v-else-if="isEllipsis(link.label)"
                class="inline-flex items-center justify-center min-w-9 h-9 px-2 text-sm text-muted-foreground"
            >
                <MoreHorizontal class="size-4" />
            </span>
        </template>
    </nav>
</template>

<script setup lang="ts">
import { ChevronLeft, ChevronRight, MoreHorizontal } from 'lucide-vue-next';
import {
    getPaginationLinkComponent,
    getPageNumberLabel,
    isEllipsisLabel,
    isNextLink,
    isPreviousLink,
    type PaginationLink,
} from '@/components/ui/pagination/paginatorUtils';

interface Props {
    links?: PaginationLink[];
    linkComponent?: string | object;
}

const props = withDefaults(defineProps<Props>(), {
    links: () => [],
    linkComponent: 'a',
});

const getLinkComponent = (link: PaginationLink) =>
    getPaginationLinkComponent(link, props.linkComponent);

const isPrevious = (link: PaginationLink, index: number) =>
    isPreviousLink(link, index);

const isNext = (link: PaginationLink, index: number) =>
    isNextLink(link, index, props.links.length);

const isEllipsis = (label: string) =>
    isEllipsisLabel(label);

const getPageNumber = (label: string) =>
    getPageNumberLabel(label);
</script>
