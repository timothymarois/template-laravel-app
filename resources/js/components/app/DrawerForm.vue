<template>
    <Sheet v-model:open="isOpen">
        <SheetContent :side="position" class="flex flex-col !max-w-none p-0 overflow-hidden" :style="widthStyle">
            <!-- Header -->
            <div class="px-6 py-4" :class="tabs?.length ? '' : 'border-b border-border'">
                <SheetHeader>
                    <SheetTitle>{{ title }}</SheetTitle>
                </SheetHeader>
            </div>

            <!-- Tabs -->
            <div v-if="tabs?.length" class="flex gap-2 px-6 border-b border-border -mt-2">
                <button
                    v-for="(tab, index) in tabs"
                    :key="index"
                    :disabled="tab.disabled"
                    :class="[
                        'px-4 py-3 text-sm font-medium transition-colors -mb-px',
                        activeTab === index
                            ? 'border-b-2 border-primary text-foreground'
                            : 'text-muted-foreground hover:text-foreground',
                        tab.disabled ? 'opacity-50 cursor-not-allowed' : 'cursor-pointer'
                    ]"
                    @click="!tab.disabled && (activeTab = index)"
                >
                    {{ tab.title }}
                </button>
            </div>

            <!-- Content -->
            <div class="flex-1 min-h-0 overflow-y-auto px-6 py-4">
                <template v-if="tabs?.length">
                    <div v-show="activeTab === 0">
                        <slot />
                    </div>
                    <template v-for="(tab, index) in tabs.slice(1)" :key="index">
                        <div v-show="activeTab === index + 1">
                            <slot :name="`tab-${index + 1}`" />
                        </div>
                    </template>
                </template>
                <template v-else>
                    <slot />
                </template>
            </div>

            <!-- Footer -->
            <div class="bg-muted/30">
                <!-- Errors -->
                <Errors
                    v-if="hasErrors"
                    :errors="errors"
                    :expandDefault="true"
                    class="rounded-none border-0"
                />
                <div class="px-6 py-4 flex items-center gap-2 border-t border-border">
                    <Button @click="$emit('submit')" :disabled="loading">
                        <Loader2 v-if="loading" class="mr-2 h-4 w-4 animate-spin" />
                        Save
                    </Button>
                    <Button variant="ghost" @click="isOpen = false">Cancel</Button>
                </div>
            </div>
        </SheetContent>
    </Sheet>
</template>

<script setup lang="ts">
import { ref, computed } from 'vue';
import {
    Sheet,
    SheetContent,
    SheetFooter,
    SheetHeader,
    SheetTitle,
} from '@/components/ui/sheet';
import { Button } from '@/components/ui/button';
import { Loader2 } from 'lucide-vue-next';
import Errors from './Errors.vue';

interface Tab {
    title: string;
    disabled?: boolean;
}

interface Props {
    modelValue?: boolean;
    title?: string;
    tabs?: Tab[];
    position?: 'left' | 'right' | 'top' | 'bottom';
    width?: string;
    loading?: boolean;
    errors?: Record<string, string>;
}

const props = withDefaults(defineProps<Props>(), {
    modelValue: false,
    title: '',
    tabs: () => [],
    position: 'right',
    width: '400px',
    loading: false,
    errors: () => ({}),
});

const emit = defineEmits<{
    'update:modelValue': [value: boolean];
    'submit': [];
}>();

const activeTab = ref(0);

const isOpen = computed({
    get: () => props.modelValue,
    set: (val) => emit('update:modelValue', val),
});

const widthStyle = computed(() => {
    if (props.position === 'left' || props.position === 'right') {
        return { width: props.width, maxWidth: '100%' };
    }
    return {};
});

const hasErrors = computed(() => Object.keys(props.errors).length > 0);
</script>
