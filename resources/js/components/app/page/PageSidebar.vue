<template>
    <SidebarProvider :default-open="true" class="!min-h-0 w-auto">
        <Sidebar collapsible="none" class="border-r bg-sidebar w-64">
            <SidebarHeader v-if="title" class="border-b px-4 py-3">
                <span class="font-semibold text-sm">{{ title }}</span>
            </SidebarHeader>
            <SidebarContent>
                <SidebarGroup>
                    <SidebarMenu>
                        <template v-for="item in items" :key="item.label">
                            <!-- Item with children - collapsible -->
                            <Collapsible
                                v-if="item.children?.length"
                                :default-open="isGroupActive(item)"
                                class="group/collapsible"
                            >
                                <SidebarMenuItem>
                                    <CollapsibleTrigger as-child>
                                        <SidebarMenuButton>
                                            <component v-if="item.icon" :is="item.icon" class="size-4" />
                                            <span>{{ item.label }}</span>
                                            <ChevronRight class="ml-auto size-4 transition-transform duration-200 group-data-[state=open]/collapsible:rotate-90" />
                                        </SidebarMenuButton>
                                    </CollapsibleTrigger>
                                    <CollapsibleContent>
                                        <SidebarMenuSub>
                                            <SidebarMenuSubItem v-for="child in item.children" :key="child.href">
                                                <SidebarMenuSubButton
                                                    as-child
                                                    :is-active="isActive(child)"
                                                >
                                                    <component
                                                        :is="linkComponent"
                                                        :href="child.href"
                                                        draggable="false"
                                                    >
                                                        <component v-if="child.icon" :is="child.icon" class="size-4" />
                                                        <span>{{ child.label }}</span>
                                                    </component>
                                                </SidebarMenuSubButton>
                                            </SidebarMenuSubItem>
                                        </SidebarMenuSub>
                                    </CollapsibleContent>
                                </SidebarMenuItem>
                            </Collapsible>

                            <!-- Simple item without children -->
                            <SidebarMenuItem v-else>
                                <SidebarMenuButton
                                    as-child
                                    :is-active="isActive(item)"
                                >
                                    <component
                                        :is="linkComponent"
                                        :href="item.href"
                                        draggable="false"
                                    >
                                        <component v-if="item.icon" :is="item.icon" class="size-4" />
                                        <span>{{ item.label }}</span>
                                    </component>
                                </SidebarMenuButton>
                            </SidebarMenuItem>
                        </template>
                    </SidebarMenu>
                </SidebarGroup>
            </SidebarContent>
        </Sidebar>
    </SidebarProvider>
</template>

<script setup lang="ts">
import { ChevronRight } from 'lucide-vue-next';
import { Collapsible, CollapsibleContent, CollapsibleTrigger } from '@/components/ui/collapsible';
import {
    Sidebar,
    SidebarContent,
    SidebarGroup,
    SidebarHeader,
    SidebarMenu,
    SidebarMenuButton,
    SidebarMenuItem,
    SidebarMenuSub,
    SidebarMenuSubButton,
    SidebarMenuSubItem,
    SidebarProvider,
} from '@/components/ui/sidebar';
import { isPageActive } from '@/utils/vue/inertia';

interface NavItem {
    label: string;
    href?: string;
    parent?: string;
    icon?: any;
    children?: NavItem[];
}

interface Props {
    items?: NavItem[];
    title?: string;
    linkComponent?: string | object;
}

const props = withDefaults(defineProps<Props>(), {
    items: () => [],
    linkComponent: 'a',
});

const isActive = (item: NavItem) => {
    if (!item.href) return false;
    return item.parent ? isPageActive(item.parent) : isPageActive(item.href, undefined, true);
};

const isGroupActive = (item: NavItem) => {
    if (!item.children?.length) return false;
    return item.children.some(child => isActive(child));
};
</script>
