<template>
    <Sheet v-model:open="isOpen">
        <SheetContent :side="position" class="flex flex-col !max-w-none p-0 overflow-hidden" :style="widthStyle">
            <!-- Header -->
            <div class="px-6 pt-4" :class="tabs?.length ? 'pb-2' : 'pb-4 border-b border-border'">
                <SheetTitle>{{ title }}</SheetTitle>
            </div>

            <!-- With Tabs -->
            <template v-if="tabs?.length">
                <Tabs v-model="activeTabValue" variant="underline" class="flex flex-col flex-1 min-h-0">
                    <div class="border-b border-border">
                        <TabsList class="px-6 border-b-0">
                            <TabsTrigger
                                v-for="(tab, index) in tabs"
                                :key="index"
                                :value="String(index)"
                                :disabled="tab.disabled"
                            >
                                {{ tab.title }}
                            </TabsTrigger>
                        </TabsList>
                    </div>

                    <!-- Content -->
                    <div class="flex-1 min-h-0 overflow-y-auto px-6 py-4">
                        <TabsContent value="0" class="mt-0">
                            <slot />
                        </TabsContent>
                        <TabsContent
                            v-for="(tab, index) in tabs.slice(1)"
                            :key="index"
                            :value="String(index + 1)"
                            class="mt-0"
                        >
                            <slot :name="`tab-${index + 1}`" />
                        </TabsContent>
                    </div>
                </Tabs>
            </template>

            <!-- Without Tabs -->
            <template v-else>
                <div class="flex-1 min-h-0 overflow-y-auto px-6 py-4">
                    <slot />
                </div>
            </template>

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
    SheetTitle,
} from '@/components/ui/sheet';
import { Tabs, TabsList, TabsTrigger, TabsContent } from '@/components/ui/tabs';
import { Button } from '@/components/ui/button';
import { Loader2 } from 'lucide-vue-next';
import { Errors } from '@/components/ui/form';

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

const activeTabValue = ref('0');

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
