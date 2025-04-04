<template>
    <div class="relative">
        <div
            v-if="user && user?.id"
            class="flex justify-content-center"
        >
            <button
                type="button"
                aria-haspopup="true"
                aria-controls="overlay_menu"
                class="h-8 rounded-md inline-flex justify-center items-center bg-surface-100 dark:bg-surface-800 hover:bg-surface-800 dark:hover:bg-surface-700 text-surface-600 hover:text-surface-900 dark:text-surface-300 dark:hover:text-surface-200 transition-colors duration-200 text-sm px-2"
                @click="toggle"
            >
                <div class="pr-1">
                    {{ user.name }}
                </div> <span class="pi pi-fw pi-angle-down" />
            </button>
            <VoltMenu
                ref="menu"
                :model="items"
                class="w-[245px]"
                popup
            >
                <template #start>
                    <button
                        class="relative overflow-hidden w-full p-link flex items-center p-2 pl-3 text-surface-800 dark:text-white hover:bg-surface-200 dark:hover:bg-surface-600 border-noround"
                    >
                        <div>
                            <VoltAvatar
                                :label="user.name[0]"
                                class="mr-2 border border-surface-200 dark:border-surface-400"
                                shape="circle"
                            />
                        </div>
                        <div class="flex flex-col text-left flex-1 min-w-0">
                            <span class="font-bold truncate">{{ user.name }}</span>
                            <span class="text-sm truncate">{{ user.email }}</span>
                        </div>
                    </button>
                </template>
                <template #item="{ item, props }">
                    <Link
                        class="flex align-items-center"
                        :href="item.href"
                        v-bind="props.action"
                    >
                        <span :class="item.icon" />
                        <span class="ml-2 text-sm">{{ item.label }}</span>
                        <Badge
                            v-if="item.badge"
                            class="ml-auto"
                            :value="item.badge"
                        />
                        <span
                            v-if="item.shortcut"
                            class="ml-auto border-1 surface-border border-round surface-100 text-xs p-1"
                        >{{ item.shortcut }}</span>
                    </Link>
                </template>
            </VoltMenu>
        </div>
        <div v-else>
            <Link
                href="/login"
                class="h-8 rounded-md inline-flex justify-center items-center bg-surface-100 dark:bg-surface-800 hover:bg-surface-800 dark:hover:bg-surface-700 text-surface-600 hover:text-surface-900 dark:text-surface-300 dark:hover:text-surface-200 transition-colors duration-200 text-sm px-2"
            >
                Login or Register
            </Link>
        </div>
    </div>
</template>

<script setup>
const page = usePage()
const user = computed(() => page.props?.user)
const menu = ref();
const items = ref([
    {
        separator: true
    },
    {
        label: 'Company Name',
        icon: 'pi pi-building-columns',
        href: '/'
    },
    {
        separator: true
    },
    {
        label: 'Billing & Plan',
        icon: 'pi pi-credit-card',
        href: '/'
    },
    {
        label: 'Manage Access',
        icon: 'pi pi-users',
        href: '/'
    },
    {
        label: 'Integrations',
        icon: 'pi pi-objects-column',
        href: '/'
    },
    {
        label: 'Settings',
        icon: 'pi pi-cog',
        href: '/'
    },
    {
        separator: true
    },
    {
        label: 'Logout',
        icon: 'pi pi-sign-out',
        href: '/logout'
    }
]);
const toggle = (event) => {
    menu.value.toggle(event);
};
</script>
