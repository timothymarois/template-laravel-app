<script setup lang="ts">
import type { HTMLAttributes } from "vue";
import { computed } from "vue";
import { SwitchRoot, SwitchThumb } from "reka-ui";
import { cn } from '@/utils';

interface Props {
    class?: HTMLAttributes["class"]
    size?: "sm" | "default"
    modelValue?: boolean
    defaultValue?: boolean
    disabled?: boolean
}

const props = withDefaults(defineProps<Props>(), {
    size: "default",
    modelValue: undefined,
    defaultValue: undefined,
    disabled: false,
});

const emit = defineEmits<{
    'update:modelValue': [value: boolean]
}>();

const sizeClasses = computed(() => {
    return props.size === "sm"
        ? "h-5 w-9"
        : "h-6 w-11";
});

const thumbSizeClasses = computed(() => {
    return props.size === "sm"
        ? "h-4 w-4 data-[state=checked]:translate-x-4"
        : "h-5 w-5 data-[state=checked]:translate-x-5";
});
</script>

<template>
    <SwitchRoot
        :model-value="props.modelValue"
        :default-value="props.defaultValue"
        :disabled="props.disabled"
        :class="cn(
            'peer inline-flex shrink-0 cursor-pointer items-center rounded-full border-2 border-transparent transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 focus-visible:ring-offset-background disabled:cursor-not-allowed disabled:opacity-50 data-[state=checked]:bg-primary data-[state=unchecked]:bg-input',
            sizeClasses,
            props.class,
        )"
        @update:model-value="emit('update:modelValue', $event)"
    >
        <SwitchThumb
            :class="cn('pointer-events-none block rounded-full bg-background shadow-lg ring-0 transition-transform', thumbSizeClasses)"
        >
            <slot name="thumb" />
        </SwitchThumb>
    </SwitchRoot>
</template>
