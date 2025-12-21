<template>
    <Tooltip>
        <TooltipTrigger as-child>
            <Button
                icon
                text
                size="small"
                class="!px-2 hover:!bg-accent/50"
                @click="editor?.chain().focus().unsetAllMarks().clearNodes().run()"
            >
                <div class="flex items-center text-foreground">
                    <IconClearFormatting class="size-5" />
                </div>
            </Button>
        </TooltipTrigger>
        <TooltipContent>Clear formatting</TooltipContent>
    </Tooltip>
</template>

<script setup>
import { IconClearFormatting } from '@tabler/icons-vue';
import Button from '../../Button.vue';
import {
    Tooltip,
    TooltipContent,
    TooltipTrigger,
} from '@/components/ui/tooltip';

const props = defineProps({ editor: Object });
</script>
