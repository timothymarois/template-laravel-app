<template>
    <Popover
        ref="el"
        unstyled
        v-bind="bindProps"
        :pt="mergedPt"
        :ptOptions="{ mergeProps: ptViewMerge }"
    >
        <template v-for="(_, slotName) in $slots" v-slot:[slotName]="slotProps">
            <slot :name="slotName" v-bind="slotProps ?? {}" />
        </template>
    </Popover>
</template>

<script setup lang="ts">
import Popover, { type PopoverPassThroughOptions, type PopoverProps } from 'primevue/popover';
import { ref, useAttrs } from 'vue';
import { ptViewMerge } from '../utils';
import { usePrimeBindings } from '../composables';

interface Props extends /* @vue-ignore */ PopoverProps {}
const props = defineProps<Props>();
const attrs = useAttrs();

const theme = ref<PopoverPassThroughOptions>({
    root: `mt-[5px] p-flipped:-mt-[5px] p-flipped:mb-[5px]
        bg-surface-0 dark:bg-surface-900 text-surface-800 dark:text-surface-0 text-sm
        dark:border dark:border-surface-700
        rounded-[var(--p-content-border-radius)] shadow-floating`,
    content: `p-3`,
    transition: {
        enterFromClass: 'opacity-0 scale-y-75',
        enterActiveClass: 'transition duration-120 ease-[cubic-bezier(0,0,0.2,1)]',
        leaveActiveClass: 'transition-opacity duration-100 ease-linear',
        leaveToClass: 'opacity-0'
    }
});

const { bindProps, mergedPt } = usePrimeBindings(props, attrs, theme);


const el = ref();

defineExpose({
    toggle: (event: any, target?: any) => el.value?.toggle(event, target),
    show: (event: any, target?: any) => el.value?.show(event, target),
    hide: () => el.value?.hide()
});
</script>

<style>
.shadow-floating {
    box-shadow: 0 0 2px 0 rgba(0, 0, 0, 0.24), 0 8px 10px 1px rgb(0 0 0 / 5%),
        0 3px 14px 2px rgb(0 0 0 / 6%), 0 5px 5px -3px rgba(0, 0, 0, 0.1);
}
</style>
