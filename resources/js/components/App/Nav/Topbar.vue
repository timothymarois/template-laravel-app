<template>
    <nav
        class="w-full border-b shadow-lg"
        :class="containerClass"
    >
        <div class="mx-auto px-4" :class="widthClass">
            <div class="flex h-16 items-center justify-between">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <component :is="linkComponent" :href="logoLinkPath" class="inline-flex items-center h-8">
                            <slot name="logo">
                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    class="h-8 w-8"
                                    viewBox="0 0 256 264"
                                ><path
                                    fill="#ff2d20"
                                    d="M255.856 59.62c.095.351.144.713.144 1.077v56.568c0 1.478-.79 2.843-2.073 3.578L206.45 148.18v54.18a4.14 4.14 0 0 1-2.062 3.579l-99.108 57.053c-.227.128-.474.21-.722.299c-.093.03-.18.087-.278.113a4.15 4.15 0 0 1-2.114 0c-.114-.03-.217-.093-.325-.134c-.227-.083-.464-.155-.68-.278L2.073 205.938A4.13 4.13 0 0 1 0 202.36V32.656c0-.372.052-.733.144-1.083c.031-.119.103-.227.145-.346c.077-.216.15-.438.263-.639c.077-.134.19-.242.283-.366c.119-.165.227-.335.366-.48c.119-.118.274-.206.408-.309c.15-.124.283-.258.453-.356h.005L51.613.551a4.14 4.14 0 0 1 4.125 0l49.546 28.526h.01c.165.104.305.232.454.351c.134.103.284.196.402.31c.145.149.248.32.371.484c.088.124.207.232.279.366c.118.206.185.423.268.64c.041.118.113.226.144.35c.095.351.144.714.145 1.078V138.65l41.286-23.773V60.692c0-.36.052-.727.145-1.072c.036-.124.103-.232.144-.35c.083-.217.155-.44.268-.64c.077-.134.19-.242.279-.366c.123-.165.226-.335.37-.48c.12-.118.269-.206.403-.309c.155-.124.289-.258.454-.356h.005l49.551-28.526a4.13 4.13 0 0 1 4.125 0l49.546 28.526c.175.103.309.232.464.35c.128.104.278.197.397.31c.144.15.247.32.37.485c.094.124.207.232.28.366c.118.2.185.423.267.64c.047.118.114.226.145.35m-8.115 55.258v-47.04l-17.339 9.981l-23.953 13.792v47.04l41.297-23.773zm-49.546 85.095V152.9l-23.562 13.457l-67.281 38.4v47.514zM8.259 39.796v160.177l90.833 52.294v-47.505L51.64 177.906l-.015-.01l-.02-.01c-.16-.093-.295-.227-.444-.34c-.13-.104-.279-.186-.392-.3l-.01-.015c-.134-.129-.227-.289-.34-.433c-.104-.14-.227-.258-.31-.402l-.005-.016c-.093-.154-.15-.34-.217-.515c-.067-.155-.154-.3-.196-.464v-.005c-.051-.196-.061-.403-.082-.604c-.02-.154-.062-.309-.062-.464V63.57L25.598 49.772l-17.339-9.97zM53.681 8.893L12.399 32.656l41.272 23.762L94.947 32.65L53.671 8.893zm21.468 148.298l23.948-13.786V39.796L81.76 49.778L57.805 63.569v103.608zM202.324 36.935l-41.276 23.762l41.276 23.763l41.271-23.768zm-4.13 54.676l-23.953-13.792l-17.338-9.981v47.04l23.948 13.787l17.344 9.986zm-94.977 106.006l60.543-34.564l30.264-17.272l-41.246-23.747l-47.489 27.34l-43.282 24.918z"
                                /></svg>
                            </slot>
                        </component>
                    </div>
                    <div class="hidden md:flex ml-10 items-baseline space-x-4">
                        <template v-for="item in items" :key="item.href">
                            <component
                                v-if="!item.children"
                                :is="linkComponent"
                                :href="item.href"
                                class="rounded-[var(--p-content-border-radius)] px-3 py-2 text-sm font-medium"
                                :class="linkClass(item)"
                            >
                                {{ item.label }}
                            </component>
                            <div
                                v-else
                                class="rounded-[var(--p-content-border-radius)] px-3 py-2 text-sm font-medium cursor-pointer"
                                :class="linkClass(item)"
                                @click="toggleMenu(item.href, $event)"
                            >
                                {{ item.label }}
                                <span class="pi pi-fw pi-angle-down ml-2" />
                                <Menu
                                    :ref="setMenuRef(item.href)"
                                    :model="item.children"
                                    popup
                                >
                                    <template #item="{ item, props }">
                                        <component
                                            :is="linkComponent"
                                            v-bind="props.action"
                                            :href="item.href"
                                            class="flex items-center w-full px-2 py-1 text-sm rounded-[var(--p-content-border-radius)]"
                                            :class="isActive(item) ? menuActiveClass : ''"
                                        >
                                            <span :class="item.icon" />
                                            <span class="ml-2">{{ item.label }}</span>
                                        </component>
                                    </template>
                                </Menu>
                            </div>
                        </template>
                    </div>
                </div>
                <div v-if="hasActions" class="relative flex space-x-2 items-center">
                    <slot name="actions" />
                </div>
            </div>
        </div>
    </nav>
</template>

<script setup>
import { reactive, useSlots, computed } from 'vue';
import { hasSlotContent } from '../../../utils';
import { isPageActive } from '../../../utils/vue/inertia';
import Menu from '../../Menu.vue';

const slots = useSlots();

const props = defineProps({
    items: {
        type: Array,
        required: true,
    },
    linkComponent: {
        type: [String, Object],
        default: 'a',
    },
    logoLinkPath: {
        type: String,
        default: '/',
    },
    widthClass: {
        type: String,
        default: 'max-w-screen-2xl',
    },
    autoDark: {
        type: Boolean,
        default: false,
    },
    backgroundClass: {
        type: String,
        default: '',
    },
    activeClass: {
        type: String,
        default: '',
    },
});

const menuRefs = reactive({});

const setMenuRef = (key) => (el) => {
    if (el) menuRefs[key] = el;
};

const toggleMenu = (key, event) => {
    const menu = menuRefs[key];
    if (menu) menu.toggle(event);
};

const isActive = (item) => {
    if (item.parent) {
        return isPageActive(item.parent);
    }
    if (item.href === '/') {
        return isPageActive(item.href, undefined, true);
    }
    return isPageActive(item.href);
};

const hasActions = computed(() => hasSlotContent(slots.actions));

const containerClass = computed(() => {
    const bg = props.backgroundClass || (props.autoDark
        ? 'bg-surface-100 dark:bg-surface-900 border-surface-200 dark:border-surface-700'
        : 'dark bg-surface-900 border-surface-700');
    return bg;
});

const menuActiveClass = computed(() => {
    return props.activeClass || 'bg-white text-primary-900';
});

const linkClass = (item) => {
    const activeCls = props.activeClass || 'bg-white text-black';
    if (props.autoDark) {
        const baseCls = 'text-surface-700 dark:text-surface-300 hover:bg-surface-200 dark:hover:bg-primary-800 hover:text-surface-900 dark:hover:text-white';
        return isActive(item) ? activeCls : baseCls;
    }
    const baseCls = 'text-gray-300 hover:bg-surface-600/50 hover:text-white';
    return isActive(item) ? activeCls : baseCls;
};
</script>
