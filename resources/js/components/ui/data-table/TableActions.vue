<template>
    <div class="bg-primary text-primary-foreground px-4 rounded-full shadow">
        <div class="grow flex items-center">
            <div class="text-sm font-semibold px-3 py-2" :class="{ 'pr-6': menuItems?.length }">
                Selected: <span class="font-bold">{{ formatNumber(selectedCount) }}</span>
            </div>
            <div v-if="menuItems?.length" class="flex relative justify-center items-center mx-2 py-3 min-h-full before:block before:absolute before:left-1/2 before:top-0 before:transform before:-translate-x-1/2 before:min-h-full before:border-solid before:border-l before:border-primary-foreground/30" />
            <template v-for="(actionItem, index) in menuItems" :key="index">
                <Tooltip v-if="actionItem?.tooltip">
                    <TooltipTrigger as-child>
                        <div class="pl-3">
                            <ActionItem :menuItem="actionItem" @action="actionClick" />
                        </div>
                    </TooltipTrigger>
                    <TooltipContent>
                        {{ actionItem.tooltip }}
                    </TooltipContent>
                </Tooltip>
                <div v-else class="pl-3">
                    <ActionItem :menuItem="actionItem" @action="actionClick" />
                </div>
            </template>
            <div>
                <Tooltip>
                    <TooltipTrigger as-child>
                        <button
                            type="button"
                            class="flex items-center justify-center hover:bg-primary-foreground/10 transition px-3 py-2 cursor-pointer opacity-80 hover:opacity-100"
                            @click="$emit('action', 'clear')"
                        >
                            <X class="size-4" />
                        </button>
                    </TooltipTrigger>
                    <TooltipContent>
                        Clear selection
                    </TooltipContent>
                </Tooltip>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import { defineComponent, h } from 'vue';
import { formatNumber } from '@/utils';
import { X, ChevronDown } from 'lucide-vue-next';
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuItem,
    DropdownMenuSeparator,
    DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu';
import {
    Tooltip,
    TooltipContent,
    TooltipTrigger,
} from '@/components/ui/tooltip';

const emit = defineEmits(['action']);

const { selectedCount, menuItems } = defineProps({
    selectedCount: {
        type: Number,
        default: null,
    },
    menuItems: {
        type: Array,
        default: () => []
    },
});

const actionClick = (item: any) => {
    if (!item?.disabled) {
        emit('action', item?.action);
    }
};

const ActionItem = defineComponent({
    props: {
        menuItem: Object
    },
    emits: ['action'],
    setup(props, { emit }) {
        return () => {
            if (props.menuItem?.children && props.menuItem.children.length) {
                // Render dropdown menu for items with children
                return h(DropdownMenu, {}, {
                    default: () => [
                        h(DropdownMenuTrigger, { asChild: true }, {
                            default: () => h('button', {
                                class: 'flex items-center space-x-0.5 font-semibold cursor-pointer hover:text-primary-foreground text-sm hover:bg-primary-foreground/10 px-3 py-2'
                            }, [
                                h('span', props.menuItem.label),
                                h(ChevronDown, { class: 'size-4 ml-1' })
                            ])
                        }),
                        h(DropdownMenuContent, { align: 'start' }, {
                            default: () => props.menuItem.children.map((child: any, idx: number) => {
                                if (child.separator) {
                                    return h(DropdownMenuSeparator, { key: `sep-${idx}` });
                                }
                                return h(DropdownMenuItem, {
                                    key: idx,
                                    disabled: child.disabled,
                                    onClick: () => emit('action', child)
                                }, {
                                    default: () => h('span', {
                                        class: child.disabled ? 'text-muted-foreground' : ''
                                    }, child.label)
                                });
                            })
                        })
                    ]
                });
            }
            // Render simple button for items without children
            return h('button', {
                class: ['flex items-center space-x-0.5 font-semibold cursor-pointer hover:text-primary-foreground text-sm hover:bg-primary-foreground/10 px-3 py-2', {
                    'opacity-50 pointer-events-none': props.menuItem?.disabled
                }],
                onClick: () => emit('action', props.menuItem)
            }, [
                h('span', { class: 'text-sm' }, props.menuItem?.label)
            ]);
        };
    }
});
</script>
