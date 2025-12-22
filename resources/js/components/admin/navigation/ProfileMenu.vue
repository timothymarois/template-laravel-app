<template>
    <div class="relative">
        <div v-if="user?.id" class="flex justify-center items-center">
            <DropdownMenu>
                <DropdownMenuTrigger as-child>
                    <button
                        class="relative overflow-hidden w-full mr-2 flex items-center p-2 px-3 rounded-md text-foreground hover:bg-accent cursor-pointer"
                        :class="{ '!p-1 !py-2 !mr-0': avatarOnly }"
                    >
                        <Avatar
                            :label="user.name?.charAt(0).toUpperCase()"
                            class="m-auto border border-border bg-muted text-foreground"
                            shape="circle"
                        />
                        <div v-if="!avatarOnly" class="ml-2 flex flex-col text-left flex-1 min-w-0">
                            <span class="font-bold truncate">{{ user.name }}</span>
                            <span class="text-sm truncate">{{ user.email }}</span>
                        </div>
                    </button>
                </DropdownMenuTrigger>
                <DropdownMenuContent align="end" class="w-[245px]">
                    <!-- Avatar header for avatar-only mode -->
                    <div v-if="avatarOnly" class="p-1 pb-0">
                        <component
                            :is="headerLink ? linkComponent : 'div'"
                            :href="headerLink"
                            class="relative overflow-hidden w-full flex items-center p-2 rounded"
                            :class="headerLink
                                ? 'text-foreground hover:bg-accent cursor-pointer'
                                : 'text-foreground cursor-default'"
                        >
                            <Avatar
                                :label="user?.name?.charAt(0).toUpperCase()"
                                class="mr-2 border border-border"
                                shape="circle"
                            />
                            <div class="flex flex-col text-left flex-1 min-w-0">
                                <span class="font-bold truncate">{{ user?.name }}</span>
                                <span class="text-sm truncate">{{ user?.email }}</span>
                            </div>
                        </component>
                    </div>

                    <!-- Menu items -->
                    <template v-for="(item, index) in items" :key="index">
                        <DropdownMenuSeparator v-if="item.separator" />
                        <DropdownMenuItem v-else as-child>
                            <component
                                :is="item.external ? 'a' : linkComponent"
                                :href="item.href"
                                :target="item.external ? '_blank' : undefined"
                                class="flex items-center justify-between w-full cursor-pointer"
                            >
                                <div class="flex items-center">
                                    <component
                                        :is="item.icon"
                                        v-if="item.icon && typeof item.icon !== 'string'"
                                        class="h-4 w-4 mr-2"
                                    />
                                    <span v-else-if="item.icon" :class="item.icon" class="mr-2" />
                                    <span>{{ item.label }}</span>
                                </div>
                                <IconArrowUpRight
                                    v-if="item.external"
                                    class="ml-2 w-4 h-4 opacity-50"
                                />
                            </component>
                        </DropdownMenuItem>
                    </template>
                </DropdownMenuContent>
            </DropdownMenu>
        </div>
    </div>
</template>

<script setup>
import { IconArrowUpRight } from '@tabler/icons-vue';
import { Avatar } from '@/components/composed';
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuItem,
    DropdownMenuSeparator,
    DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu';

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
</script>
