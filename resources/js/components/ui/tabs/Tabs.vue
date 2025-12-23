<script setup lang="ts">
import { ref, computed, provide } from 'vue';

interface Props {
    modelValue?: string;
    defaultValue?: string;
    variant?: 'underline' | 'pills' | 'boxed';
}

const props = withDefaults(defineProps<Props>(), {
    variant: 'underline',
});

const emit = defineEmits<{
    'update:modelValue': [value: string];
}>();

const internalValue = ref(props.defaultValue || '');

const activeTab = computed({
    get: () => props.modelValue ?? internalValue.value,
    set: (val) => {
        internalValue.value = val;
        emit('update:modelValue', val);
    },
});

provide('tabsContext', {
    activeTab,
    variant: computed(() => props.variant),
});
</script>

<template>
    <div class="w-full">
        <slot />
    </div>
</template>
