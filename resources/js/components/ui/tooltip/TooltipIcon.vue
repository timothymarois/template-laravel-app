<template>
    <Tooltip>
        <TooltipTrigger as-child>
            <span :class="cn('cursor-help inline text-muted-foreground hover:text-foreground', props.class)">
                <IconInfoSquareRoundedFilled :class="iconClass" />
            </span>
        </TooltipTrigger>
        <TooltipContent v-if="text || $slots.default" :class="cn('max-w-[260px] whitespace-pre-line', props.contentClass)">
            <slot>{{ text }}</slot>
        </TooltipContent>
    </Tooltip>
</template>

<script setup lang="ts">
import { IconInfoSquareRoundedFilled } from '@tabler/icons-vue';
import {
    Tooltip,
    TooltipContent,
    TooltipTrigger,
} from '@/components/ui/tooltip';
import { cn } from '@/utils';

interface Props {
    text?: string | null;
    iconClass?: string;
    class?: string;
    contentClass?: string;
}

const props = withDefaults(defineProps<Props>(), {
    text: null,
    iconClass: 'size-5'
});
</script>
