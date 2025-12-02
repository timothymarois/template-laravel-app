<template>
    <div
        v-bind="bindProps"
        :class="mergedPt.root.class"
        @mouseenter="onTriggerEnter"
        @mouseleave="onTriggerLeave"
    >
        <template v-if="$slots.trigger">
            <slot name="trigger" :toggleMenu="toggleMenu" :triggerRef="setTriggerRef" />
        </template>
        <template v-else>
            <Button
                v-if="!onHover"
                ref="trigger"
                :icon="icon"
                text
                size="small"
                pt:root:class="!p-0 !w-[20px]"
                @click="toggleMenu"
            />
            <Button
                v-else
                ref="trigger"
                text
                size="small"
                pt:root:class="!p-0 !w-[20px]"
                @click="toggleMenu"
            >
                <IconChevronDown
                    class="transition-transform duration-200"
                    :class="isMenuOpen ? 'rotate-180' : ''"
                />
            </Button>
        </template>
        <Menu
            ref="menu"
            :model="items"
            :popup="true"
            @show="onMenuShow"
            @hide="onMenuHide"
            @mouseenter="onMenuEnter"
            @mouseleave="onMenuLeave"
        >
            <template #item="{ item, props }">
                <div
                    v-bind="props.action"
                    @click="actionClick(item)"
                    :class="[
                        'flex items-center px-3 py-2 text-sm',
                        item.disabled
                            ? 'opacity-50 cursor-not-allowed'
                            : 'cursor-pointer hover:bg-surface-100 dark:hover:bg-surface-700',
                        'text-surface-900 dark:text-surface-0 rounded-md'
                    ]"
                >
                    <span v-if="item.icon" :class="item.icon" />
                    <span class="ml-2">{{ item.label }}</span>
                </div>
            </template>
        </Menu>
    </div>
</template>

<script setup lang="ts">
import { ref, nextTick, useAttrs } from 'vue';
import Button from './Button.vue';
import Menu from './Menu.vue';
import { IconChevronDown } from '@tabler/icons-vue';
import type { MenuItem } from 'primevue/menuitem';
import { usePrimeBindings } from '../composables';


interface ButtonMenuPassThroughOptions {
    root?: any;
    menu?: any;
}

interface Props {
    items?: MenuItem[];
    icon?: string;
    ptData?: Record<string, any>;
    onHover?: boolean;
    pt?: ButtonMenuPassThroughOptions;
}

const props = withDefaults(defineProps<Props>(), {
    icon: 'pi pi-ellipsis-v',
    ptData: () => ({}),
    onHover: false
});
const attrs = useAttrs();

const theme = ref<ButtonMenuPassThroughOptions>({
    root: 'relative flex justify-center',
    menu: {}
});

const { bindProps, mergedPt } = usePrimeBindings(props, attrs, theme, ['onHover'] as const);


const trigger = ref<any>(null);
const menu = ref<any>(null);
const isMenuOpen = ref(false);
let closeTimeout = ref<any>(null);

const setTriggerRef = (el: any) => {
    trigger.value = el;
};

const getTriggerEl = () => trigger.value?.$el || trigger.value?.$refs?.button || trigger.value;

const onTriggerEnter = async () => {
    if (!props.onHover || isMenuOpen.value) return;
    clearTimeout(closeTimeout.value);
    await nextTick();
    const el = getTriggerEl();
    if (el) menu.value.show({ currentTarget: el });
};

const onTriggerLeave = (e: MouseEvent) => {
    if (!props.onHover || !isMenuOpen.value) return;
    const toEl = e.relatedTarget as Node | null;
    if (menu.value?.$el?.contains(toEl)) return;
    closeTimeout.value = setTimeout(() => {
        if (isMenuOpen.value) menu.value.hide();
    }, 200);
};

const toggleMenu = () => {
    const el = getTriggerEl();
    if (el) setTimeout(() => menu.value.show({ currentTarget: el }), 0);
};

const onMenuShow = () => (isMenuOpen.value = true);
const onMenuHide = () => (isMenuOpen.value = false);
const onMenuEnter = () => clearTimeout(closeTimeout.value);
const onMenuLeave = (e: MouseEvent) => {
    if (!props.onHover || !isMenuOpen.value) return;
    const toEl = e.relatedTarget as Node | null;
    const triggerEl = getTriggerEl();
    if (triggerEl?.contains(toEl)) return;
    closeTimeout.value = setTimeout(() => {
        if (isMenuOpen.value) menu.value.hide();
    }, 200);
};

const actionClick = (item: any) => {
    if (item?.disabled) return;
    item?.click ? item.click() : emit('action', item.action, props.ptData);
};

const emit = defineEmits<{
    (e: 'action', action: any, ptData: Record<string, any>): void;
}>();
</script>
