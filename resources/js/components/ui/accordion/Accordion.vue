<script setup lang="ts">
import { ref, computed, provide } from 'vue';

interface Props {
    type?: 'single' | 'multiple';
    modelValue?: string | string[];
    defaultValue?: string | string[];
    collapsible?: boolean;
}

const props = withDefaults(defineProps<Props>(), {
    type: 'single',
    collapsible: true,
});

const emit = defineEmits<{
    'update:modelValue': [value: string | string[]];
}>();

const internalValue = ref<string[]>(
    Array.isArray(props.defaultValue)
        ? props.defaultValue
        : props.defaultValue
          ? [props.defaultValue]
          : []
);

const openItems = computed({
    get: () => {
        if (props.modelValue !== undefined) {
            return Array.isArray(props.modelValue) ? props.modelValue : [props.modelValue];
        }
        return internalValue.value;
    },
    set: (val) => {
        internalValue.value = val;
        if (props.type === 'single') {
            emit('update:modelValue', val[0] || '');
        } else {
            emit('update:modelValue', val);
        }
    },
});

const toggle = (value: string) => {
    const isOpen = openItems.value.includes(value);

    if (props.type === 'single') {
        if (isOpen && props.collapsible) {
            openItems.value = [];
        } else if (!isOpen) {
            openItems.value = [value];
        }
    } else {
        if (isOpen) {
            openItems.value = openItems.value.filter((v) => v !== value);
        } else {
            openItems.value = [...openItems.value, value];
        }
    }
};

const isOpen = (value: string) => openItems.value.includes(value);

provide('accordionContext', {
    toggle,
    isOpen,
});
</script>

<template>
    <div class="divide-y divide-border rounded-md border">
        <slot />
    </div>
</template>
