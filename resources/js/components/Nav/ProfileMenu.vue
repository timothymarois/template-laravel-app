<template>
    <div class="relative">
        <div
            v-if="user && user?.id"
            class="flex justify-content-center justify-center items-center"
        >
            <button
                class="relative overflow-hidden w-full mr-2 p-link flex items-center p-2 px-3 rounded-md text-surface-800 dark:text-white hover:bg-surface-200 dark:hover:bg-surface-600 border-noround cursor-pointer"
                :class="{ '!p-1 !py-2 !mr-0' : avatarOnly }"
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
            <Menu
                ref="menu"
                :model="items"
                class="w-[245px] !z-[99999]"
                popup
            >
                <template v-if="avatarOnly" #start>
                    <button
                        class="relative overflow-hidden w-full p-link flex items-center p-2 pl-3 text-surface-800 dark:text-white hover:bg-surface-200 dark:hover:bg-surface-600 border-noround"
                        @click="userEditModal = true"
                    >
                        <div>
                            <Avatar
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
            </Menu>
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
    <Dialog v-model:visible="userEditModal" modal header="Edit Profile" :draggable="false" :dismissable-mask="true" :style="{ width: '25rem' }" @update:visible="userEditModal=false">
        <span class="text-surface-500 dark:text-surface-400 block mb-8">Update your information.</span>
        <div class="flex items-center gap-4 mb-4">
            <label for="username" class="font-semibold w-24">Username</label>
            <InputText id="username" class="flex-auto" autocomplete="off" />
        </div>
        <div class="flex items-center gap-4 mb-8">
            <label for="email" class="font-semibold w-24">Email</label>
            <InputText id="email" class="flex-auto" autocomplete="off" />
        </div>
        <div class="flex justify-end gap-2">
            <Button type="button" label="Cancel" severity="secondary" @click="visible = false"></Button>
            <Button type="button" label="Save" @click="visible = false"></Button>
        </div>
    </Dialog>
    <Dialog v-model:visible="userDataModal" modal :draggable="false" :dismissable-mask="true" header="Data Modal" :style="{ width: '25rem' }" @update:visible="userDataModal=false">
        {{ userData }}
    </Dialog>
</template>

<script setup>
import { useModal } from '@atlas/composables';

const props = defineProps({
    avatarOnly: {
        type: Boolean,
        default: false
    }
});

const { activeState, data } = useModal();

const userEditModal = activeState('userEditModal');
const userDataModal = activeState('userDataModal');
const userData = data('userDataModal');

const page = usePage();
const user = computed(() => page.props.user);
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
