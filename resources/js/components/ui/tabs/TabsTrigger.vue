<script setup lang="ts">
import { inject, computed, type ComputedRef, type WritableComputedRef } from 'vue';
import { cn } from '@/utils';

interface TabsContext {
    activeTab: WritableComputedRef<string>;
    variant: ComputedRef<'underline' | 'pills' | 'boxed'>;
}

interface Props {
    value: string;
    disabled?: boolean;
    class?: string;
}

const props = defineProps<Props>();

const context = inject<TabsContext>('tabsContext')!;

const isActive = computed(() => context.activeTab.value === props.value);

const triggerClasses = computed(() => {
    const base = 'inline-flex items-center justify-center whitespace-nowrap text-sm font-medium transition-all focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 cursor-pointer';

    switch (context.variant.value) {
    case 'pills':
        return cn(
            base,
            'px-4 py-2 rounded-full',
            isActive.value
                ? 'bg-primary text-primary-foreground shadow-sm'
                : 'text-muted-foreground hover:text-foreground hover:bg-muted'
        );
    case 'boxed':
        return cn(
            base,
            'px-3 py-1.5 rounded-sm flex-1',
            isActive.value
                ? 'bg-background text-foreground shadow-sm'
                : 'text-muted-foreground hover:text-foreground'
        );
    case 'underline':
    default:
        return cn(
            base,
            'px-4 py-2 -mb-px border-b-2',
            isActive.value
                ? 'border-primary text-foreground'
                : 'border-transparent text-muted-foreground hover:text-foreground hover:border-muted-foreground/30'
        );
    }
});

const selectTab = () => {
    if (!props.disabled) {
        context.activeTab.value = props.value;
    }
};
</script>

<template>
    <button
        role="tab"
        type="button"
        :aria-selected="isActive"
        :data-state="isActive ? 'active' : 'inactive'"
        :disabled="disabled"
        :class="cn(triggerClasses, props.class)"
        @click="selectTab"
    >
        <slot />
    </button>
</template>
