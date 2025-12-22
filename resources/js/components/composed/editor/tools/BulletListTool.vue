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
                    '!bg-accent text-foreground': editor?.isActive('bulletList')
                }"
                @click="editor?.chain().focus().toggleBulletList().run()"
            >
                <div class="flex items-center text-foreground">
                    <IconList class="size-5" />
                </div>
            </Button>
        </TooltipTrigger>
        <TooltipContent>Bullet list</TooltipContent>
    </Tooltip>
</template>

<script setup>
import { IconList } from '@tabler/icons-vue';
import Button from '../../button/Button.vue';
import {
    Tooltip,
    TooltipContent,
    TooltipTrigger,
} from '@/components/ui/tooltip';

const props = defineProps({ editor: Object });
</script>
