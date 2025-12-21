<template>
    <div class="bg-primary text-primary-foreground px-4 rounded-full shadow">
        <div class="grow flex items-center">
            <div class="text-sm font-semibold px-3 py-2" :class="{ 'pr-6': menuItems?.length }">
                Selected: <span class="font-bold">{{ formatNumber(selectedCount) }}</span>
            </div>
            <div v-if="menuItems?.length" class="flex relative justify-center items-center mx-2 py-3 min-h-full before:block before:absolute before:left-1/2 before:top-0 before:transform before:-translate-x-1/2 before:min-h-full before:border-solid before:border-l before:border-primary-foreground/30" />
            <template v-for="(menuItem, index) in menuItems" :key="index">
                <Tooltip v-if="menuItem?.tooltip">
                    <TooltipTrigger as-child>
                        <div class="pl-3">
                            <ActionItem :menuItem="menuItem" @action="actionClick" @toggle="toggle" />
                        </div>
                    </TooltipTrigger>
                    <TooltipContent>
                        {{ menuItem.tooltip }}
                    </TooltipContent>
                </Tooltip>
                <div v-else class="pl-3">
                    <ActionItem :menuItem="menuItem" @action="actionClick" @toggle="toggle" />
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
                            <IconX class="size-4" />
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
import { ref, defineComponent, h } from 'vue';
import { formatNumber } from '../../../utils';
import { IconX } from '@tabler/icons-vue';
import Menu from '../../base/Menu.vue';
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

const menu = ref<any>(null);

const actionClick = (item: any) => {
    if (!item?.disabled) {
        emit('action', item?.action);
    }
};

const toggle = (event: any) => {
    if (menu.value) {
        (menu.value as any)[0].toggle(event);
    }
};

const ActionItem = defineComponent({
    props: {
        menuItem: Object
    },
    emits: ['action', 'toggle'],
    setup(props, { emit }) {
        return () => {
            if (props.menuItem?.children && props.menuItem.children.length) {
                return h('div', { class: 'relative' }, [
                    h('div', {
                        class: 'flex items-center space-x-0.5 font-semibold cursor-pointer hover:text-primary-foreground text-sm hover:bg-primary-foreground/10 px-3 py-2',
                        onClick: (e: any) => { e.stopPropagation(); emit('toggle', e); }
                    }, [
                        h('div', { class: 'flex items-center space-x-0.5 font-semibold hover:cursor-pointer' }, [
                            h('div', props.menuItem.label),
                            h('svg', { xmlns: 'http://www.w3.org/2000/svg', class: 'size-4', viewBox: '0 0 24 24' }, [
                                h('path', {
                                    fill: 'currentColor',
                                    'fill-rule': 'evenodd',
                                    d: 'M7 9a1 1 0 0 0-.707 1.707l5 5a1 1 0 0 0 1.414 0l5-5A1 1 0 0 0 17 9z',
                                    'clip-rule': 'evenodd'
                                })
                            ])
                        ])
                    ]),
                    h(Menu, {
                        ref: menu,
                        model: props.menuItem.children,
                        size: 'small',
                        popup: true
                    }, {
                        item: ({ item, props: itemProps }: any) => h('div', {
                            class: 'flex align-items-center',
                            ...itemProps.action,
                            onClick: () => emit('action', item)
                        }, [
                            item?.icon ? h('span', { class: item.icon }) : null,
                            h('span', {
                                class: ['text-sm', {
                                    'text-muted-foreground': item?.disabled,
                                    'ml-4': item?.icon
                                }]
                            }, item.label)
                        ])
                    })
                ]);
            }
            return h('div', {
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
