<template>
    <Tooltip>
        <TooltipTrigger as-child>
            <Button
                icon
                text
                size="small"
                class="!px-2"
                :class="{
                    'hover:!bg-accent/50': true,
                    '!bg-accent text-foreground': editor?.isActive('italic')
                }"
                @click="editor?.chain().focus().toggleItalic().run()"
            >
                <div class="flex items-center text-foreground">
                    <IconItalic class="size-5" />
                </div>
            </Button>
        </TooltipTrigger>
        <TooltipContent>Italic</TooltipContent>
    </Tooltip>
</template>

<script setup>
import { IconItalic } from '@tabler/icons-vue';
import { Button } from '@/components/ui/button';
import {
    Tooltip,
    TooltipContent,
    TooltipTrigger,
} from '@/components/ui/tooltip';

const props = defineProps({ editor: Object });
</script>
