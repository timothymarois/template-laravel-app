<template>
    <div
        v-bind="bindProps"
        :class="[
            mergedPt.root.class,
            { 'text-primary-500 hover:text-primary-600 dark:text-gray-200': isActive }
        ]"
        @click="toggle"
    >
        <svg
            v-show="isActive"
            xmlns="http://www.w3.org/2000/svg"
            :class="mergedPt.icon.class"
            viewBox="0 0 24 24"
        >
            <path
                fill="currentColor"
                d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10s10-4.48 10-10S17.52 2 12 2m1 15h-2v-6h2zm0-8h-2V7h2z"
            />
        </svg>
        <svg
            v-show="!isActive"
            xmlns="http://www.w3.org/2000/svg"
            :class="mergedPt.icon.class"
            viewBox="0 0 24 24"
        >
            <path
                fill="currentColor"
                d="M11 7h2v2h-2zm0 4h2v6h-2zm1-9C6.48 2 2 6.48 2 12s4.48 10 10 10s10-4.48 10-10S17.52 2 12 2m0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8s8 3.59 8 8s-3.59 8-8 8"
            />
        </svg>
    </div>
    <Popover ref="tactive" @show="setIsActive(true)" @hide="setIsActive(false)">
        <slot />
    </Popover>
</template>

<script setup lang="ts">
import { ref, useAttrs } from 'vue';

import Popover from './Popover.vue';
import { usePrimeBindings } from '../composables';

interface TooltipInfoPassThroughOptions {
    root?: any;
    icon?: any;
}

interface Props {
    pt?: TooltipInfoPassThroughOptions;
}

const props = defineProps<Props>();
const attrs = useAttrs();

const theme = ref<TooltipInfoPassThroughOptions>({
    root: 'relative hover:cursor-pointer text-gray-500 hover:text-gray-900 dark:text-gray-400 dark:hover:text-gray-200',
    icon: 'size-5'
});

const { bindProps, mergedPt } = usePrimeBindings(props, attrs, theme);



const emit = defineEmits<{ (e: 'toggle', value: boolean): void }>();

const isActive = ref(false);
const tactive = ref<any>();

const setIsActive = (value: boolean) => {
    isActive.value = value;
    emit('toggle', isActive.value);
};

const toggle = (event: any) => {
    tactive.value.toggle(event);
};
</script>
