<template>
    <div class="relative">
        <div v-if="user?.id" class="flex justify-center items-center">
            <slot name="trigger" :user="user" :toggle="toggle">
                <button
                    class="relative overflow-hidden w-full mr-2 p-link flex items-center p-2 px-3 rounded-md text-surface-800 dark:text-white hover:bg-surface-200 dark:hover:bg-surface-600 border-noround cursor-pointer"
                    :class="{ '!p-1 !py-2 !mr-0': avatarOnly }"
                    @click="toggle"
                >
                    <Avatar
                        :label="user.name?.charAt(0).toUpperCase()"
                        class="m-auto border border-surface-200 dark:border-surface-400 bg-surface-100 text-surface-800 dark:bg-surface-700 dark:text-white"
                        shape="circle"
                    />
                    <div v-if="!avatarOnly" class="ml-2 flex flex-col text-left flex-1 min-w-0">
                        <span class="font-bold truncate">{{ user.name }}</span>
                        <span class="text-sm truncate">{{ user.email }}</span>
                    </div>
                </button>
            </slot>
            <Menu ref="menu" :model="menuItems" class="w-[245px] !z-[99999]" popup>
                <template v-if="avatarOnly" #start>
                    <slot name="avatarMenuHeader">
                        <div class="p-1 pb-0">
                            <component
                                :is="headerLink ? linkComponent : 'div'"
                                :href="headerLink"
                                class="relative overflow-hidden w-full p-link flex items-center p-2 rounded"
                                :class="headerLink
                                    ? 'text-surface-800 dark:text-white hover:bg-surface-100 dark:hover:bg-surface-600 cursor-pointer'
                                    : 'text-surface-800 dark:text-white cursor-default'"
                            >
                                <Avatar
                                    :label="user?.name?.charAt(0).toUpperCase()"
                                    class="mr-2 border border-surface-200 dark:border-surface-400"
                                    shape="circle"
                                />
                                <div class="flex flex-col text-left flex-1 min-w-0">
                                    <span class="font-bold truncate">{{ user?.name }}</span>
                                    <span class="text-sm truncate">{{ user?.email }}</span>
                                </div>
                            </component>
                        </div>
                    </slot>
                </template>
                <template #item="{ item, props }">
                    <component
                        :is="item.external ? 'a' : linkComponent"
                        :href="item.href"
                        v-bind="props.action"
                        class="flex items-center justify-between text-surface-800 dark:text-white hover:bg-surface-100 dark:hover:bg-surface-700 p-2 px-3 w-full rounded"
                    >
                        <div class="flex align-items-center items-center">
                            <span :class="item.icon" />
                            <span class="ml-2 text-sm">{{ item.label }}</span>
                            <Badge
                                v-if="item.badge"
                                class="ml-auto"
                                :value="item.badge"
                            />
                            <span v-if="item.shortcut" class="ml-auto border border-surface-300 rounded bg-surface-100 text-xs p-1">
                                {{ item.shortcut }}
                            </span>
                        </div>
                        <IconArrowUpRight
                            v-if="item.external"
                            class="ml-2 w-4 h-4 opacity-50"
                        />
                    </component>
                </template>
            </Menu>
        </div>
    </div>
</template>

<script setup>
import { ref, computed } from 'vue';
import { IconArrowUpRight } from '@tabler/icons-vue';
import Avatar from '../../Avatar.vue';
import Badge from '../../Badge.vue';
import Menu from '../../Menu.vue';

const props = defineProps({
    avatarOnly: {
        type: Boolean,
        default: false
    },
    user: {
        type: Object,
        default: () => ({})
    },
    items: {
        type: Array,
        default: () => ([])
    },
    headerLink: {
        type: String,
        default: null
    },
    linkComponent: {
        type: [String, Object],
        default: 'a'
    }
});

const menu = ref();
const menuItems = computed(() => props.items);

const toggle = (event) => {
    menu.value.toggle(event);
};
</script>

