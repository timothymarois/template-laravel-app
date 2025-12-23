<template>
    <DialogBase v-model:open="isOpen">
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
    </DialogBase>
</template>

<script setup lang="ts">
import { computed } from 'vue';
import DialogBase from './DialogBase.vue';
import DialogContent from './DialogContent.vue';
import DialogDescription from './DialogDescription.vue';
import DialogFooter from './DialogFooter.vue';
import DialogHeader from './DialogHeader.vue';
import DialogTitle from './DialogTitle.vue';

interface Props {
    visible?: boolean;
    header?: string;
    class?: string;
}

const props = withDefaults(defineProps<Props>(), {
    visible: false,
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
