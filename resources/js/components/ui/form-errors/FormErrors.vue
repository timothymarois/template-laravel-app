<template>
    <div
        v-if="hasErrors || failed"
        :class="cn('rounded-md bg-destructive/10 border border-destructive/30 p-4 cursor-pointer', props.class)"
        @click="expandErrors = !expandErrors"
    >
        <div class="flex">
            <div class="relative top-1 shrink-0 text-destructive self-start flex items-center">
                <IconAlertCircle size="18" stroke-width="2.5" />
            </div>
            <div class="ml-2 basis-full">
                <div class="flex font-semibold text-destructive text-base">
                    <span>{{ title || (failed ? 'An error occurred' : 'Errors') }}</span>
                    <span :class="['ml-auto transition-transform duration-200', expandErrors ? 'rotate-180' : '']">
                        <IconChevronDown size="16" />
                    </span>
                </div>
            </div>
        </div>
        <transition name="expand" @enter="expandEnter" @leave="expandLeave">
            <div v-show="expandErrors" class="overflow-hidden">
                <div class="text-sm text-destructive pt-1">
                    <ul v-if="hasErrors" role="list" class="list-disc pl-[26px] space-y-1 text-left">
                        <li
                            v-for="(error, index) in props.errors"
                            :key="index"
                        >
                            <slot :error="error">
                                <span v-html="error" />
                            </slot>
                        </li>
                    </ul>
                    <div v-else-if="failed" class="pl-[26px] text-left">
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
import { ref, computed, onMounted } from 'vue';
import { IconAlertCircle, IconChevronDown } from '@tabler/icons-vue';
import { isEmpty, cn, createExpandTransition } from '@/utils';

interface Props {
    errors?: Record<string, any> | any[];
    failed?: boolean;
    title?: string;
    expandDefault?: boolean;
    class?: string;
}

const props = withDefaults(defineProps<Props>(), {
    errors: () => ({}),
    failed: false,
    title: '',
    expandDefault: true,
});

const expandErrors = ref(props.expandDefault);

onMounted(() => {
    expandErrors.value = props.expandDefault;
});

const hasErrors = computed(() => !isEmpty(props.errors));

const { expandEnter, expandLeave } = createExpandTransition(200);
</script>

<style scoped>
.expand-enter-active,
.expand-leave-active {
    transition: height 200ms ease;
}
</style>
