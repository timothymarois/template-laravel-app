<template>
    <nav class="dark bg-surface-800">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="flex h-16 items-center justify-between">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" viewBox="0 0 256 264"><path fill="#ff2d20" d="M255.856 59.62c.095.351.144.713.144 1.077v56.568c0 1.478-.79 2.843-2.073 3.578L206.45 148.18v54.18a4.14 4.14 0 0 1-2.062 3.579l-99.108 57.053c-.227.128-.474.21-.722.299c-.093.03-.18.087-.278.113a4.15 4.15 0 0 1-2.114 0c-.114-.03-.217-.093-.325-.134c-.227-.083-.464-.155-.68-.278L2.073 205.938A4.13 4.13 0 0 1 0 202.36V32.656c0-.372.052-.733.144-1.083c.031-.119.103-.227.145-.346c.077-.216.15-.438.263-.639c.077-.134.19-.242.283-.366c.119-.165.227-.335.366-.48c.119-.118.274-.206.408-.309c.15-.124.283-.258.453-.356h.005L51.613.551a4.14 4.14 0 0 1 4.125 0l49.546 28.526h.01c.165.104.305.232.454.351c.134.103.284.196.402.31c.145.149.248.32.371.484c.088.124.207.232.279.366c.118.206.185.423.268.64c.041.118.113.226.144.35c.095.351.144.714.145 1.078V138.65l41.286-23.773V60.692c0-.36.052-.727.145-1.072c.036-.124.103-.232.144-.35c.083-.217.155-.44.268-.64c.077-.134.19-.242.279-.366c.123-.165.226-.335.37-.48c.12-.118.269-.206.403-.309c.155-.124.289-.258.454-.356h.005l49.551-28.526a4.13 4.13 0 0 1 4.125 0l49.546 28.526c.175.103.309.232.464.35c.128.104.278.197.397.31c.144.15.247.32.37.485c.094.124.207.232.28.366c.118.2.185.423.267.64c.047.118.114.226.145.35m-8.115 55.258v-47.04l-17.339 9.981l-23.953 13.792v47.04l41.297-23.773zm-49.546 85.095V152.9l-23.562 13.457l-67.281 38.4v47.514zM8.259 39.796v160.177l90.833 52.294v-47.505L51.64 177.906l-.015-.01l-.02-.01c-.16-.093-.295-.227-.444-.34c-.13-.104-.279-.186-.392-.3l-.01-.015c-.134-.129-.227-.289-.34-.433c-.104-.14-.227-.258-.31-.402l-.005-.016c-.093-.154-.15-.34-.217-.515c-.067-.155-.154-.3-.196-.464v-.005c-.051-.196-.061-.403-.082-.604c-.02-.154-.062-.309-.062-.464V63.57L25.598 49.772l-17.339-9.97zM53.681 8.893L12.399 32.656l41.272 23.762L94.947 32.65L53.671 8.893zm21.468 148.298l23.948-13.786V39.796L81.76 49.778L57.805 63.569v103.608zM202.324 36.935l-41.276 23.762l41.276 23.763l41.271-23.768zm-4.13 54.676l-23.953-13.792l-17.338-9.981v47.04l23.948 13.787l17.344 9.986zm-94.977 106.006l60.543-34.564l30.264-17.272l-41.246-23.747l-47.489 27.34l-43.282 24.918z"/></svg>
                    </div>
                    <div class="hidden md:block">
                        <div class="ml-10 flex items-baseline space-x-4">
                        <Link :class="{'bg-surface-900 text-white' : $page.url === '/' }" href="/" class="rounded-md px-3 py-2 text-sm font-medium text-gray-300 hover:bg-gray-700 hover:text-white" aria-current="page">Home</Link>
                        <Link :class="{'bg-surface-900 text-white' : $page.url.startsWith('/admin/users') }" href="/admin/users" class="rounded-md px-3 py-2 text-sm font-medium text-gray-300 hover:bg-gray-700 hover:text-white">Users</Link>
                        <Link :class="{'bg-surface-900 text-white' : $page.url.startsWith('/example/store') }" href="/example/store" class="rounded-md px-3 py-2 text-sm font-medium text-gray-300 hover:bg-gray-700 hover:text-white">Store</Link>
                        </div>
                    </div>
                </div>
                <div class="relative flex space-x-2 items-center">
                    <div class="relative">
                        <OptionsDarkToggle />
                    </div>
                    <div class="relative">
                        <OptionsColorPalette />
                    </div>
                    <div v-if="user && user?.id" class="flex justify-content-center">
                        <button
                            type="button"
                            aria-haspopup="true"
                            aria-controls="overlay_menu"
                            @click="toggle"
                            class="h-8 rounded-md inline-flex justify-center items-center bg-surface-100 dark:bg-surface-800 hover:bg-surface-800 dark:hover:bg-surface-700 text-surface-600 hover:text-surface-900 dark:text-surface-300 dark:hover:text-surface-200 transition-colors duration-200 text-sm px-2">
                            <div class="pr-1">{{ user.name }}</div> <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="size-3"><path fill-rule="evenodd" d="M12.53 16.28a.75.75 0 0 1-1.06 0l-7.5-7.5a.75.75 0 0 1 1.06-1.06L12 14.69l6.97-6.97a.75.75 0 1 1 1.06 1.06l-7.5 7.5Z" clip-rule="evenodd" /></svg>
                        </button>
                        <Menu
                            ref="menu"
                            :model="items"
                            class="w-[245px]"
                            :popup="true"
                        >
                            <template #start>
                                <button
                                    class="relative overflow-hidden w-full p-link flex items-center p-2 pl-3 text-color hover:bg-surface-200 dark:hover:bg-surface-600 border-noround">
                                    <!-- <Avatar image="https://primefaces.org/cdn/primevue/images/avatar/amyelsner.png" class="mr-2" shape="circle" /> -->
                                    <Avatar :label="user.name[0]"  class="mr-4 border border-surface-200 dark:border-surface-400" shape="circle" />
                                    <div class="flex flex-col text-left">
                                        <span class="font-bold">{{ user.name }}</span>
                                        <span class="text-sm">{{ user.email }}</span>
                                    </div>
                                </button>
                            </template>
                            <template #item="{ item, props }">
                                <Link class="flex align-items-center" :href="item.href" v-bind="props.action">
                                    <span :class="item.icon" />
                                    <span class="ml-2 text-sm">{{ item.label }}</span>
                                    <Badge v-if="item.badge" class="ml-auto" :value="item.badge" />
                                    <span v-if="item.shortcut" class="ml-auto border-1 surface-border border-round surface-100 text-xs p-1">{{ item.shortcut }}</span>
                                </Link>
                            </template>
                        </Menu>
                    </div>
                    <div v-else>
                        <Link href="/login" class="h-8 rounded-md inline-flex justify-center items-center bg-surface-100 dark:bg-surface-800 hover:bg-surface-800 dark:hover:bg-surface-700 text-surface-600 hover:text-surface-900 dark:text-surface-300 dark:hover:text-surface-200 transition-colors duration-200 text-sm px-2">Login or Register</Link>
                    </div>
                </div>
            </div>
        </div>
    </nav>
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
