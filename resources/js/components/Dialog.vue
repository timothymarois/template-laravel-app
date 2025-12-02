<template>
    <Dialog
        unstyled
        v-bind="bindProps"
        :pt="mergedPt"
        :ptOptions="{ mergeProps: ptViewMerge }"
    >
        <template #closebutton="{ closeCallback }">
            <Button text rounded @click="closeCallback" autofocus>
                <template #icon>
                    <TimesIcon />
                </template>
            </Button>
        </template>
        <template #maximizebutton="{ maximized, maximizeCallback }">
            <Button text rounded @click="maximizeCallback" autofocus>
                <template #icon>
                    <WindowMinimizeIcon v-if="maximized" />
                    <WindowMaximizeIcon v-else />
                </template>
            </Button>
        </template>
        <template v-for="(_, slotName) in $slots" v-slot:[slotName]="slotProps">
            <slot :name="slotName" v-bind="slotProps ?? {}" />
        </template>
    </Dialog>
</template>

<script setup lang="ts">
import TimesIcon from '@primevue/icons/times';
import WindowMaximizeIcon from '@primevue/icons/windowmaximize';
import WindowMinimizeIcon from '@primevue/icons/windowminimize';
import Dialog, { type DialogPassThroughOptions, type DialogProps } from 'primevue/dialog';
import { ref, useAttrs } from 'vue';
import Button from './Button.vue';
import { ptViewMerge } from '../utils';
import { usePrimeBindings } from '../composables';

interface Props extends /* @vue-ignore */ DialogProps {}
const props = defineProps<Props>();
const attrs = useAttrs();

const theme = ref<DialogPassThroughOptions>({
    root: `max-h-[90%] max-w-screen rounded-[var(--p-content-border-radius)]
        border border-surface-200 dark:border-surface-700
        bg-surface-0 dark:bg-surface-900
        text-surface-700 dark:text-surface-0 shadow-lg
        p-maximized:w-screen p-maximized:h-screen p-maximized:top-0 p-maximized:start-0 p-maximized:max-h-full p-maximized:rounded-none`,
    header: `flex items-center justify-between shrink-0 p-4 pt-2`,
    title: `font-semibold text-lg`,
    headerActions: `flex items-center gap-2`,
    content: `overflow-y-auto pt-0 px-4 pb-4 p-maximized:grow`,
    footer: `shrink-0 pt-0 px-4 pb-4 flex justify-end gap-2`,
    mask: `p-modal:bg-black/50 p-modal:fixed p-modal:top-0 p-modal:start-0 p-modal:w-full p-modal:h-full`,
    transition: {
        enterFromClass: 'opacity-0 scale-75',
        enterActiveClass: 'transition-all duration-150 ease-[cubic-bezier(0,0,0.2,1)]',
        leaveActiveClass: 'transition-all duration-150 ease-[cubic-bezier(0.4,0,0.2,1)]',
        leaveToClass: 'opacity-0 scale-75'
    }
});

const { bindProps, mergedPt } = usePrimeBindings(props, attrs, theme);

</script>

