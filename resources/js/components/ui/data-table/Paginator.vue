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

interface PaginationLink {
    url: string | null;
    label: string;
    active: boolean;
}

interface Props {
    links: PaginationLink[];
    linkComponent?: string | object;
}

const props = withDefaults(defineProps<Props>(), {
    links: () => [],
    linkComponent: 'a',
});

const getLinkComponent = (link: PaginationLink) => {
    if (!link.url) return 'span';
    return props.linkComponent;
};

const isPrevious = (link: PaginationLink, index: number) => {
    return index === 0 && (
        link.label.toLowerCase().includes('previous') ||
        link.label.includes('&laquo;')
    );
};

const isNext = (link: PaginationLink, index: number) => {
    return index === props.links.length - 1 && (
        link.label.toLowerCase().includes('next') ||
        link.label.includes('&raquo;')
    );
};

const isEllipsis = (label: string) => {
    return label === '...' || label.includes('&hellip;');
};

const getPageNumber = (label: string) => {
    // Strip any HTML entities and return clean number
    return label.replace(/&[^;]+;/g, '').trim();
};
</script>
