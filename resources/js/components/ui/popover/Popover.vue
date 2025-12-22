<template>
    <PopoverBase v-model:open="isOpen">
        <PopoverTrigger as-child>
            <slot name="trigger" />
        </PopoverTrigger>
        <PopoverContent :class="cn('w-auto', className)">
            <slot />
        </PopoverContent>
    </PopoverBase>
</template>

<script setup lang="ts">
import { ref, computed } from 'vue';
import PopoverBase from './PopoverBase.vue';
import PopoverContent from './PopoverContent.vue';
import PopoverTrigger from './PopoverTrigger.vue';
import { cn } from '@/utils';

interface Props {
    class?: string;
}

const props = defineProps<Props>();

const className = computed(() => props.class || '');
const isOpen = ref(false);

defineExpose({
    toggle: (event: any) => { isOpen.value = !isOpen.value; },
    show: (event: any) => { isOpen.value = true; },
    hide: () => { isOpen.value = false; },
    isOpen
});
</script>
