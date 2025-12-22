<template>
    <div :class="cn('w-full', className)">
        <label
            v-if="label"
            :for="name"
            class="flex items-center text-sm font-medium leading-6 text-foreground mb-2"
        >
            <span>{{ label }}</span>
            <span v-if="required" class="text-destructive ml-0.5">*</span>
            <TooltipIcon v-if="tooltip" :text="tooltip" class="ml-2" />
        </label>
        <div class="w-full">
            <slot />
            <div v-if="error" class="text-sm text-destructive mt-1">
                {{ error }}
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import { computed } from 'vue';
import TooltipIcon from '../display/TooltipIcon.vue';
import { cn } from '@/utils';

interface Props {
    label?: string;
    tooltip?: string;
    name?: string;
    required?: boolean;
    error?: string;
    class?: string;
}

const props = withDefaults(defineProps<Props>(), {
    label: '',
    tooltip: '',
    name: '',
    required: false,
    error: ''
});

const className = computed(() => props.class || '');
</script>
