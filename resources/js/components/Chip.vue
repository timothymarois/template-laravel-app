<template>
    <Chip
        unstyled
        v-bind="bindProps"
        :pt="mergedPt"
        :ptOptions="{ mergeProps: ptViewMerge }"
    >
        <template #removeicon="{ removeCallback, keydownCallback }">
            <TimesCircleIcon
                class="cursor-pointer text-base w-4 h-4 rounded-full text-white dark:text-surface-0 focus-visible:outline focus-visible:outline-offset-2 focus-visible:outline-primary"
                @click="removeCallback"
                @keydown="keydownCallback"
            />
        </template>
        <template v-for="(_, slotName) in $slots" v-slot:[slotName]="slotProps">
            <slot :name="slotName" v-bind="slotProps ?? {}" />
        </template>
    </Chip>
</template>

<script setup lang="ts">
import TimesCircleIcon from '@primevue/icons/timescircle';
import Chip, { type ChipPassThroughOptions, type ChipProps } from 'primevue/chip';
import { ref, useAttrs } from 'vue';
import { ptViewMerge } from '../utils';
import { usePrimeBindings } from '../composables';

interface Props extends /* @vue-ignore */ ChipProps {}
const props = defineProps<Props>();
const attrs = useAttrs();

const theme = ref<ChipPassThroughOptions>({
    root: `inline-flex items-center rounded-2xl gap-2 px-3 py-2
        bg-primary-500 dark:bg-primary-500
        text-white dark:text-surface-0
        has-[img]:pt-1 has-[img]:pb-1
        p-removable:pe-2`,
    image: `rounded-full w-8 h-8 -ms-2`,
    icon: `text-white dark:text-surface-0 text-base w-4 h-4`
});

const { bindProps, mergedPt } = usePrimeBindings(props, attrs, theme);

</script>
