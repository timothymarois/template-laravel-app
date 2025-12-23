<script setup lang="ts">
import { provide } from 'vue';

interface Props {
    value: string;
    disabled?: boolean;
}

const props = defineProps<Props>();

provide('accordionItemContext', {
    value: props.value,
    disabled: props.disabled,
});
</script>

<template>
    <div
        :data-state="disabled ? 'disabled' : undefined"
        :data-disabled="disabled ? '' : undefined"
    >
        <slot />
    </div>
</template>
