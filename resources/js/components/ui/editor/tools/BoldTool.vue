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
                    '!bg-accent text-foreground': editor?.isActive('bold')
                }"
                @click="editor?.chain().focus().toggleBold().run()"
            >
                <div class="flex items-center text-foreground">
                    <IconBold class="size-5" />
                </div>
            </Button>
        </TooltipTrigger>
        <TooltipContent>Bold</TooltipContent>
    </Tooltip>
</template>

<script setup lang="ts">
import { IconBold } from '@tabler/icons-vue';
import { Button } from '@/components/ui/button';
import {
    Tooltip,
    TooltipContent,
    TooltipTrigger,
} from '@/components/ui/tooltip';

const props = defineProps({
    editor: Object
});
</script>
