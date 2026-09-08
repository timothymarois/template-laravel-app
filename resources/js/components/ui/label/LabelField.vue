<template>
    <div :class="cn('w-full', props.class)">
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
            <!-- The error replaces the help rather than stacking under it: once
                 something is wrong, that is the sentence to read. -->
            <p v-else-if="help" class="text-sm text-muted-foreground mt-1">
                {{ help }}
            </p>
        </div>
    </div>
</template>

<script setup lang="ts">
import type { HTMLAttributes } from 'vue';
import { TooltipIcon } from '@/components/ui/tooltip';
import { cn } from '@/utils';

interface Props {
    label?: string;
    tooltip?: string;
    name?: string;
    required?: boolean;
    error?: string;

    /**
     * A sentence under the field, for what a label cannot say in two words.
     *
     * Undeclared, Vue's attribute fallthrough puts a passed `help` silently on the
     * wrapping element — no error, no warning, no text. A fork wrote roughly thirty
     * field explanations that nobody ever saw.
     */
    help?: string;
    class?: HTMLAttributes['class'];
}

const props = withDefaults(defineProps<Props>(), {
    label: '',
    tooltip: '',
    name: '',
    required: false,
    error: '',
    help: ''
});
</script>
