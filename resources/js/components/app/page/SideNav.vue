<template>
    <div
        v-if="items && items.length"
        class="w-64 shrink-0 border-r border-border bg-card overflow-hidden select-none"
    >
        <ScrollFrame>
            <div class="py-2 px-2 space-y-2">
                <template v-for="item in items" :key="item.label">
                    <div v-if="item.children && item.children.length" class="space-y-2">
                        <div class="pt-2">
                            <span class="px-4 text-sm text-muted-foreground font-bold uppercase">{{ item.label }}</span>
                        </div>
                        <ul class="space-y-1">
                            <li v-for="child in item.children" :key="child.href">
                                <component
                                    :is="linkComponent"
                                    :href="child.href"
                                    draggable="false"
                                    class="block rounded-md px-4 py-2 text-sm font-medium"
                                    :class="[
                                        isActive(child)
                                            ? 'bg-accent text-accent-foreground font-semibold'
                                            : 'text-foreground hover:bg-accent',
                                        child.disabled && 'opacity-50 cursor-not-allowed pointer-events-none'
                                    ]"
                                >
                                    <span class="flex items-center">
                                        <span class="flex-1">{{ child.label }}</span>
                                        <Tooltip v-if="child.disabled && child.lockTooltipText">
                                            <TooltipTrigger as-child>
                                                <span class="ml-auto pl-2 pointer-events-auto">
                                                    <Lock :size="16" />
                                                </span>
                                            </TooltipTrigger>
                                            <TooltipContent side="right">
                                                {{ child.lockTooltipText }}
                                            </TooltipContent>
                                        </Tooltip>
                                    </span>
                                </component>
                            </li>
                        </ul>
                    </div>
                    <component
                        v-else
                        :is="linkComponent"
                        :href="item.href"
                        draggable="false"
                        class="block rounded-md px-4 py-2 text-sm font-medium"
                        :class="[
                            isActive(item)
                                ? 'bg-accent text-accent-foreground font-semibold'
                                : 'text-foreground hover:bg-accent',
                            item.disabled && 'opacity-50 cursor-not-allowed pointer-events-none'
                        ]"
                    >
                        <span class="flex items-center">
                            <span class="flex-1">{{ item.label }}</span>
                            <Tooltip v-if="item.disabled && item.lockTooltipText">
                                <TooltipTrigger as-child>
                                    <span class="ml-auto pl-2 pointer-events-auto">
                                        <Lock :size="16" />
                                    </span>
                                </TooltipTrigger>
                                <TooltipContent side="right">
                                    {{ item.lockTooltipText }}
                                </TooltipContent>
                            </Tooltip>
                        </span>
                    </component>
                </template>
            </div>
        </ScrollFrame>
    </div>
</template>

<script setup>
import { toRefs } from 'vue';
import { ScrollFrame } from '@/components/ui';
import { Lock } from 'lucide-vue-next';
import { isPageActive } from '@/utils/vue/inertia';
import {
    Tooltip,
    TooltipContent,
    TooltipTrigger,
} from '@/components/ui/tooltip';

const props = defineProps({
    items: {
        type: Array,
        default: () => [],
    },
    linkComponent: {
        type: [String, Object],
        default: 'a',
    },
});

// toRefs, not a destructure: `items` is often computed (permissions, badge
// counts) and a by-value capture at setup never re-renders when it changes.
const { items, linkComponent } = toRefs(props);

const isActive = (item) =>
    item.parent ? isPageActive(item.parent) : isPageActive(item.href, undefined, true);
</script>
