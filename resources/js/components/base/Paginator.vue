<template>
    <nav class="flex items-center justify-center gap-1" aria-label="Pagination">
        <template v-for="(link, index) in links" :key="index">
            <component
                :is="getLinkComponent(link)"
                v-if="link.url || !link.active"
                :href="link.url"
                :disabled="!link.url"
                :class="[
                    'inline-flex items-center justify-center min-w-9 h-9 px-3 text-sm font-medium rounded-md transition-colors',
                    link.active
                        ? 'bg-primary text-primary-foreground'
                        : 'hover:bg-accent hover:text-accent-foreground',
                    !link.url ? 'opacity-50 pointer-events-none' : 'cursor-pointer'
                ]"
                v-html="link.label"
            />
        </template>
    </nav>
</template>

<script setup lang="ts">
import { computed } from 'vue';

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
</script>
