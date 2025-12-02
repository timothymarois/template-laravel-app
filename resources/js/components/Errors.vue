<template>
    <div
        v-bind="bindProps"
        :class="[mergedPt.root.class, hasErrors || failed ? '' : 'hidden']"
        @click="expandErrors = !expandErrors"
    >
        <div :class="mergedPt.header.class">
            <div :class="mergedPt.icon.class">
                <IconAlertCircle size="18" stroke-width="2.5" />
            </div>
            <div :class="mergedPt.titleContainer.class">
                <div :class="mergedPt.title.class">
                    <span>{{ title || (failed ? 'An error occurred' : 'Errors') }}</span>
                    <span :class="[mergedPt.chevron.class, expandErrors ? 'rotate-180' : '']">
                        <IconChevronDown size="16" />
                    </span>
                </div>
            </div>
        </div>
        <transition name="expand" @enter="expandEnter" @leave="expandLeave">
            <div v-show="expandErrors" :class="mergedPt.expandRoot.class">
                <div :class="mergedPt.text.class">
                    <ul v-if="hasErrors" role="list" :class="mergedPt.list.class">
                        <li
                            v-for="(error, index) in props.errors"
                            :key="index"
                            :class="mergedPt.listItem.class"
                        >
                            <slot :error="error">
                                <span v-html="error" />
                            </slot>
                        </li>
                    </ul>
                    <div v-else-if="failed" :class="mergedPt.defaultError.class">
                        <slot name="defaultError">
                            <span>An unexpected error occurred. Please try again later.</span>
                        </slot>
                    </div>
                </div>
            </div>
        </transition>
    </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted, useAttrs } from 'vue';
import { IconAlertCircle, IconChevronDown } from '@tabler/icons-vue';
import { isEmpty } from '../utils';
import { usePrimeBindings } from '../composables';

interface ErrorsPassThroughOptions {
    root?: any;
    header?: any;
    icon?: any;
    titleContainer?: any;
    title?: any;
    chevron?: any;
    expandRoot?: any;
    text?: any;
    list?: any;
    listItem?: any;
    defaultError?: any;
}

interface Props {
    errors?: Record<string, any> | any[];
    failed?: boolean;
    title?: string;
    expandDefault?: boolean;
    pt?: ErrorsPassThroughOptions;
}

const props = withDefaults(defineProps<Props>(), {
    errors: () => ({}),
    failed: false,
    title: '',
    expandDefault: true,
});
const attrs = useAttrs();

const expandErrors = ref(props.expandDefault);

onMounted(() => {
    expandErrors.value = props.expandDefault;
});

const theme = ref<ErrorsPassThroughOptions>({
    root: 'rounded-[var(--p-content-border-radius)] bg-red-50 dark:bg-red-900/20 border border-red-300 dark:border-red-800/70 p-4 cursor-pointer',
    header: 'flex',
    icon: 'relative top-1 shrink-0 text-red-700 self-start flex items-center',
    titleContainer: 'ml-2 basis-full',
    title: 'flex font-semibold text-red-700 dark:text-red-50 text-base',
    chevron: 'ml-auto transition-transform duration-200',
    expandRoot: 'overflow-hidden',
    text: 'text-sm text-red-800 dark:text-red-50 pt-1',
    list: 'list-disc pl-[26px] space-y-1 text-left',
    listItem: '',
    defaultError: 'pl-[26px] text-left',
});

const { bindProps, mergedPt } = usePrimeBindings(props, attrs, theme);


const hasErrors = computed(() => !isEmpty(props.errors));

function expandEnter(el: HTMLElement) {
    el.style.height = '0';
    el.offsetHeight; // force reflow
    el.style.transition = 'height 200ms ease';
    el.style.height = el.scrollHeight + 'px';
}

function expandLeave(el: HTMLElement) {
    el.style.height = el.scrollHeight + 'px';
    el.offsetHeight; // force reflow
    el.style.transition = 'height 200ms ease';
    el.style.height = '0';
}


</script>

<style scoped>
.expand-enter-active,
.expand-leave-active {
    transition: height 200ms ease;
}
</style>

