<template>
    <div class="relative inline-flex items-center h-8 rounded-full bg-muted border border-border/60 shadow-inner">
        <span
            class="absolute top-0.5 bottom-0.5 w-[calc(50%-2px)] rounded-full bg-card shadow-sm transition-transform duration-200 ease-out"
            :class="modelValue === 'list' ? 'translate-x-[calc(100%+2px)]' : 'translate-x-0.5'"
        />
        <button
            type="button"
            class="relative z-10 flex items-center justify-center h-8 w-9 rounded-full cursor-pointer transition-colors"
            :class="modelValue === 'grid' ? 'text-foreground' : 'text-muted-foreground hover:text-foreground'"
            title="Grid view"
            aria-label="Grid view"
            @click="emit('update:modelValue', 'grid')"
        >
            <LayoutGrid class="h-3.5 w-3.5" />
        </button>
        <button
            type="button"
            class="relative z-10 flex items-center justify-center h-8 w-9 rounded-full cursor-pointer transition-colors"
            :class="modelValue === 'list' ? 'text-foreground' : 'text-muted-foreground hover:text-foreground'"
            title="List view"
            aria-label="List view"
            @click="emit('update:modelValue', 'list')"
        >
            <List class="h-3.5 w-3.5" />
        </button>
    </div>
</template>

<script setup lang="ts">
import { LayoutGrid, List } from 'lucide-vue-next';

type ViewMode = 'grid' | 'list';

defineProps<{ modelValue: ViewMode }>();

const emit = defineEmits<{
    'update:modelValue': [value: ViewMode];
}>();
</script>
