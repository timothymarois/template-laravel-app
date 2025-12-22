<template>
    <DropdownMenu>
        <DropdownMenuTrigger as-child>
            <slot name="trigger">
                <Button variant="ghost" size="icon" class="h-8 w-8">
                    <MoreVertical class="h-4 w-4" />
                </Button>
            </slot>
        </DropdownMenuTrigger>
        <DropdownMenuContent align="start" class="min-w-[160px]">
            <template v-for="(item, index) in items" :key="index">
                <DropdownMenuSeparator v-if="item.separator" />
                <DropdownMenuItem
                    v-else
                    :disabled="item.disabled"
                    @click="handleClick(item)"
                >
                    <component
                        :is="item.icon"
                        v-if="item.icon && typeof item.icon !== 'string'"
                        class="mr-2 h-4 w-4"
                    />
                    <span v-else-if="item.icon" :class="item.icon" class="mr-2" />
                    {{ item.label }}
                </DropdownMenuItem>
            </template>
        </DropdownMenuContent>
    </DropdownMenu>
</template>

<script setup lang="ts">
import { Button } from '@/components/ui/button';
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuItem,
    DropdownMenuSeparator,
    DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu';
import { MoreVertical } from 'lucide-vue-next';

interface MenuItem {
    label?: string;
    icon?: any;
    disabled?: boolean;
    separator?: boolean;
    click?: () => void;
    action?: string;
    children?: MenuItem[];
}

interface Props {
    items?: MenuItem[];
    ptData?: Record<string, any>;
}

const props = withDefaults(defineProps<Props>(), {
    items: () => [],
    ptData: () => ({}),
});

const emit = defineEmits<{
    action: [action: any, ptData: Record<string, any>];
}>();

const handleClick = (item: MenuItem) => {
    if (item.disabled) return;
    if (item.click) {
        item.click();
    } else if (item.action) {
        emit('action', item.action, props.ptData);
    }
};
</script>
