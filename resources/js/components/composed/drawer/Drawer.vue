<template>
    <Sheet v-model:open="isOpen">
        <SheetContent :side="computedSide" :class="className">
            <SheetHeader v-if="$slots.header || header">
                <SheetTitle>
                    <slot name="header">{{ header }}</slot>
                </SheetTitle>
                <SheetDescription v-if="$slots.subheader">
                    <slot name="subheader" />
                </SheetDescription>
            </SheetHeader>
            <div class="flex-1 overflow-y-auto py-4">
                <slot />
            </div>
            <SheetFooter v-if="$slots.footer">
                <slot name="footer" />
            </SheetFooter>
        </SheetContent>
    </Sheet>
</template>

<script setup lang="ts">
import { computed } from 'vue';
import {
    Sheet,
    SheetContent,
    SheetDescription,
    SheetFooter,
    SheetHeader,
    SheetTitle,
} from '@/components/ui/sheet';

interface Props {
    visible?: boolean;
    header?: string;
    position?: 'left' | 'right' | 'top' | 'bottom' | 'full';
    modal?: boolean;
    class?: string;
}

const props = withDefaults(defineProps<Props>(), {
    visible: false,
    position: 'right',
    modal: true,
});

const emit = defineEmits<{
    'update:visible': [value: boolean];
}>();

const className = computed(() => props.class || '');

const isOpen = computed({
    get: () => props.visible,
    set: (val) => emit('update:visible', val),
});

const computedSide = computed(() => {
    if (props.position === 'full') return 'right'; // Full screen not directly supported
    return props.position;
});
</script>
