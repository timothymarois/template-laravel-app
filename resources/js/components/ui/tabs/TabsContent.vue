<script setup lang="ts">
import { inject, computed, type WritableComputedRef, type ComputedRef } from 'vue';
import { cn } from '@/utils';

interface TabsContext {
    activeTab: WritableComputedRef<string>;
    variant: ComputedRef<'underline' | 'pills' | 'boxed'>;
}

interface Props {
    value: string;
    class?: string;
}

const props = defineProps<Props>();

const context = inject<TabsContext>('tabsContext')!;

const isActive = computed(() => context.activeTab.value === props.value);
</script>

<template>
    <div
        v-if="isActive"
        role="tabpanel"
        :class="cn('mt-4 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2', props.class)"
    >
        <slot />
    </div>
</template>
