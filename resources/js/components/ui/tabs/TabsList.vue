<script setup lang="ts">
import { inject, computed, type ComputedRef, type WritableComputedRef } from 'vue';
import { cn } from '@/utils';

interface TabsContext {
    activeTab: WritableComputedRef<string>;
    variant: ComputedRef<'underline' | 'pills' | 'boxed'>;
}

interface Props {
    class?: string;
}

const props = defineProps<Props>();

const context = inject<TabsContext>('tabsContext')!;

const listClasses = computed(() => {
    switch (context.variant.value) {
        case 'pills':
            return 'inline-flex gap-1';
        case 'boxed':
            return 'inline-flex p-1 bg-muted rounded-lg';
        case 'underline':
        default:
            return 'inline-flex gap-1 border-b border-border';
    }
});
</script>

<template>
    <div
        role="tablist"
        :class="cn(listClasses, props.class)"
    >
        <slot />
    </div>
</template>
