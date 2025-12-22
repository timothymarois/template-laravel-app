<template>
    <Dialog v-model:open="isOpen">
        <DialogContent :class="className">
            <DialogHeader v-if="$slots.header || header">
                <DialogTitle>
                    <slot name="header">{{ header }}</slot>
                </DialogTitle>
                <DialogDescription v-if="$slots.subheader">
                    <slot name="subheader" />
                </DialogDescription>
            </DialogHeader>
            <div class="py-4">
                <slot />
            </div>
            <DialogFooter v-if="$slots.footer">
                <slot name="footer" />
            </DialogFooter>
        </DialogContent>
    </Dialog>
</template>

<script setup lang="ts">
import { computed } from 'vue';
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog';

interface Props {
    visible?: boolean;
    header?: string;
    modal?: boolean;
    closable?: boolean;
    maximizable?: boolean;
    class?: string;
}

const props = withDefaults(defineProps<Props>(), {
    visible: false,
    modal: true,
    closable: true,
});

const emit = defineEmits<{
    'update:visible': [value: boolean];
}>();

const className = computed(() => props.class || '');

const isOpen = computed({
    get: () => props.visible,
    set: (val) => emit('update:visible', val),
});
</script>
