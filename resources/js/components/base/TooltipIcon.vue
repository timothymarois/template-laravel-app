<template>
    <Tooltip>
        <TooltipTrigger as-child>
            <span :class="cn('cursor-help inline text-muted-foreground hover:text-foreground', className)">
                <IconInfoSquareRoundedFilled :class="iconClass" />
            </span>
        </TooltipTrigger>
        <TooltipContent v-if="text" :class="cn('max-w-[260px] whitespace-pre-line', contentClass)">
            {{ text }}
        </TooltipContent>
    </Tooltip>
</template>

<script setup lang="ts">
import { computed } from 'vue';
import { IconInfoSquareRoundedFilled } from '@tabler/icons-vue';
import {
    Tooltip,
    TooltipContent,
    TooltipTrigger,
} from '@/components/ui/tooltip';
import { cn } from '@/lib/utils';

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

const className = computed(() => props.class || '');
const contentClass = computed(() => props.contentClass || '');
</script>
