<template>
    <div
        v-bind="bindProps"
        :class="mergedPt.root.class"
    >
        <component
            v-for="link in links"
            :key="link.label"
            :is="linkComponent"
            :href="link.url ?? ''"
            v-html="link.label"
            preserve-scroll
            :class="[mergedPt.link.class, {
                'bg-gray-200 dark:bg-gray-600 dark:text-white text-gray-900 font-semibold': link.active,
                '!text-gray-300': !link.url
            }]"
        />
    </div>
</template>

<script setup lang="ts">
import { useAttrs, ref } from 'vue';
import { usePrimeBindings } from '../composables';


interface PaginationLink {
    label: string;
    url?: string | null;
    active?: boolean;
}

interface PaginationPassThroughOptions {
    root?: any;
    link?: any;
}

interface Props {
    links: PaginationLink[];
    linkComponent?: string | object;
    pt?: PaginationPassThroughOptions;
}

const props = withDefaults(defineProps<Props>(), {
    linkComponent: 'a',
});
const attrs = useAttrs();

const theme = ref<PaginationPassThroughOptions>({
    root: 'flex items-center justify-center flex-wrap gap-1',
    link: 'flex items-center justify-center px-3 py-2 text-sm rounded-lg text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700',
});

const { bindProps, mergedPt } = usePrimeBindings(props, attrs, theme);

</script>
