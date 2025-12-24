<template>
    <Tooltip>
        <TooltipTrigger as-child>
            <Button
                icon
                text
                size="small"
                class="!px-2"
                :class="[
                    'hover:!bg-accent/50',
                    isActive && '!bg-accent text-foreground',
                    buttonClass
                ]"
                :disabled="disabled"
                @click="$emit('click')"
            >
                <div class="flex items-center text-foreground">
                    <slot />
                </div>
            </Button>
        </TooltipTrigger>
        <TooltipContent>
            <slot name="tooltip">{{ tooltip }}</slot>
        </TooltipContent>
    </Tooltip>
</template>

<script setup lang="ts">
/**
 * Base button component for editor toolbar tools.
 * Provides consistent styling, tooltip, and active state handling.
 *
 * @example Basic usage
 * <EditorToolButton
 *     :isActive="editor?.isActive('bold')"
 *     tooltip="Bold"
 *     @click="editor?.chain().focus().toggleBold().run()"
 * >
 *     <IconBold class="size-5" />
 * </EditorToolButton>
 *
 * @example With custom tooltip content
 * <EditorToolButton :isActive="false" @click="handleClick">
 *     <IconImage class="size-5" />
 *     <template #tooltip>
 *         Insert Image <kbd>Ctrl+I</kbd>
 *     </template>
 * </EditorToolButton>
 */
import { Button } from '@/components/ui/button';
import {
    Tooltip,
    TooltipContent,
    TooltipTrigger,
} from '@/components/ui/tooltip';

defineProps({
    /** Whether the tool is currently active (e.g., bold is applied to selection) */
    isActive: {
        type: Boolean,
        default: false
    },
    /** Tooltip text to display on hover */
    tooltip: {
        type: String,
        default: ''
    },
    /** Whether the button is disabled */
    disabled: {
        type: Boolean,
        default: false
    },
    /** Additional classes to apply to the button */
    buttonClass: {
        type: String,
        default: ''
    }
});

defineEmits(['click']);
</script>
