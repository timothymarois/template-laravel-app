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
                    '!bg-accent text-foreground': editor?.isActive('orderedList')
                }"
                @click="editor?.chain().focus().toggleOrderedList().run()"
            >
                <div class="flex items-center text-foreground">
                    <IconListNumbers class="size-5" />
                </div>
            </Button>
        </TooltipTrigger>
        <TooltipContent>Numbered list</TooltipContent>
    </Tooltip>
</template>

<script setup>
import { IconListNumbers } from '@tabler/icons-vue';
import Button from '../../Button.vue';
import {
    Tooltip,
    TooltipContent,
    TooltipTrigger,
} from '@/components/ui/tooltip';

const props = defineProps({ editor: Object });
</script>
