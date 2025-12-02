<template>
    <Drawer
        unstyled
        v-bind="bindProps"
        :pt="mergedPt"
        :ptOptions="{
            mergeProps: ptViewMerge
        }"
    >
        <template #closebutton="{ closeCallback }">
            <Button text rounded @click="closeCallback" autofocus>
                <template #icon>
                    <TimesIcon />
                </template>
            </Button>
        </template>
        <template v-for="(_, slotName) in $slots" v-slot:[slotName]="slotProps">
            <slot :name="slotName" v-bind="slotProps ?? {}" />
        </template>
    </Drawer>
</template>

<script setup lang="ts">
import TimesIcon from '@primevue/icons/times';
import Drawer, { type DrawerPassThroughOptions, type DrawerProps } from 'primevue/drawer';
import { ref, useAttrs } from 'vue';
import Button from './Button.vue';
import { ptViewMerge } from '../utils';
import { usePrimeBindings } from '../composables';

interface Props extends /* @vue-ignore */ DrawerProps {}
const props = defineProps<Props>();
const attrs = useAttrs();

const theme = ref<DrawerPassThroughOptions>({
    root: `atlas_drawer_shadow flex flex-col pointer-events-auto relative
        border-surface-200 dark:border-surface-700
        bg-surface-0 dark:bg-surface-900
        text-surface-700 dark:text-surface-0
        p-left:w-80 p-left:h-full p-left:border-r
        p-right:w-80 p-right:h-full p-right:border-s
        p-top:h-40 p-top:w-full p-top:border-b
        p-bottom:h-40 p-bottom:w-full p-bottom:border-t
        p-full-screen:transition-opacity p-full-screen:transform-none p-full-screen:w-screen p-full-screen:h-screen p-full-screen:max-h-full p-full-screen:top-0 p-full-screen:left-0`,
    header: `flex items-center justify-between flex-shrink-0 p-5`,
    title: `font-semibold text-2xl`,
    content: `overflow-y-auto flex-grow pt-0 pb-5 px-5`,
    footer: `p-5`,
    mask: `p-modal:bg-black/30`,
    transition: {
        enterFromClass: `p-left:-translate-x-full p-right:translate-x-full p-top:-translate-y-full p-bottom:translate-y-full p-full-screen:opacity-0`,
        enterActiveClass: `transition-transform duration-400 ease-out p-full-screen:transition-opacity`,
        leaveActiveClass: `transition-transform duration-200 ease-in p-full-screen:transition-opacity`,
        leaveToClass: `p-left:-translate-x-full p-right:translate-x-full p-top:-translate-y-full p-bottom:translate-y-full p-full-screen:opacity-0`
    }
});

const { bindProps, mergedPt } = usePrimeBindings(props, attrs, theme);

</script>

<style>
.atlas_drawer_shadow {
    box-shadow: 0 8px 10px -5px rgba(0, 0, 0, .2), 0 16px 24px 2px rgba(0, 0, 0, .14), 0 6px 30px 5px rgba(0, 0, 0, .12) !important;
}
</style>

