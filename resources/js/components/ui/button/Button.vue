<template>
    <ButtonBase
        :type="type"
        :variant="computedVariant"
        :size="computedSize"
        :disabled="disabled || loading"
        :as-child="asChild"
        :class="[
            fluid ? 'w-full' : '',
            className
        ]"
        v-bind="$attrs"
    >
        <!-- When asChild, only render slot (single child required) -->
        <template v-if="asChild">
            <slot>{{ label }}</slot>
        </template>
        <!-- Normal button content with optional icons/loading -->
        <template v-else>
            <Loader2 v-if="loading" class="mr-2 h-4 w-4 animate-spin" />
            <component :is="icon" v-else-if="icon && !iconPos?.includes('right')" class="mr-2 h-4 w-4" />
            <slot>{{ label }}</slot>
            <component :is="icon" v-if="icon && iconPos?.includes('right')" class="ml-2 h-4 w-4" />
        </template>
    </ButtonBase>
</template>

<script setup lang="ts">
import { computed } from 'vue';
import ButtonBase from './ButtonBase.vue';
import { Loader2 } from 'lucide-vue-next';

interface Props {
    label?: string;
    icon?: any;
    iconPos?: 'left' | 'right';
    loading?: boolean;
    disabled?: boolean;
    fluid?: boolean;
    size?: 'small' | 'large' | 'default';
    severity?: 'primary' | 'secondary' | 'success' | 'info' | 'warn' | 'danger' | 'help' | 'contrast';
    outlined?: boolean;
    text?: boolean;
    raised?: boolean;
    rounded?: boolean;
    type?: 'button' | 'submit' | 'reset';
    class?: string;
    asChild?: boolean;
}

const props = withDefaults(defineProps<Props>(), {
    type: 'button',
    size: 'default',
    iconPos: 'left',
});

const className = computed(() => props.class || '');

const computedVariant = computed(() => {
    if (props.text) return 'ghost';
    if (props.outlined) return 'outline';
    if (props.severity === 'secondary') return 'secondary';
    if (props.severity === 'danger') return 'destructive';
    return 'default';
});

const computedSize = computed(() => {
    if (props.size === 'small') return 'sm';
    if (props.size === 'large') return 'lg';
    return 'default';
});
</script>
