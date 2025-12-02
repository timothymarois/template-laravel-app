<template>
    <Paginator
        unstyled
        v-bind="bindProps"
        :pt="mergedPt"
        :ptOptions="{ mergeProps: ptViewMerge }"
    >
        <template #container="{ page, pageCount, pageLinks, changePageCallback, firstPageCallback, lastPageCallback, prevPageCallback, nextPageCallback }">
            <div class="flex flex-wrap gap-2 items-center justify-center">
                <Button text @click="firstPageCallback" :disabled="page === 0">
                    <template #icon>
                        <AngleDoubleLeftIcon />
                    </template>
                </Button>
                <Button text @click="prevPageCallback" :disabled="page === 0">
                    <template #icon>
                        <AngleLeftIcon />
                    </template>
                </Button>
                <div class="items-center justify-center gap-2 hidden sm:flex">
                    <Button v-for="pageLink of pageLinks" :key="pageLink" :text="page + 1 !== pageLink" @click="() => changePageCallback(pageLink - 1)" :class="['shrink-0 min-w-10 h-10', { 'bg-highlight!': page + 1 === pageLink }]">
                        {{ pageLink }}
                    </Button>
                </div>
                <Button text @click="nextPageCallback" :disabled="page === pageCount! - 1">
                    <template #icon>
                        <AngleRightIcon />
                    </template>
                </Button>
                <Button text @click="lastPageCallback" :disabled="page === pageCount! - 1">
                    <template #icon>
                        <AngleDoubleRightIcon />
                    </template>
                </Button>
            </div>
        </template>
        <template v-for="(_, slotName) in $slots" v-slot:[slotName]="slotProps">
            <slot :name="slotName" v-bind="slotProps ?? {}" />
        </template>
    </Paginator>
</template>

<script setup lang="ts">
import AngleDoubleLeftIcon from '@primevue/icons/angledoubleleft';
import AngleDoubleRightIcon from '@primevue/icons/angledoubleright';
import AngleLeftIcon from '@primevue/icons/angleleft';
import AngleRightIcon from '@primevue/icons/angleright';
import Paginator, { type PaginatorPassThroughOptions, type PaginatorProps } from 'primevue/paginator';
import Button from './Button.vue';
import { ref, useAttrs } from 'vue';
import { ptViewMerge } from '../utils';
import { usePrimeBindings } from '../composables';

interface Props extends /* @vue-ignore */ PaginatorProps {}
const props = defineProps<Props>();
const attrs = useAttrs();

const theme = ref<PaginatorPassThroughOptions>({
    root: `flex items-center justify-center flex-wrap py-2 px-4 rounded-[var(--p-content-border-radius)] gap-1
        bg-surface-0 dark:bg-surface-900 text-surface-700 dark:text-surface-0`
});

const { bindProps, mergedPt } = usePrimeBindings(props, attrs, theme);

</script>
