<template>
    <DropdownMenu v-model:open="isOpen">
        <DropdownMenuTrigger as-child>
            <slot name="trigger" />
        </DropdownMenuTrigger>
        <DropdownMenuContent>
            <template v-for="(item, index) in model" :key="index">
                <DropdownMenuSeparator v-if="item.separator" />
                <template v-else-if="item.items">
                    <DropdownMenuLabel class="font-semibold text-muted-foreground">
                        {{ item.label }}
                    </DropdownMenuLabel>
                    <DropdownMenuItem
                        v-for="(subItem, subIndex) in item.items"
                        :key="subIndex"
                        :disabled="subItem.disabled"
                        @click="handleClick(subItem)"
                    >
                        <span v-if="subItem.icon" :class="subItem.icon" class="mr-2" />
                        {{ subItem.label }}
                    </DropdownMenuItem>
                </template>
                <DropdownMenuItem
                    v-else
                    :disabled="item.disabled"
                    @click="handleClick(item)"
                >
                    <span v-if="item.icon" :class="item.icon" class="mr-2" />
                    {{ item.label }}
                </DropdownMenuItem>
            </template>
        </DropdownMenuContent>
    </DropdownMenu>
</template>

<script setup lang="ts">
import { ref, watch } from 'vue';
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuItem,
    DropdownMenuLabel,
    DropdownMenuSeparator,
    DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu';

interface MenuItem {
    label?: string;
    icon?: string;
    disabled?: boolean;
    separator?: boolean;
    command?: (event: any) => void;
    items?: MenuItem[];
}

interface Props {
    model?: MenuItem[];
    popup?: boolean;
}

const props = withDefaults(defineProps<Props>(), {
    model: () => [],
    popup: false,
});

const isOpen = ref(false);

const handleClick = (item: MenuItem) => {
    if (item.disabled) return;
    if (item.command) {
        item.command({ item });
    }
};

// Expose methods for compatibility
defineExpose({
    toggle: (event: Event) => { isOpen.value = !isOpen.value; },
    show: (event: Event) => { isOpen.value = true; },
    hide: () => { isOpen.value = false; }
});
</script>
